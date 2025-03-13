<?php

namespace App\Filament\Resources\TalkResource\Pages;

use Filament\Actions;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource;
use Filament\Resources\Pages\ListRecords;
// use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords\Tab;

class ListTalks extends ListRecords
{
    protected static string $resource = TalkResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Talks')
                ->label('Todas las charlas'),
            'submitted' => Tab::make('Submitted')
                ->label('Enviadas')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', TalkStatus::SUBMITTED);
                }),
            'approved' => Tab::make('Approved')
                ->label('Aprobadas')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', TalkStatus::APPROVED);
                }),
            'rejected' => Tab::make('Rejected')
                ->label('Rechazadas')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', TalkStatus::REJECTED);
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
