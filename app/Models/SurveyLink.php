<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model;
class SurveyLink extends Model { use HasFactory; protected $fillable=['nama_survey','url','aktif']; protected $casts=['aktif'=>'boolean']; }
