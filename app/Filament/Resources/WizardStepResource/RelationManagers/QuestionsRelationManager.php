<?php

namespace App\Filament\Resources\WizardStepResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'الأسئلة';

    protected static ?string $modelLabel = 'سؤال';

    protected static ?string $pluralModelLabel = 'الأسئلة';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات السؤال')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('نص السؤال')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: من هم الشركاء المفتاحيون لإنجاز منتجك؟'),

                        Forms\Components\Textarea::make('help_text')
                            ->label('نص المساعدة')
                            ->rows(2)
                            ->placeholder('نص توضيحي يساعد المستخدم على فهم السؤال'),

                        Forms\Components\TextInput::make('field_name')
                            ->label('اسم الحقل')
                            ->required()
                            ->placeholder('key_partners')
                            ->helperText('اسم فريد للحقل (بالإنجليزية، بدون مسافات)')
                            ->regex('/^[a-z_]+$/')
                            ->rule('alpha_dash'),
                    ]),

                Forms\Components\Section::make('نوع السؤال')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('نوع الحقل')
                            ->options([
                                'text' => 'نص قصير',
                                'textarea' => 'نص طويل',
                                'number' => 'رقم',
                                'date' => 'تاريخ',
                                'select' => 'قائمة منسدلة',
                                'radio' => 'اختيار واحد',
                                'checkbox' => 'اختيار متعدد',
                            ])
                            ->required()
                            ->default('text')
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                in_array($state, ['select', 'radio', 'checkbox'])
                                    ? null
                                    : $set('options', null)
                            ),

                        Forms\Components\KeyValue::make('options')
                            ->label('الخيارات')
                            ->keyLabel('القيمة')
                            ->valueLabel('النص المعروض')
                            ->addActionLabel('إضافة خيار')
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select', 'radio', 'checkbox']))
                            ->helperText('أضف الخيارات المتاحة للاختيار'),
                    ]),

                Forms\Components\Section::make('إعدادات إضافية')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Toggle::make('is_required')
                                    ->label('إجباري')
                                    ->default(false),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('نشط')
                                    ->default(true),
                            ]),

                        Forms\Components\TagsInput::make('validation_rules')
                            ->label('قواعد Validation إضافية')
                            ->placeholder('min:3, max:100')
                            ->helperText('قواعد Laravel validation (اختياري)'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('الترتيب')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('label')
                    ->label('السؤال')
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('field_name')
                    ->label('اسم الحقل')
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'text' => 'نص قصير',
                        'textarea' => 'نص طويل',
                        'number' => 'رقم',
                        'date' => 'تاريخ',
                        'select' => 'قائمة',
                        'radio' => 'اختيار واحد',
                        'checkbox' => 'اختيار متعدد',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'text', 'textarea' => 'info',
                        'number', 'date' => 'warning',
                        'select', 'radio', 'checkbox' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('إجباري')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع الحقل')
                    ->options([
                        'text' => 'نص قصير',
                        'textarea' => 'نص طويل',
                        'number' => 'رقم',
                        'date' => 'تاريخ',
                        'select' => 'قائمة منسدلة',
                        'radio' => 'اختيار واحد',
                        'checkbox' => 'اختيار متعدد',
                    ]),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('إجباري')
                    ->placeholder('الكل')
                    ->trueLabel('الإجبارية فقط')
                    ->falseLabel('الاختيارية فقط'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('نشط'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة سؤال')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order');
    }
}
