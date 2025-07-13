<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Bookings';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter client full name'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('client@example.com'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('(555) 123-4567'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a service')
                            ->helperText('Choose the service for this appointment')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $service = \App\Models\Service::find($state);
                                    if ($service) {
                                        // Auto-populate notes with service info if needed
                                        $currentNotes = $get('notes') ?? '';
                                        if (empty($currentNotes)) {
                                            $set('notes', "Service: {$service->name} - Duration: {$service->duration} minutes - Price: \${$service->price}");
                                        }
                                    }
                                }
                            }),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label('Appointment Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('M j, Y')
                            ->minDate(now())
                            ->placeholder('Select date'),

                        Forms\Components\TimePicker::make('booking_time')
                            ->label('Appointment Time')
                            ->required()
                            ->native(false)
                            ->displayFormat('g:i A')
                            ->placeholder('Select time')
                            ->seconds(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Booking::getStatusOptions())
                            ->required()
                            ->default(Booking::STATUS_PENDING)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Any special requests or notes for this appointment...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Client Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable()
                    ->searchable()
                    ->weight('medium')
                    ->color('primary'),

                TextColumn::make('service.price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('booking_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('booking_time')
                    ->label('Time')
                    ->time('g:i A')
                    ->sortable()
                    ->icon('heroicon-o-clock'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => Booking::STATUS_PENDING,
                        'success' => Booking::STATUS_CONFIRMED,
                        'success' => Booking::STATUS_COMPLETED,
                        'danger' => Booking::STATUS_CANCELLED,
                        'info' => Booking::STATUS_RESCHEDULED,
                        'secondary' => Booking::STATUS_NO_SHOW,
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('status')
                    ->options(Booking::getStatusOptions()),

                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    }),
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
            ->defaultSort('booking_date', 'desc')
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}