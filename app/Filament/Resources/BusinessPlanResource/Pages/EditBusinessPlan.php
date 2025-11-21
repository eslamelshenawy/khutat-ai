<?php

namespace App\Filament\Resources\BusinessPlanResource\Pages;

use App\Filament\Resources\BusinessPlanResource;
use App\Services\ExportService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class EditBusinessPlan extends EditRecord
{
    protected static string $resource = BusinessPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('تصدير PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('business-plans.export-pdf', $this->record)),
            Actions\Action::make('exportWord')
                ->label('تصدير Word')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(fn () => route('business-plans.export-word', $this->record)),
            Actions\Action::make('exportExcel')
                ->label('تصدير Excel')
                ->icon('heroicon-o-table-cells')
                ->color('warning')
                ->url(fn () => route('business-plans.export-excel', $this->record)),
            Actions\DeleteAction::make(),
        ];
    }
}
