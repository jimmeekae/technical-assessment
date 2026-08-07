<?php

namespace App\Filament\Resources\VehicleGateLogs\Pages;

use App\Filament\Resources\VehicleGateLogs\VehicleGateLogResource;
use App\Models\Driver;
use App\Models\VehicleGateLog;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListVehicleGateLogs extends ListRecords
{
    protected static string $resource = VehicleGateLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Gate In Button
            CreateAction::make()
                ->label('Gate In Vehicle'),

            // Dedicated Gate Out Modal Action
            Action::make('gate_out_modal')
                ->label('Gate Out Vehicle')
                ->color('danger')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->form([
                    Select::make('vehicle_gate_log_id')
                        ->label('Select Vehicle Currently In')
                        ->options(function () {
                            return VehicleGateLog::where('status', 'GATED_IN')
                                ->with(['vehicle', 'driver'])
                                ->get()
                                ->pluck('vehicle.registration_number', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            $log = VehicleGateLog::with('driver')->find($state);
                            if ($log) {
                                $set('driver_name', $log->driver?->name);
                                $set('driver_id_number', $log->driver_id_number);
                                $set('driver_phone', $log->driver_phone);
                            }
                        }),

                    TextInput::make('driver_name')
                        ->label('Driver Name')
                        ->readOnly(),

                    TextInput::make('driver_id_number')
                        ->label('Driver ID')
                        ->readOnly(),

                    TextInput::make('driver_phone')
                        ->label('Phone Number')
                        ->readOnly(),
                ])
                ->action(function (array $data) {
                    $log = VehicleGateLog::findOrFail($data['vehicle_gate_log_id']);
                    $log->update([
                        'gated_out_at' => now(),
                        'gated_out_by' => auth()->id(),
                        'status' => 'GATED_OUT',
                    ]);
                }),
        ];
    }
}