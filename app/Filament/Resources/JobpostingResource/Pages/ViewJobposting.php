<?php

namespace App\Filament\Resources\JobpostingResource\Pages;

use App\Filament\Resources\JobpostingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJobposting extends ViewRecord
{
    protected static string $resource = JobpostingResource::class;
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
