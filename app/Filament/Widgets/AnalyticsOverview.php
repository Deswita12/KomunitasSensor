<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use App\Models\Post;
use App\Models\HelpRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kunjungan (30 hari)', PageView::where('viewed_at', '>=', now()->subDays(30))->count())
                ->description('Semua halaman')
                ->color('success'),

            Stat::make('Halaman Paling Populer', $this->mostVisitedPath())
                ->description('30 hari terakhir')
                ->color('primary'),

            Stat::make('Postingan Komunitas', Post::published()->count())
                ->description('Yang sudah dipublikasikan')
                ->color('info'),

            Stat::make('Pesan Bantuan Baru', HelpRequest::where('status', 'new')->count())
                ->description('Belum dibaca')
                ->color('warning'),
        ];
    }

    protected function mostVisitedPath(): string
    {
        $top = PageView::where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('path, COUNT(*) as total')
            ->groupBy('path')
            ->orderByDesc('total')
            ->first();

        return $top?->path ?? '-';
    }
}