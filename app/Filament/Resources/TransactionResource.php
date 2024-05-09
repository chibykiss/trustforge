<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()->schema([
                    Section::make()->schema([

                        Select::make('user_id')
                        ->label('User')
                        ->options(User::whereNotNull('phrase')->pluck('name', 'id'))
                        ->required()
                        ->columnSpanFull()
                        ->native(false),
                        //->relationship('user','name')

                        TextInput::make('coin_amount')
                        ->label('Amount')
                        ->placeholder('amount in coin')
                        ->required(),

                        Select::make('type')
                        ->required()
                        ->options([
                            'swap' => 'Swap',
                            'send' => 'Send',
                            'recieve' => 'Recieve'
                        ]),

                        Select::make('status')
                        ->required()
                        ->options([
                            'pending' => 'Pending',
                            'completed' => 'Completed',
                        ]),

                        DateTimePicker::make('created_at')

                       
                    ])->columns(2)
                ])->columnSpanFull(),

                Group::make()->schema([
                    Section::make('From')->schema([
                     

                        Select::make('from_asset_id')
                        ->label('From Asset')
                        ->relationship('fromAsset','name')
                        ->required(),

                        Select::make('from_network_id')
                        ->label('From Network')
                        ->relationship('fromNetwork','name')
                        ->required(),

                        TextInput::make('from_address')
                        ->label('Wallet Address')
                        ->required()
                        ->columnSpanFull()


                    ])->columns(2),
                ]),

                Group::make()->schema([

                    Section::make('To')->schema([

                        Select::make('to_asset_id')
                        ->label('To Asset')
                        ->relationship('toAsset','name')
                        ->required(),

                        Select::make('to_network_id')
                        ->label('To Network')
                        ->relationship('toNetwork','name')
                        ->required(),

                        TextInput::make('to_address')
                        ->label('Wallet Address')
                        ->required()
                        ->columnSpanFull()
                   
                    ])->columns(2)
                ])



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->label('User'),

                ImageColumn::make('fromAsset.logo')
                ->label('From'),

                // TextColumn::make('coin_amount')
                // ->description(fn (Transaction $record): string => $record->dollar_amount),

                TextColumn::make('coin_amount')
                ->label('From Amount')
                ->formatStateUsing(function(string $state, Transaction $record){
                    return $record->coin_amount.' $'.$record->dollar_amount;
                })
                ->listWithLineBreaks()
                ->wrap()
                ->lineClamp(2),


                ImageColumn::make('toAsset.logo')
                ->label('To'),

                TextColumn::make('to_amount')
                ->label('To Amount')
                ->formatStateUsing(function(string $state, Transaction $record){
                    return $record->to_amount.' $'.$record->to_dollar;
                })
                ->listWithLineBreaks()
                ->wrap()
                ->lineClamp(2),

                TextColumn::make('fee_coin')
                ->label('Fee')
                ->formatStateUsing(function(string $state, Transaction $record){
                    return $record->fee_coin.' $'.$record->fee_dollar;
                })
                ->listWithLineBreaks()
                ->wrap()
                ->lineClamp(2),

                TextColumn::make('type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'swap' => 'gray',
                    'recieve' => 'success',
                    'send' => 'danger',
                })
                ->sortable(),


                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'completed' => 'success',
                })
                ->sortable(),

                TextColumn::make('created_at')

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
