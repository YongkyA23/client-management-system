<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceOverview extends BaseWidget
{
    
    protected function getStats(): array
    {
        return [
            Stat::make('Clients', Client::count()),
            Stat::make('Total Invoices', Invoice::count()),
            Stat::make('Unpaid Invoices', Invoice::whereNull('paid_date')->count()),
        ];
    }
}
