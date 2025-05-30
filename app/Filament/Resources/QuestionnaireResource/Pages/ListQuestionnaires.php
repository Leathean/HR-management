<?php

namespace App\Filament\Resources\QuestionnaireResource\Pages;

use App\Filament\Resources\QuestionnaireResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionnaires extends ListRecords
{
    protected static string $resource = QuestionnaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
