<?php

namespace App\Filament\Resources\EjobResource\Pages;

use App\Filament\Resources\EjobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEjobs extends ListRecords
{
    protected static string $resource = EjobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
