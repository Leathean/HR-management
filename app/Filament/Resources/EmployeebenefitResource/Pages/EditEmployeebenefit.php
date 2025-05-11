<?php

namespace App\Filament\Resources\EmployeebenefitResource\Pages;

use App\Filament\Resources\EmployeebenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeebenefit extends EditRecord
{
    protected static string $resource = EmployeebenefitResource::class;

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
