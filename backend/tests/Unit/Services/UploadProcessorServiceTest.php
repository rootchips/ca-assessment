<?php

namespace Tests\Unit\Services;

use App\Services\UploadProcessorService;
use App\Contracts\ProductRepositoryContract;
use App\Enums\UploadStatus;
use App\Models\Upload;
use Illuminate\Support\Facades\{Redis, Event};
use Illuminate\Broadcasting\BroadcastFactory;
use Illuminate\Broadcasting\PendingBroadcast;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Creator\WriterFactory;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadProcessorServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_says_success_processing_sets_completed_status()
    {
        Event::fake();

        app()->instance(BroadcastFactory::class, new class () {
            public function event($event): PendingBroadcast
            {
                return new PendingBroadcast(app('events'), $event);
            }
        });

        $mockRepo = Mockery::mock(ProductRepositoryContract::class);

        $mockRepo->shouldReceive('applyStatusUpdates')->twice()->andReturnNull();

        $service = new UploadProcessorService($mockRepo);

        $upload = Upload::create([
            'id' => '1',
            'file_name' => 'product_status_list.xlsx',
            'status' => UploadStatus::PENDING,
        ]);

        $filePath = base_path('tests/stubs/product_status_list.xlsx');

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $writer = WriterFactory::createFromFile($filePath);
        $writer->openToFile($filePath);
        $writer->addRow(Row::fromValues(['PRODUCT_ID', 'STATUS', 'QUANTITY']));
        for ($i = 1; $i <= 1000; $i++) {
            $writer->addRow(Row::fromValues([$i, 'Sold', 1]));
        }
        $writer->close();

        $mockUpload = Mockery::mock($upload)->makePartial();
        $mockUpload->shouldReceive('getFirstMediaPath')->andReturn($filePath);
        $mockUpload->shouldReceive('fresh')->andReturn($mockUpload);

        Redis::flushall();

        $service->process($mockUpload);

        $this->assertEquals(UploadStatus::COMPLETED, $mockUpload->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_says_invalid_xlsx_sets_failed_status()
    {
        Event::fake();

        app()->instance(BroadcastFactory::class, new class () {
            public function event($event): PendingBroadcast
            {
                return new PendingBroadcast(app('events'), $event);
            }
        });

        $mockRepo = Mockery::mock(ProductRepositoryContract::class);
        $service  = new UploadProcessorService($mockRepo);

        $upload = Upload::create([
            'id' => '2',
            'file_name' => 'bad.xlsx',
            'status' => UploadStatus::PENDING,
        ]);

        $mockUpload = Mockery::mock($upload)->makePartial();
        $mockUpload->shouldReceive('getFirstMediaPath')->andReturn('/invalid/path.xlsx');
        $mockUpload->shouldReceive('fresh')->andReturn($mockUpload);

        Redis::flushall();

        try {
            $service->process($mockUpload);
        } catch (\Throwable) {
            //
        }

        $this->assertEquals(UploadStatus::FAILED, $mockUpload->status);
    }
}
