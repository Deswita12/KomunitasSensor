<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class PageViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Kunjungan Halaman (7 Hari Terakhir)';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));

        $counts = $days->map(function ($date) {
            return PageView::whereDate('viewed_at', $date)->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data'  => $counts->toArray(),
                    'backgroundColor' => '#416744',
                    'borderColor'     => '#416744',
                ],
            ],
            'labels' => $days->map(fn ($d) => \Carbon\Carbon::parse($d)->translatedFormat('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}