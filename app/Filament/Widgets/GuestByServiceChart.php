<?php
namespace App\Filament\Widgets;
use App\Models\Guest; use Filament\Widgets\ChartWidget;
class GuestByServiceChart extends ChartWidget { protected static ?string $heading='Tamu berdasarkan Jenis Layanan'; protected function getData(): array { $data=Guest::selectRaw('jenis_layanan, count(*) total')->groupBy('jenis_layanan')->pluck('total','jenis_layanan'); return ['datasets'=>[['label'=>'Jumlah','data'=>$data->values()->all()]], 'labels'=>$data->keys()->all()]; } protected function getType(): string { return 'bar'; } }
