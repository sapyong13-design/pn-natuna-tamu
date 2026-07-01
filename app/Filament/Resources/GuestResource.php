<?php
namespace App\Filament\Resources;
use App\Filament\Resources\GuestResource\Pages; use App\Models\Guest; use Filament\Forms; use Filament\Forms\Form; use Filament\Resources\Resource; use Filament\Tables; use Filament\Tables\Table; use Maatwebsite\Excel\Facades\Excel; use App\Exports\GuestsExport;
class GuestResource extends Resource { protected static ?string $model=Guest::class; protected static ?string $navigationLabel='Guests'; protected static ?string $navigationIcon='heroicon-o-rectangle-stack'; public static function form(Form $form): Form { return $form->schema([Forms\Components\TextInput::make('kode_kunjungan')->disabled(), Forms\Components\TextInput::make('nama_tamu')->required(), Forms\Components\TextInput::make('pekerjaan')->required(),
Forms\Components\DatePicker::make('tanggal_lahir')->required(),
Forms\Components\Select::make('jenis_kelamin')->options([
    'Laki-laki' => 'Laki-laki',
    'Perempuan' => 'Perempuan'
])->required(),
Forms\Components\Select::make('pendidikan_terakhir')->options([
    'Tidak Sekolah' => 'Tidak Sekolah',
    'SD' => 'SD',
    'SMP' => 'SMP',
    'SMA' => 'SMA / Sederajat',
    'D3' => 'Diploma (D1/D2/D3)',
    'S1' => 'Sarjana (S1)',
    'S2' => 'Magister (S2)',
    'S3' => 'Doktor (S3)'
])->required(), Forms\Components\TextInput::make('no_hp')->required(), Forms\Components\TextInput::make('alamat_instansi')->required(), Forms\Components\Select::make('jenis_layanan')->options(array_combine(\App\Models\Guest::JENIS_LAYANAN, \App\Models\Guest::JENIS_LAYANAN))->required(), Forms\Components\Textarea::make('keperluan'), Forms\Components\Select::make('peran_sidang')->options(array_combine(\App\Models\Guest::PERAN_SIDANG, \App\Models\Guest::PERAN_SIDANG)), Forms\Components\TextInput::make('nomor_perkara'), Forms\Components\TextInput::make('agenda_sidang'), Forms\Components\TextInput::make('ruang_sidang'), Forms\Components\TimePicker::make('jam_sidang')->seconds(false), Forms\Components\DateTimePicker::make('waktu_kedatangan')]); } public static function table(Table $table): Table { return $table->columns([Tables\Columns\TextColumn::make('kode_kunjungan')->searchable()->sortable(), Tables\Columns\TextColumn::make('nama_tamu')->searchable()->sortable(), Tables\Columns\TextColumn::make('pekerjaan')->searchable()->sortable(),
Tables\Columns\TextColumn::make('tanggal_lahir')->date()->sortable(),
Tables\Columns\TextColumn::make('jenis_kelamin')->sortable(),
Tables\Columns\TextColumn::make('pendidikan_terakhir')->sortable(), Tables\Columns\TextColumn::make('jenis_layanan')->searchable()->sortable(), Tables\Columns\TextColumn::make('peran_sidang')->searchable()->sortable(), Tables\Columns\TextColumn::make('waktu_kedatangan')->searchable()->sortable()])->filters([])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])->headerActions([Tables\Actions\Action::make('export')->label('Export Excel')->form([Forms\Components\DatePicker::make('tanggal_awal'),Forms\Components\DatePicker::make('tanggal_akhir'),Forms\Components\Select::make('jenis_layanan')->options(array_combine(\App\Models\Guest::JENIS_LAYANAN, \App\Models\Guest::JENIS_LAYANAN))])->action(fn(array $data)=>Excel::download(new GuestsExport($data['tanggal_awal']??null,$data['tanggal_akhir']??null,$data['jenis_layanan']??null),'buku-tamu-pn-natuna-'.now()->format('Y-m-d').'.xlsx'))]); } public static function getPages(): array { return ['index'=>Pages\ListGuest::route('/'),'create'=>Pages\CreateGuest::route('/create'),'edit'=>Pages\EditGuest::route('/{record}/edit')]; } }
