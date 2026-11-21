<?php
namespace App\Livewire;

use App\Services\DataImportService;
use App\Support\CurrentUser;
use Livewire\Component;
use Livewire\WithFileUploads;

class DataUpload extends Component
{
    use WithFileUploads;

    public $files = [];
    public ?string $asOf = null;
    public array $results = [];
    public ?string $error = null;
    public int $step = 0;

    public function mount(): void { $this->asOf = \App\Models\Setting::asOf()->format('Y-m-d\TH:i'); }

    public function removeFile(int $i): void { unset($this->files[$i]); $this->files = array_values($this->files); }

    public function import(DataImportService $svc): void
    {
        $this->error = null; $this->results = [];
        if (! CurrentUser::get()?->isDirector()) { $this->error = 'Hanya Director yang dapat mengunggah data.'; return; }
        $this->validate([
            'files' => ['required', 'array', 'min:1', 'max:6'],
            'files.*' => ['file', 'max:' . config('ontrack.upload.max_kb'), 'extensions:csv,json,txt'],
            'asOf' => ['nullable', 'date'],
        ], [
            'files.required' => 'Pilih minimal satu file CSV atau JSON.',
            'files.*.max' => 'Ukuran file melebihi ' . config('ontrack.upload.max_kb') . ' KB.',
            'files.*.extensions' => 'Hanya file .csv atau .json yang didukung.',
        ]);
        $input = [];
        foreach ($this->files as $f) $input[] = ['path' => $f->getRealPath(), 'name' => $f->getClientOriginalName()];
        $batches = $svc->importFiles($input, $this->asOf);
        $this->results = collect($batches)->map->only(['id', 'file_name', 'source_kind', 'status', 'total_records', 'valid_records', 'invalid_records', 'new_records', 'duplicate_records', 'conflicting_records', 'missing_field_count', 'message'])->all();
        $this->files = [];
        $this->step = 1;
    }

    public function render() { return view('livewire.data-upload'); }
}
