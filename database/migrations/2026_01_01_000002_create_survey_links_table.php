<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('survey_links', function (Blueprint $table) { $table->id(); $table->string('nama_survey'); $table->string('url', 2048); $table->boolean('aktif')->default(true); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('survey_links'); } };
