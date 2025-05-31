<?php

namespace App\Filament\Resources\DeductionAttendanceRuleResource\Pages;

use App\Filament\Resources\DeductionAttendanceRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeductionAttendanceRule extends CreateRecord
{
    protected static string $resource = DeductionAttendanceRuleResource::class;

            protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
