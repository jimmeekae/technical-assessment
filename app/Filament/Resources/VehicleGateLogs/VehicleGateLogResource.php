<?php

namespace App\Filament\Resources\VehicleGateLogs;

use App\Filament\Resources\VehicleGateLogs\Pages\CreateVehicleGateLog;
use App\Filament\Resources\VehicleGateLogs\Pages\EditVehicleGateLog;
use App\Filament\Resources\VehicleGateLogs\Pages\ListVehicleGateLogs;
use App\Filament\Resources\VehicleGateLogs\Schemas\VehicleGateLogForm;
use App\Filament\Resources\VehicleGateLogs\Tables\VehicleGateLogsTable;
use App\Models\VehicleGateLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleGateLogResource extends Resource
{
    protected static ?string $model = VehicleGateLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Vehicle Gate Ops';

    protected static ?string $modelLabel = 'Gate Log';

    protected static ?string $pluralModelLabel = 'Vehicle Gate Logs';

    public static function form(Schema $schema): Schema
    {
        return VehicleGateLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleGateLogsTable::configure($table);
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
            'index' => ListVehicleGateLogs::route('/'),
            'create' => CreateVehicleGateLog::route('/create'),
            'edit' => EditVehicleGateLog::route('/{record}/edit'),
        ];
    }
}