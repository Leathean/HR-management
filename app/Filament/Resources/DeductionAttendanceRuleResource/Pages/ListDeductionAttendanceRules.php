<?php

namespace App\Filament\Resources\DeductionAttendanceRuleResource\Pages;

use App\Filament\Resources\DeductionAttendanceRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeductionAttendanceRules extends ListRecords
{
    protected static string $resource = DeductionAttendanceRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
