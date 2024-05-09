<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Filament\Resources\AssetResource\RelationManagers\NetworksRelationManager;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $modelLabel = 'Crypto Assets';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([

                        TextInput::make('name')
                        ->required(),

                        TextInput::make('abbr')
                        ->required(),

                        TextInput::make('rate')
                        ->label('dollar rate'),

                        Select::make('automatic_price')
                        ->options([
                            1 => 'yes',
                            0 => 'No'
                        ]),

                        FileUpload::make('logo')
                        ->image()
                        ->directory('assets-logo')
                        ->imageEditor()
                        ->columnSpanFull()

                    ])->columns(2)
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                ImageColumn::make('logo'),

                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('abbr'),

                TextColumn::make('rate'),

                ToggleColumn::make('automatic_price')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    BulkAction::make('make_price_automatic')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function(Collection $records){
                        $records->each(function($asset){
                            $asset->automatic_price = 1;
                            $asset->save();
                        });
                    })
                    ->deselectRecordsAfterCompletion(),

                    BulkAction::make('make_price_manual')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function(Collection $records){
                        $records->each(function($asset){
                            $asset->automatic_price = 0;
                            $asset->save();
                        });
                    })
                    ->deselectRecordsAfterCompletion()  
                ]),
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            NetworksRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
