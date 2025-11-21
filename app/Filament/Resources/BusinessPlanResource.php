<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessPlanResource\Pages;
use App\Filament\Resources\BusinessPlanResource\RelationManagers;
use App\Models\BusinessPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessPlanResource extends Resource
{
    protected static ?string $model = BusinessPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'خطط الأعمال';

    protected static ?string $modelLabel = 'خطة عمل';

    protected static ?string $pluralModelLabel = 'خطط الأعمال';

    protected static ?string $navigationGroup = 'إدارة النظام';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('template_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('project_type')
                    ->required(),
                Forms\Components\TextInput::make('industry_type')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('completion_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('ai_score')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('ai_feedback')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('last_analyzed_at'),
                Forms\Components\TextInput::make('company_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('company_logo')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('vision')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('mission')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('language')
                    ->required()
                    ->maxLength(5)
                    ->default('ar'),
                Forms\Components\Toggle::make('is_public')
                    ->required(),
                Forms\Components\Toggle::make('allow_comments')
                    ->required(),
                Forms\Components\TextInput::make('version')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('parent_plan_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\DateTimePicker::make('published_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'warning' => 'draft',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'archived' => 'مؤرشف',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('نسبة الإنجاز')
                    ->suffix('%')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('chapters_count')
                    ->label('عدد الفصول')
                    ->counts('chapters')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('عام')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'archived' => 'مؤرشف',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('wizard')
                    ->label('المعالج')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn (BusinessPlan $record): string => route('wizard.steps', ['businessPlan' => $record->id])),

                Tables\Actions\ViewAction::make()
                    ->label('عرض'),

                Tables\Actions\EditAction::make()
                    ->label('تعديل'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListBusinessPlans::route('/'),
            'create' => Pages\CreateBusinessPlan::route('/create'),
            'edit' => Pages\EditBusinessPlan::route('/{record}/edit'),
        ];
    }
}
