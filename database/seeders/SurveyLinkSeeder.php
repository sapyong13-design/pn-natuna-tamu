<?php
namespace Database\Seeders;
use App\Models\SurveyLink; use Illuminate\Database\Seeder;
class SurveyLinkSeeder extends Seeder { public function run(): void { SurveyLink::updateOrCreate(['nama_survey'=>'Survey Kepuasan Masyarakat'],['url'=>'https://survey.populix.id/145264','aktif'=>true]); SurveyLink::updateOrCreate(['nama_survey'=>'Survey IKM dan SPAK'],['url'=>'https://hovqr.me/0771bd45','aktif'=>true]); } }
