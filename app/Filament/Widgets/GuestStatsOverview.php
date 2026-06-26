<?php
namespace App\Filament\Widgets;
use App\Models\Guest; use Filament\Widgets\StatsOverviewWidget as BaseWidget; use Filament\Widgets\StatsOverviewWidget\Stat;
class GuestStatsOverview extends BaseWidget { protected function getStats(): array { return [Stat::make('Tamu Hari Ini', Guest::whereDate('waktu_kedatangan',today())->count()), Stat::make('Tamu Bulan Ini', Guest::whereMonth('waktu_kedatangan',now()->month)->whereYear('waktu_kedatangan',now()->year)->count()), Stat::make('Sidang Hari Ini', Guest::whereDate('waktu_kedatangan',today())->where('jenis_layanan','Menghadiri Sidang')->count())]; } }
