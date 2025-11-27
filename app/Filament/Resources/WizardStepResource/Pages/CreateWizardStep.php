<?php

namespace App\Filament\Resources\WizardStepResource\Pages;

use App\Filament\Resources\WizardStepResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWizardStep extends CreateRecord
{
    protected static string $resource = WizardStepResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
