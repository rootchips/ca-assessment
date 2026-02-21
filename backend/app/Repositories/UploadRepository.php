<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\UploadRepositoryContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Helpers\CacheBucket;
use App\Enums\UploadStatus;
use App\Jobs\ProcessXlsx;
use App\Models\Upload;

class UploadRepository implements UploadRepositoryContract
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(private Upload $model) {}

    /**
     * Retrieve all uploads ordered by latest first.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return CacheBucket::remember(
            'uploads',
            ['latest' => true],
            now()->addMinutes(2),
            fn () => $this->model
                ->newQuery()
                ->latest()
                ->get()
        );
    }

    /**
     * Create a new upload record and dispatch processing job.
     *
     * @param UploadedFile $file
     * @return Upload
     */
    public function create(UploadedFile $file): Upload
    {
        return DB::transaction(function () use ($file) {
            $upload = $this->model
            ->create([
                'id' => (string) Str::uuid(),
                'file_name' => $file->getClientOriginalName(),
                'status' => UploadStatus::PENDING,
            ]);

            $upload->addMedia($file)
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('files');

            ProcessXlsx::dispatch($upload);
            CacheBucket::flush('uploads');

            return $upload->refresh();
        });
    }

    /**
     * Update status for a given upload.
     *
     * @param Upload $upload
     * @param UploadStatus $status
     * @return void
     */
    public function updateStatus(Upload $upload, UploadStatus $status): void
    {
        $upload->update(['status' => $status]);
        CacheBucket::flush('uploads');
    }
}
