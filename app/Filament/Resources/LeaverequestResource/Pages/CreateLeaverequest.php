<?php

namespace App\Filament\Resources\LeaverequestResource\Pages;

use App\Filament\Resources\LeaverequestResource;
use Filament\Actions;
use App\Models\Leaverequest;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
class CreateLeaverequest extends CreateRecord
{
    protected static string $resource = LeaverequestResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Leaverequest
{

    Log::info('Create form submitted with data:', $data);


    return Leaverequest::create($data);
}

}
