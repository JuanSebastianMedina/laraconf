<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\ToggleColumn::make('new_talk'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color( function($state) {
                        return $state->getColor();
                    }),
                Tables\Columns\IconColumn::make('length')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
