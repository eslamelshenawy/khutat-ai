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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_type'),
                Tables\Columns\TextColumn::make('industry_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ai_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_analyzed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('language')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\IconColumn::make('allow_comments')
                    ->boolean(),
                Tables\Columns\TextColumn::make('version')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent_plan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
