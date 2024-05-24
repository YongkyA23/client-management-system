<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;


use App\Models\Invoice;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Chart';

    protected function getData(): array
    {
        $data = Trend::model(Invoice::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
}
