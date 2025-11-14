<?php

namespace VisioSoft\LaraAnsible\Filament\Resources;

use VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource\Pages;
use VisioSoft\LaraAnsible\Jobs\ExecuteAnsibleDeployment;
use VisioSoft\LaraAnsible\Models\Deployment;
use VisioSoft\LaraAnsible\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

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
                            ->preload()
                            ->disabled(fn ($livewire) => $livewire instanceof Pages\EditDeployment),
                        Forms\Components\CheckboxList::make('inventory_ids')
                            ->label('Servers')
                            ->options(function () {
                                $options = ['all' => 'All Servers'];
                                $inventories = Inventory::where('is_active', true)->pluck('name', 'id')->toArray();

                                return $options + $inventories;
                            })
                            ->columns(2)
                            ->required()
                            ->disabled(fn ($livewire) => $livewire instanceof Pages\EditDeployment)
                            ->afterStateUpdated(function ($state, callable $set) {
                                // If 'all' is selected, deselect individual servers
                                if (is_array($state) && in_array('all', $state)) {
                                    $set('inventory_ids', ['all']);
                                }
                            })
                            ->reactive(),
                    ])
                    ->columns(2)
                    ->compact(),
                Forms\Components\Section::make('Execution Details')
                    ->schema([
                        Forms\Components\Placeholder::make('status')
                            ->label('Status')
                            ->content(function (?Deployment $record) {
                                if (! $record) {
                                    return new HtmlString('<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-500/20 text-gray-700 ring-1 ring-gray-500">N/A</span>');
                                }

                                $status = $record->status ?? 'pending';
                                $styles = [
                                    'pending' => 'bg-gray-500/20 text-gray-700 ring-gray-500',
                                    'running' => 'bg-blue-500/20 text-blue-700 ring-blue-500 animate-pulse',
                                    'success' => 'bg-green-500/20 text-green-700 ring-green-500',
                                    'failed' => 'bg-red-500/20 text-red-700 ring-red-500',
                                ];

                                $class = $styles[$status] ?? 'bg-gray-500/20 text-gray-700 ring-gray-500';
                                $label = ucfirst($status);

                                return new HtmlString("<span class=\"inline-flex items-center px-2 py-1 rounded text-xs font-medium ring-1 {$class}\">{$label}</span>");
                            }),
                        Forms\Components\Placeholder::make('started_at')
                            ->content(fn (?Deployment $record) => $record?->started_at?->diffForHumans() ?? 'N/A'),
                        Forms\Components\Placeholder::make('duration')
                            ->label('Duration')
                            ->content(function (?Deployment $record) {
                                if (! $record?->started_at || ! $record?->completed_at) {
                                    return '—';
                                }
                                $seconds = $record->started_at->diffInSeconds($record->completed_at);
                                $h = intdiv($seconds, 3600);
                                $m = intdiv($seconds % 3600, 60);
                                $s = $seconds % 60;
                                $parts = [];
                                if ($h) {
                                    $parts[] = $h.'h';
                                }
                                if ($m) {
                                    $parts[] = $m.'m';
                                }
                                $parts[] = $s.'s';

                                return implode(' ', $parts);
                            }),
                        Forms\Components\Placeholder::make('exit_code')
                            ->content(fn (?Deployment $record) => $record?->exit_code ?? 'N/A'),
                    ])
                    ->columns(4)
                    ->compact()
                    ->hidden(fn (?Deployment $record) => $record === null),
                Forms\Components\Section::make('Command Input')
                    ->schema([
                        Forms\Components\Textarea::make('command_input')
                            ->label('')
                            ->rows(10)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->compact()
                    ->hidden(fn (?Deployment $record) => $record === null || empty($record->command_input)),
                Forms\Components\Section::make('Command Output')
                    ->schema([
                        Forms\Components\Textarea::make('command_output')
                            ->label('')
                            ->rows(20)
                            ->disabled()
                            ->columnSpanFull()
                            ->live(onBlur: false),
                    ])
                    ->compact()
                    ->hidden(fn (?Deployment $record) => $record === null || empty($record->command_output)),
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
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->state(function (Deployment $record) {
                        if (! $record->started_at || ! $record->completed_at) {
                            return '—';
                        }
                        $seconds = $record->started_at->diffInSeconds($record->completed_at);
                        $h = intdiv($seconds, 3600);
                        $m = intdiv($seconds % 3600, 60);
                        $s = $seconds % 60;
                        $parts = [];
                        if ($h) {
                            $parts[] = $h.'h';
                        }
                        if ($m) {
                            $parts[] = $m.'m';
                        }
                        $parts[] = $s.'s';

                        return implode(' ', $parts);
                    })
                    ->sortable(),
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
