<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Talk;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\TalkResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TalkResource\RelationManagers;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Titulo')
                    ->maxLength(255),
                Forms\Components\Textarea::make('abstract')
                    ->label('Resumen')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->label('Ponente')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->live()
                    ->enum( TalkStatus::class)
                    ->options( TalkStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction( function ($action) {
                return $action->button()->label('Filtrar');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titulo')
                    //->rules(['required', 'max:255'])
                    ->searchable()
                    //->wrap()
                    ->description( function (Talk $record){
                        return Str::of( $record->abstract)->limit(40);
                    }),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode( $record->speaker->name);
                    }),
                    //->avatar(),
                // Tables\Columns\TextColumn::make('abstract')->label('Resumen')->wrap(),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->label('Ponente')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('new_talk')
                    ->label('Charla nueva'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable()
                    ->color( function($state) {
                        return $state->getColor();
                    }),
                Tables\Columns\IconColumn::make('length')
                    ->label('Duración')
                    ->icon( function ($state) {
                        return match($state){
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    })
                // Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_avatar')
                    ->label('Mostrar solamente los que tienen avatar')
                    ->toggle()
                    ->query(function ($query) {
                        return $query->whereHas('speaker', function (Builder $query) {
                            $query->whereNotNull('avatar');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->visible( function ($record) {
                            return $record->status === (TalkStatus::SUBMITTED);
                        })
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action( function (Talk $record) {
                            $record->approve();
                        })->after( function() {
                            Notification::make()->success()->title('La charla fue aprobada')
                                ->duration(2000)
                                ->body('El conferencista ha sido notificado del cambio de estado de la  charla')
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->visible( function ($record) {
                            return $record->status === (TalkStatus::SUBMITTED);
                        })
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action( function (Talk $record) {
                            $record->reject();
                        })->after( function() {
                            Notification::make()->danger()->title('La charla fue rechazada')
                                ->duration(2000)
                                ->body('El conferencista ha sido notificado')
                                ->send();
                        })
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        //->visible( function ($record) { return $record->status === (TalkStatus::SUBMITTED); })
                        ->color('success')
                        ->label('Aprobar seleccionados')
                        ->action( function( Collection $records) {
                            $records->each->approve();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar seleccionados'),
                //]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('Export')
                    ->tooltip('Mandará todo a la chingada, cuidado')
                    ->action( function($livewire) {
                        // dd($livewire->getFilteredTableQuery()->count());
                        dd("Exportando las charlas seleccionadas");
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
