<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskTemplateResource\Pages;
use App\Models\TaskTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskTemplateResource extends Resource
{
    protected static ?string $model = TaskTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Ansible Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'playbook' => 'Playbook',
                                'adhoc' => 'Ad-hoc Command',
                            ])
                            ->default('playbook')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Playbook Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('playbook_path')
                            ->label('Playbook Path')
                            ->maxLength(255)
                            ->placeholder('/path/to/playbook.yml')
                            ->helperText('Absolute path to the playbook file on the server'),
                        Forms\Components\Textarea::make('playbook_content')
                            ->label('Playbook Content')
                            ->rows(10)
                            ->columnSpanFull()
                            ->helperText('Or paste the playbook content directly here'),
                    ]),
                // Removed Extra Variables section per request
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'playbook',
                        'success' => 'adhoc',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('playbook_path')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deployments_count')
                    ->counts('deployments')
                    ->label('Deployments')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'playbook' => 'Playbook',
                        'adhoc' => 'Ad-hoc',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTaskTemplates::route('/'),
            'create' => Pages\CreateTaskTemplate::route('/create'),
            'edit' => Pages\EditTaskTemplate::route('/{record}/edit'),
        ];
    }
}
