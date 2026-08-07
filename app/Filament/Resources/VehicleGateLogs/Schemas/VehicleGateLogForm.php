<?php

namespace App\Filament\Resources\VehicleGateLogs\Schemas;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleGateLog;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VehicleGateLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Vehicle selection (Searchable dropdown)
                Select::make('vehicle_id')
                    ->label('Vehicle Number')
                    ->options(fn () => Vehicle::pluck('registration_number', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                // Driver selection (Searchable dropdown with auto-populating fields)
                Select::make('driver_id')
                    ->label('Driver Name')
                    ->options(fn () => Driver::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $driver = Driver::find($state);
                        if ($driver) {
                            $set('driver_id_number', $driver->national_id);
                            $set('driver_phone', $driver->phone_number);
                        } else {
                            $set('driver_id_number', null);
                            $set('driver_phone', null);
                        }
                    }),

                // Auto-populated Driver ID
                TextInput::make('driver_id_number')
                    ->label('Driver ID')
                    ->required()
                    ->readOnly()
                    ->dehydrated(),

                // Auto-populated Phone Number
                TextInput::make('driver_phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->readOnly()
                    ->dehydrated(),

                // Automatic System Captures (Gate In)
                Hidden::make('gated_in_at')
                    ->default(now()),

                Hidden::make('gated_in_by')
                    ->default(fn () => Auth::id()),

                Hidden::make('status')
                    ->default('GATED_IN'),
            ]);
    }
}