<?php

namespace App\Filament\Resources\VehicleGateLogs\Tables;

use App\Models\VehicleGateLog;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VehicleGateLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('driver.name')
                    ->label('Driver Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('driver_id_number')
                    ->label('Driver ID')
                    ->searchable(),

                TextColumn::make('driver_phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('gated_in_at')
                    ->label('Gated In At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('gatedInUser.name')
                    ->label('Gated In By')
                    ->sortable(),

                TextColumn::make('gated_out_at')
                    ->label('Gated Out At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('gatedOutUser.name')
                    ->label('Gated Out By')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'GATED_IN' => 'warning',
                        'GATED_OUT' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Gate Out Action: Directly gates out currently gated-in vehicles
                Action::make('gate_out')
                    ->label('Gate Out')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('danger')
                    ->visible(fn (VehicleGateLog $record) => $record->status === 'GATED_IN')
                    ->requiresConfirmation()
                    ->action(function (VehicleGateLog $record) {
                        $record->update([
                            'gated_out_at' => now(),
                            'gated_out_by' => Auth::id(),
                            'status' => 'GATED_OUT',
                        ]);
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}