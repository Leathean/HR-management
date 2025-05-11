<?php

namespace App\Filament\Resources\QuestionnaireResource\Pages;

use App\Filament\Resources\QuestionnaireResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionnaire extends ViewRecord
{
    protected static string $resource = QuestionnaireResource::class;

 protected function getHeaderActions(): array
    {
        return [
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
