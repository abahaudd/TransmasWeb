<?php

namespace App\Modules\Dashboard\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Trend';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',

                    'data' => [
                        245000,
                        268000,
                        259000,
                        301000,
                        326000,
                        348000,
                        362000,
                        381000,
                        395000,
                        422000,
                        438000,
                        465000,
                    ],

                    'fill' => true,
                    'tension' => 0.35,
                    'borderWidth' => 3,
                ],
            ],

            'labels' => [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
