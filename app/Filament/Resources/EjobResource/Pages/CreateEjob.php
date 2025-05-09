<?php

namespace App\Filament\Resources\EjobResource\Pages;

use App\Filament\Resources\EjobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEjob extends CreateRecord
{
    protected static string $resource = EjobResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
