<?php

namespace App\Filament\Resources\JobpostingResource\Pages;

use App\Filament\Resources\JobpostingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobposting extends EditRecord
{
    protected static string $resource = JobpostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
