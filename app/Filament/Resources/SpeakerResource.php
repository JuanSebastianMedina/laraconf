<?php

namespace App\Filament\Resources;

use App\Enums\TalkStatus;
use Filament\Forms;
use Filament\Tables;
use App\Models\Speaker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\SpeakerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SpeakerResource\RelationManagers;

class SpeakerResource extends Resource
{
    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Speaker::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_handle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make('Informacón personal')
                ->columns(3)
                ->schema([
                    ImageEntry::make('avatar')
                        ->circular(),
                    Group::make()
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label('Nombre'),
                            TextEntry::make('email'),
                            TextEntry::make('twitter_handle')
                                ->label('Twitter')
                                ->getStateUsing(function ($record) {
                                    return '@' . $record->twitter_handle;
                                })
                                ->url(function ($record) {
                                    return 'https://x.com/'.$record->twitter_handle;
                                }),
                            TextEntry::make('has_spoken')
                                ->getStateUsing( function ($record) {
                                    return $record->talks()->where('status', TalkStatus::APPROVED)->count() > 0 ? 'Experimentado' : 'Novato';
                                })
                                ->badge()
                                ->color( function($state){
                                    if ($state === 'Experimentado') {
                                        return 'success';
                                    }
                                    return 'primary';
                                })
                        ]),
                ]),
            Section::make('Información adicional')
                ->schema([
                    TextEntry::make('bio')
                        ->extraAttributes(['class' => 'prose dark:prose-invert'])
                        ->html(),
                    TextEntry::make('qualifications')
                        ->listWithLineBreaks()
                        ->bulleted(),
                ])
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //Establish relationships with Talks
            RelationManagers\TalksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeaker::route('/create'),
            // 'edit' => Pages\EditSpeaker::route('/{record}/edit'),
            'view' => Pages\ViewSpeaker::route('/{record}'),
        ];
    }
}
