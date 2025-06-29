<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayroll extends ViewRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
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
