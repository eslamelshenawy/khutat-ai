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
                ->action(function () {
                    try {
                        $exportService = new ExportService();
                        $path = $exportService->exportToPDF($this->record);

                        Notification::make()
                            ->title('تم التصدير بنجاح')
                            ->success()
                            ->send();

                        return response()->download(storage_path('app/' . $path));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطأ في التصدير')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('exportWord')
                ->label('تصدير Word')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(function () {
                    try {
                        $exportService = new ExportService();
                        $path = $exportService->exportToWord($this->record);

                        Notification::make()
                            ->title('تم التصدير بنجاح')
                            ->success()
                            ->send();

                        return response()->download(storage_path('app/' . $path));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطأ في التصدير')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('exportExcel')
                ->label('تصدير Excel')
                ->icon('heroicon-o-table-cells')
                ->color('warning')
                ->action(function () {
                    try {
                        $exportService = new ExportService();
                        $path = $exportService->exportToExcel($this->record);

                        Notification::make()
                            ->title('تم التصدير بنجاح')
                            ->success()
                            ->send();

                        return response()->download(storage_path('app/' . $path));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطأ في التصدير')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
