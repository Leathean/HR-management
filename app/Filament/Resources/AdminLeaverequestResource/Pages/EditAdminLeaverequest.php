<?php

namespace App\Filament\Resources\AdminLeaverequestResource\Pages;

use App\Filament\Resources\AdminLeaverequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminLeaverequest extends EditRecord
{
    protected static string $resource = AdminLeaverequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
