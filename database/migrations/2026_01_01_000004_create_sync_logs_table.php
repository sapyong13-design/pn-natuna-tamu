<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('sync_logs', function (Blueprint $table) { $table->id(); $table->string('sumber'); $table->string('status'); $table->text('pesan')->nullable(); $table->dateTime('synced_at'); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('sync_logs'); } };
