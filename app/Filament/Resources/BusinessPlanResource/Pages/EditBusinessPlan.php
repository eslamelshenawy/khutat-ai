<?php

namespace App\Filament\Resources\BusinessPlanResource\Pages;

use App\Filament\Resources\BusinessPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessPlan extends EditRecord
{
    protected static string $resource = BusinessPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
