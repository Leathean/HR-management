<?php

namespace App\Filament\Resources\EmployeebenefitResource\Pages;

use App\Filament\Resources\EmployeebenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeebenefit extends CreateRecord
{
    protected static string $resource = EmployeebenefitResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
