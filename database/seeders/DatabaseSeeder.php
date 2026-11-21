<?php
namespace Database\Seeders;

use App\Models\{Setting, User};
use App\Services\DataImportService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'director@ontrack.test'], ['name' => 'Direktur (Demo)', 'role' => 'DIRECTOR']);
        User::firstOrCreate(['email' => 'pm@ontrack.test'], ['name' => 'Raka (Project Manager)', 'role' => 'PROJECT_MANAGER', 'pic_name' => 'Raka']);
        Setting::write('as_of', config('ontrack.as_of'));
        $dir = database_path('data');
        $files = [['path' => "$dir/02_Project_Master.csv", 'name' => '02_Project_Master.csv'], ['path' => "$dir/03_Project_Updates.csv", 'name' => '03_Project_Updates.csv'],
            ['path' => "$dir/04_Production_Data.json", 'name' => '04_Production_Data.json']];
        app(DataImportService::class)->importFiles($files);
    }
}
