<?php

namespace App\Filament\Resources\PayslipResource\Pages;

use App\Filament\Resources\PayslipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayslip extends CreateRecord
{
    protected static string $resource = PayslipResource::class;
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
