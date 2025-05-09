<?php

namespace App\Filament\Resources\EjobResource\Pages;

use App\Filament\Resources\EjobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEjob extends EditRecord
{
    protected static string $resource = EjobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
