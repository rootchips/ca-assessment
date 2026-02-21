<?php

namespace App\Jobs;

use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\UploadProcessorContract;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use App\Models\Upload;

class ProcessXlsx implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Upload $upload
     * @return void
     */
    public function __construct(public Upload $upload) {}

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, WithoutOverlapping>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping($this->upload->id)];
    }

    /**
     * Execute the job.
     *
     * @param UploadProcessorContract $processor
     * @return void
     */
    public function handle(UploadProcessorContract $processor): void
    {
        $processor->process($this->upload);
    }
}
