<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeploymentResource\Pages;
use App\Filament\Resources\DeploymentResource\RelationManagers;
use App\Jobs\ExecuteAnsibleDeployment;
use App\Models\Deployment;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationGroup = 'Ansible Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Deployment Configuration')
                    ->schema([
                        Forms\Components\Select::make('task_template_id')
                            ->relationship('taskTemplate', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('environment_id')
                            ->relationship('environment', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('inventory_ids')
                            ->label('Servers')
                            ->multiple()
                            ->options(Inventory::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->helperText('Select the servers to run this deployment on'),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Execution Details')
                    ->schema([
                        Forms\Components\Placeholder::make('status')
                            ->content(fn (?Deployment $record) => $record?->status ?? 'Not started'),
                        Forms\Components\Placeholder::make('started_at')
                            ->content(fn (?Deployment $record) => $record?->started_at?->diffForHumans() ?? 'N/A'),
                        Forms\Components\Placeholder::make('completed_at')
                            ->content(fn (?Deployment $record) => $record?->completed_at?->diffForHumans() ?? 'N/A'),
                        Forms\Components\Placeholder::make('exit_code')
                            ->content(fn (?Deployment $record) => $record?->exit_code ?? 'N/A'),
                    ])
                    ->columns(2)
                    ->hidden(fn (?Deployment $record) => $record === null),
                Forms\Components\Section::make('Console Output')
                    ->schema([
                        Forms\Components\Textarea::make('console_output')
                            ->label('')
                            ->rows(15)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn (?Deployment $record) => $record === null || empty($record->console_output)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('taskTemplate.name')
                    ->label('Task')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('environment.name')
                    ->label('Environment')
                    ->searchable()
                    ->sortable()
                    ->placeholder('None'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'running',
                        'success' => 'success',
                        'danger' => 'failed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('task_template_id')
                    ->relationship('taskTemplate', 'name')
                    ->label('Task Template'),
            ])
            ->actions([
                Tables\Actions\Action::make('execute')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (Deployment $record) {
                        if ($record->status === 'pending') {
                            ExecuteAnsibleDeployment::dispatch($record);
                            $record->update(['status' => 'running']);
                        }
                    })
                    ->visible(fn (Deployment $record) => $record->status === 'pending')
                    ->requiresConfirmation(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDeployments::route('/'),
            'create' => Pages\CreateDeployment::route('/create'),
            'edit' => Pages\EditDeployment::route('/{record}/edit'),
        ];
    }
}
