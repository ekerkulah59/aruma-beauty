<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Service Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Hair Cut, Hair Styling, Hair Braiding')
                            ->columnSpan(2),

                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->required()
                            ->options([
                                'Hair Cut' => 'Hair Cut',
                                'Hair Styling' => 'Hair Styling',
                                'Hair Braiding' => 'Hair Braiding',
                                'Hair Coloring' => 'Hair Coloring',
                                'Hair Treatment' => 'Hair Treatment',
                                'Hair Extensions' => 'Hair Extensions',
                                'Special Occasion' => 'Special Occasion',
                                'Consultation' => 'Consultation',
                            ])
                            ->searchable()
                            ->placeholder('Select service category')
                            ->native(false),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->helperText('Enter the service price in USD'),

                        Forms\Components\TextInput::make('duration')
                            ->label('Duration')
                            ->required()
                            ->numeric()
                            ->suffix('minutes')
                            ->minValue(1)
                            ->maxValue(600)
                            ->placeholder('60')
                            ->helperText('Duration of the service in minutes'),

                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Toggle to enable/disable this service for booking')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Service Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->rows(4)
                            ->placeholder('Detailed description of the service, including what is included, techniques used, and any special notes...')
                            ->columnSpanFull()
                            ->helperText('Provide a comprehensive description that clients will see when booking'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Service Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->wrap(),

                BadgeColumn::make('category')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->colors([
                        'primary' => 'Hair Cut',
                        'success' => 'Hair Styling',
                        'warning' => 'Hair Braiding',
                        'danger' => 'Hair Coloring',
                        'info' => 'Hair Treatment',
                        'secondary' => 'Hair Extensions',
                        'purple' => 'Special Occasion',
                        'gray' => 'Consultation',
                    ]),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->weight('medium')
                    ->color('success'),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->sortable()
                    ->suffix(' min')
                    ->alignCenter()
                    ->color('info'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap()
                    ->toggleable(),

                BadgeColumn::make('active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('bookings_count')
                    ->label('Total Bookings')
                    ->counts('bookings')
                    ->sortable()
                    ->alignCenter()
                    ->color('warning')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Hair Cut' => 'Hair Cut',
                        'Hair Styling' => 'Hair Styling',
                        'Hair Braiding' => 'Hair Braiding',
                        'Hair Coloring' => 'Hair Coloring',
                        'Hair Treatment' => 'Hair Treatment',
                        'Hair Extensions' => 'Hair Extensions',
                        'Special Occasion' => 'Special Occasion',
                        'Consultation' => 'Consultation',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Min Price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Max Price')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),

                Tables\Filters\Filter::make('duration_range')
                    ->form([
                        Forms\Components\TextInput::make('duration_from')
                            ->label('Min Duration (minutes)')
                            ->numeric(),
                        Forms\Components\TextInput::make('duration_to')
                            ->label('Max Duration (minutes)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['duration_from'],
                                fn (Builder $query, $duration): Builder => $query->where('duration', '>=', $duration),
                            )
                            ->when(
                                $data['duration_to'],
                                fn (Builder $query, $duration): Builder => $query->where('duration', '<=', $duration),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('active')
                    ->label('Status')
                    ->placeholder('All Services')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),
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
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
