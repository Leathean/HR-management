<?php

namespace App\Filament\Resources\DeductionAttendanceRuleResource\Pages;

use App\Filament\Resources\DeductionAttendanceRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeductionAttendanceRule extends ViewRecord
{
    protected static string $resource = DeductionAttendanceRuleResource::class;

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
