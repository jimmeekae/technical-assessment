<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Top Approval Banner (> 10,000)
                Placeholder::make('approval_warning')
                    ->hiddenLabel()
                    ->content(function (Get $get) {
                        $total = (float) $get('total_after_discount');
                        if ($total > 10000) {
                            return new HtmlString("
                                <div class='p-3 mb-2 text-sm text-amber-800 rounded bg-amber-50 dark:bg-gray-800 dark:text-amber-400 font-semibold border border-amber-300' role='alert'>
                                     Invoice will go for approval – Amount: " . number_format($total, 2) . " KES
                                </div>
                            ");
                        }
                        return null;
                    })
                    ->visible(fn (Get $get) => (float) $get('total_after_discount') > 10000)
                    ->columnSpanFull(),

                // Header Section
                Section::make('')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(12)->schema([
                            // Left Column (Customer Details)
                            Grid::make(1)
                                ->columnSpan(6)
                                ->schema([
                                    Select::make('customer_id')
                                        ->label('Customer Code')
                                        ->relationship('customer', 'customer_code')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $customer = Customer::find($state);
                                            $set('customer_name', $customer?->name ?? '');
                                        }),

                                    TextInput::make('customer_name')
                                        ->label('Customer Name')
                                        ->disabled()
                                        ->dehydrated(false),
                                ]),

                            // Right Column (Invoice Metadata)
                            Grid::make(1)
                                ->columnSpan(6)
                                ->schema([
                                    TextInput::make('invoice_number')
                                        ->label('No.')
                                        ->required()
                                        ->maxLength(50)
                                        ->default(fn () => 'INV-' . str_pad((string) (Invoice::max('id') + 1), 6, '0', STR_PAD_LEFT)),

                                    DatePicker::make('posting_date')
                                        ->label('Posting Date')
                                        ->default(now())
                                        ->required(),
                                ]),
                        ]),
                    ]),

                // Table Section: Line Items
                Section::make('')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('invoiceItems')
                            ->relationship('invoiceItems')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item No.')
                                    ->options(Item::all()->pluck('item_code', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $item = Item::find($state);
                                        if ($item) {
                                            $set('item_description', $item->description);
                                            $set('price_before_discount', $item->unit_price);
                                            static::calculateRowTotals($set, $get);
                                        }
                                    })
                                    ->columnSpan(2),

                                TextInput::make('item_description')
                                    ->label('Item Description')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateRowTotals($set, $get))
                                    ->columnSpan(1),

                                TextInput::make('price_before_discount')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateRowTotals($set, $get))
                                    ->columnSpan(2),

                                TextInput::make('discount')
                                    ->label('Disc %')
                                    ->numeric()
                                    ->default(0)
                                    ->maxValue(50)
                                    ->validationMessages([
                                        'max' => 'Discount percentage cannot exceed 50%.',
                                    ])
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateRowTotals($set, $get))
                                    ->columnSpan(1),

                                TextInput::make('price_after_discount')
                                    ->label('Price After Disc.')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->columnSpan(2),

                                TextInput::make('total')
                                    ->label('Total (LC)')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->columnSpan(2),
                            ])
                            ->columns(12)
                            ->compact()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateTableTotals($set, $get))
                            ->defaultItems(1),
                    ]),

                // Footer Section
                Section::make('')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(12)->schema([
                            // Left Side: Sales Employee & Remarks
                            Grid::make(1)
                                ->columnSpan(7)
                                ->schema([
                                    Select::make('sales_employee_id')
                                        ->label('Sales Employee')
                                        ->relationship('salesEmployee', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    Textarea::make('remarks')
                                        ->label('Remarks')
                                        ->required()
                                        ->rows(3),
                                ]),

                            // Right Side: Bottom Summary Totals
                            Grid::make(1)
                                ->columnSpan(5)
                                ->schema([
                                    TextInput::make('total_before_discount')
                                        ->label('Total Before Discount')
                                        ->numeric()
                                        ->readOnly()
                                        ->prefix('KES'),

                                    TextInput::make('discount')
                                        ->label('Discount')
                                        ->numeric()
                                        ->default(0)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateTableTotals($set, $get)),

                                    TextInput::make('total_after_discount')
                                        ->label('Total After Discount')
                                        ->numeric()
                                        ->readOnly()
                                        ->prefix('KES')
                                        ->live(),
                                ]),
                        ]),
                    ]),
            ]);
    }

    protected static function calculateRowTotals(Set $set, Get $get): void
    {
        $qty = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('price_before_discount') ?? 0);
        $discountPct = (float) ($get('discount') ?? 0);

        $priceAfterDiscount = $unitPrice - ($unitPrice * ($discountPct / 100));
        $lineTotal = $qty * $priceAfterDiscount;

        $set('price_after_discount', round($priceAfterDiscount, 3));
        $set('total', round($lineTotal, 3));
    }

    public static function calculateTableTotals(Set $set, Get $get): void
    {
        $items = $get('invoiceItems') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += (float) ($item['total'] ?? 0);
        }

        $headerDiscount = (float) ($get('discount') ?? 0);
        $finalTotal = $subtotal - $headerDiscount;

        $set('total_before_discount', round($subtotal, 3));
        $set('total_after_discount', round(max(0, $finalTotal), 3));
    }
}