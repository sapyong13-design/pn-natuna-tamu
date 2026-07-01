<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('pekerjaan');
            $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            $table->string('pendidikan_terakhir')->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'jenis_kelamin', 'pendidikan_terakhir']);
        });
    }
};
