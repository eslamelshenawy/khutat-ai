<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WizardStepResource\Pages;
use App\Filament\Resources\WizardStepResource\RelationManagers\QuestionsRelationManager;
use App\Models\WizardStep;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use LaraZeus\Bolt\Models\Form as BoltForm;

class WizardStepResource extends Resource
{
    protected static ?string $model = WizardStep::class;
    protected static ?string $navigationIcon = "heroicon-o-clipboard-document-list";
    protected static ?string $navigationLabel = "خطوات الـ Wizard";
    protected static ?string $modelLabel = "خطوة";
    protected static ?string $pluralModelLabel = "خطوات الـ Wizard";
    protected static ?string $navigationGroup = "إدارة النظام";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("معلومات الخطوة")->schema([
                Forms\Components\TextInput::make("title")->label("عنوان الخطوة")->required()->maxLength(255),
                Forms\Components\Textarea::make("description")->label("وصف الخطوة")->rows(3),
                Forms\Components\TextInput::make("icon")->label("أيقونة"),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make("order")->label("الترتيب")->numeric()->default(0),
                    Forms\Components\Toggle::make("is_active")->label("نشط")->default(true),
                ]),
            ]),
            Forms\Components\Section::make("ربط Bolt Form")->description("اختر نموذج Bolt لهذه الخطوة")->schema([
                Forms\Components\Select::make("bolt_form_id")->label("نموذج Bolt")->options(fn() => BoltForm::where("is_active", true)->pluck("name", "id")->toArray())->searchable()->preload()->placeholder("اختر نموذج Bolt"),
            ]),
            Forms\Components\Section::make("إعدادات AI")->schema([
                Forms\Components\Toggle::make("enable_ai_suggestions")->label("تفعيل اقتراحات AI")->default(true),
                Forms\Components\Textarea::make("ai_suggestion_prompt")->label("برومبت AI مخصص")->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make("order")->label("الترتيب")->sortable()->badge()->color("primary"),
            Tables\Columns\TextColumn::make("icon")->label("الأيقونة"),
            Tables\Columns\TextColumn::make("title")->label("العنوان")->searchable()->weight("bold"),
            Tables\Columns\TextColumn::make("boltForm.name")->label("Bolt Form")->badge()->color("info")->placeholder("بدون"),
            Tables\Columns\TextColumn::make("questions_count")->label("عدد الأسئلة")->counts("questions")->badge()->color("success"),
            Tables\Columns\IconColumn::make("enable_ai_suggestions")->label("AI")->boolean()->trueIcon("heroicon-o-sparkles"),
            Tables\Columns\IconColumn::make("is_active")->label("نشط")->boolean(),
        ])->filters([
            Tables\Filters\TernaryFilter::make("is_active")->label("نشط"),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ])->defaultSort("order", "asc")->reorderable("order");
    }

    public static function getRelations(): array
    {
        return [QuestionsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListWizardSteps::route("/"),
            "create" => Pages\CreateWizardStep::route("/create"),
            "edit" => Pages\EditWizardStep::route("/{record}/edit"),
        ];
    }
}
