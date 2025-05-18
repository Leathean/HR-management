<?php

namespace App\Filament\Resources\JobpostingResource\Pages;

use App\Filament\Resources\JobpostingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobpostings extends ListRecords
{
    protected static string $resource = JobpostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
