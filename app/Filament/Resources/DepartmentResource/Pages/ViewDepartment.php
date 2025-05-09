<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Edit action button
            Actions\EditAction::make(),

            // Exit button action
            Actions\Action::make('exit')
                ->label('Exit')
                ->url($this->getRedirectUrl())
                ->color('danger'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // This will redirect to the index page of the resource
        return $this->getResource()::getUrl('index');
    }
}
