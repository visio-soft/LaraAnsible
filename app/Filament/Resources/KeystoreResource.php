<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeystoreResource\Pages;
use App\Filament\Resources\KeystoreResource\RelationManagers;
use App\Models\Keystore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KeystoreResource extends Resource
{
    protected static ?string $model = Keystore::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Ansible Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Keystore Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'ssh' => 'SSH Key',
                                'password' => 'Password',
                            ])
                            ->default('ssh')
                            ->required()
                            ->live(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('SSH Key Configuration')
                    ->schema([
                        Forms\Components\Textarea::make('private_key')
                            ->label('Private Key')
                            ->rows(10)
                            ->columnSpanFull()
                            ->password()
                            ->revealable()
                            ->helperText('Paste your SSH private key here'),
                        Forms\Components\Textarea::make('public_key')
                            ->label('Public Key')
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Optional: Paste your SSH public key here'),
                        Forms\Components\TextInput::make('passphrase')
                            ->password()
                            ->revealable()
                            ->helperText('If your key is encrypted with a passphrase'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('type') === 'ssh'),
                Forms\Components\Section::make('Password Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (Forms\Get $get) => $get('type') === 'password'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('type') === 'password'),
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
                        'primary' => 'ssh',
                        'success' => 'password',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('inventories_count')
                    ->counts('inventories')
                    ->label('In Use')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'ssh' => 'SSH Key',
                        'password' => 'Password',
                    ]),
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
            'index' => Pages\ListKeystores::route('/'),
            'create' => Pages\CreateKeystore::route('/create'),
            'edit' => Pages\EditKeystore::route('/{record}/edit'),
        ];
    }
}
