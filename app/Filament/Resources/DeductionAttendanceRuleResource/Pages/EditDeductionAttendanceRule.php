<?php

namespace App\Filament\Resources\DeductionAttendanceRuleResource\Pages;

use App\Filament\Resources\DeductionAttendanceRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeductionAttendanceRule extends EditRecord
{
    protected static string $resource = DeductionAttendanceRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
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
