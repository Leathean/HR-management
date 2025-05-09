<?php

namespace App\Filament\Resources\LeaverequestResource\Pages;

use App\Filament\Resources\LeaverequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
class ViewLeaverequest extends ViewRecord
{
    protected static string $resource = LeaverequestResource::class;
    protected static ?string $title = 'Leave Request Details';



    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('exit')
                ->label('Exit')
                ->url($this->getRedirectUrl())
                ->color('danger'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
