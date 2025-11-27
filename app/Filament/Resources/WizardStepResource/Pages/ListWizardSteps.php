<?php

namespace App\Filament\Resources\WizardStepResource\Pages;

use App\Filament\Resources\WizardStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWizardSteps extends ListRecords
{
    protected static string $resource = WizardStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
