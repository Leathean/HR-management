<?php

namespace App\Filament\Resources\EmployeebenefitResource\Pages;

use App\Filament\Resources\EmployeebenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeebenefits extends ListRecords
{
    protected static string $resource = EmployeebenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
