<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'الإشعارات';

    protected static ?string $modelLabel = 'إشعار';

    protected static ?string $pluralModelLabel = 'الإشعارات';

    protected static ?string $navigationGroup = 'إدارة النظام';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الإشعار')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('المستخدم')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('message')
                            ->label('الرسالة')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('النوع')
                            ->options([
                                'info' => 'معلومة',
                                'success' => 'نجاح',
                                'warning' => 'تحذير',
                                'error' => 'خطأ',
                            ])
                            ->required()
                            ->default('info'),

                        Forms\Components\TextInput::make('action_url')
                            ->label('رابط الإجراء')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\Toggle::make('is_read')
                            ->label('مقروء')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(50)
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('message')
                    ->label('الرسالة')
                    ->limit(100)
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('النوع')
                    ->colors([
                        'primary' => 'info',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'error',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => 'معلومة',
                        'success' => 'نجاح',
                        'warning' => 'تحذير',
                        'error' => 'خطأ',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('مقروء')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('Y-m-d H:i')),

                Tables\Columns\TextColumn::make('read_at')
                    ->label('تاريخ القراءة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'info' => 'معلومة',
                        'success' => 'نجاح',
                        'warning' => 'تحذير',
                        'error' => 'خطأ',
                    ]),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('حالة القراءة')
                    ->placeholder('الكل')
                    ->trueLabel('مقروءة فقط')
                    ->falseLabel('غير مقروءة فقط'),
                Tables\Filters\Filter::make('created_at')
                    ->label('آخر 7 أيام')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_read')
                    ->label('تعليم كمقروء')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => true, 'read_at' => now()])),
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_all_as_read')
                        ->label('تعليم الكل كمقروء')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_read' => true, 'read_at' => now()]))),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'view' => Pages\ViewNotification::route('/{record}'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_read', false)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
