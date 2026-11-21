<?php
namespace Tests\Feature;

use App\Livewire\{DataUpload, FollowUpTable, PortfolioTable};
use App\Models\{Project, User};
use App\Support\CurrentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\Support\DatasetHelpers;
use Tests\TestCase;

class LivewireComponentsTest extends TestCase
{
    use RefreshDatabase, DatasetHelpers;

    public function test_portfolio_filters_search_and_sorting_work(): void
    {
        $this->seedDataset();
        Livewire::test(PortfolioTable::class)->assertSee('PRJ-001')->assertSee('PRJ-006')
            ->set('level', 'HIGH')->assertSee('PRJ-005')->assertDontSee('PRJ-006')
            ->set('level', '')->set('search', 'Beta')->assertSee('PRJ-002')->assertDontSee('PRJ-005')
            ->set('search', 'tidak-ada-proyek')->assertSee('Tidak ada proyek yang cocok')
            ->call('clear')->call('sortBy', 'project_code')->assertSet('sort', 'project_code')->assertSet('dir', 'asc');
        $pic = Project::whereNotNull('pic')->first()->pic;
        Livewire::test(PortfolioTable::class)->set('pic', $pic)->assertSee($pic);
    }

    public function test_follow_up_table_lists_follow_ups(): void
    {
        $this->seedDataset();
        $fu = $this->followUpFor($this->finding('PRJ-005', 'missing_prerequisite'));
        Livewire::test(FollowUpTable::class)->assertSee($fu->title)->set('status', 'RESOLVED')->assertDontSee($fu->title);
    }

    public function test_upload_component_imports_files_and_shows_summary(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        CurrentUser::set(User::where('role', 'DIRECTOR')->first());
        \App\Models\Project::query()->delete();
        $dir = database_path('data');
        $files = [UploadedFile::fake()->createWithContent('master.csv', file_get_contents("$dir/02_Project_Master.csv")),
            UploadedFile::fake()->createWithContent('prod.json', file_get_contents("$dir/04_Production_Data.json")),
            UploadedFile::fake()->createWithContent('upd.csv', file_get_contents("$dir/03_Project_Updates.csv"))];
        Livewire::test(DataUpload::class)->set('files', $files)->call('import')->assertHasNoErrors()->assertSee('Ringkasan proses')
            ->assertSet('results.0.source_kind', 'PROJECT_MASTER')->assertSet('results.1.source_kind', 'PROJECT_UPDATES')->assertSet('results.2.source_kind', 'PRODUCTION_DATA');
        $this->assertSame(6, Project::count());
    }

    public function test_upload_component_validates_file_type_and_requires_director(): void
    {
        $this->seedDataset();
        Livewire::test(DataUpload::class)->call('import')->assertHasErrors('files');
        Livewire::test(DataUpload::class)->set('files', [UploadedFile::fake()->create('virus.exe', 10)])->call('import')->assertHasErrors('files.0');
        CurrentUser::set(User::where('role', 'PROJECT_MANAGER')->first());
        Livewire::test(DataUpload::class)->set('files', [UploadedFile::fake()->createWithContent('a.csv', "x\n1")])->call('import')->assertSet('error', 'Hanya Director yang dapat mengunggah data.');
    }
}
