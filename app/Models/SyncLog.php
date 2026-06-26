<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model;
class SyncLog extends Model { use HasFactory; protected $fillable=['sumber','status','pesan','synced_at']; protected $casts=['synced_at'=>'datetime']; }
