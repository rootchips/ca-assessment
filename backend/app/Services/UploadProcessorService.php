<?php

namespace App\Services;

use App\Contracts\{UploadProcessorContract, ProductRepositoryContract};
use App\Events\{UploadStatusUpdated, UploadProgressUpdated};
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;
use App\Helpers\TextNormalizer;
use App\Enums\UploadStatus;
use App\Models\Upload;
use RuntimeException;
use Throwable;

class UploadProcessorService implements UploadProcessorContract
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(private ProductRepositoryContract $products) {}

    /**
     * Process an uploaded spreadsheet.
     *
     * @param Upload $upload
     * @return void
     */
    public function process(Upload $upload): void
    {
        $this->setStatus($upload, UploadStatus::PROCESSING);

        try {
            $this->importSpreadsheet($upload);

            $this->setStatus($upload, UploadStatus::COMPLETED);
        } catch (Throwable $e) {
            $this->setStatus($upload, UploadStatus::FAILED);
            
            Redis::setex("upload:progress:{$upload->id}", 300, 0);

            broadcast(new UploadStatusUpdated($upload->fresh()));

            throw $e;
        }
    }

    /**
     * Import spreadsheet rows and apply product updates.
     *
     * @param Upload $upload
     * @return void
     */
    private function importSpreadsheet(Upload $upload): void
    {
        $path = $upload->getFirstMediaPath('files');

        $totalRows = $this->countRows($path);

        if ($totalRows === 0) {
            throw new RuntimeException('No data found.');
        }

        $reader = ReaderFactory::createFromFile($path);
        $reader->open($path);

        $headers = [];
        $rowsBuffer = [];
        $processed = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                $cells = $row->toArray();

                if ($rowIndex === 1) {
                    $headers = $this->normalizeHeaders($cells);

                    if (!in_array('PRODUCT_ID', $headers, true) || !in_array('STATUS', $headers, true)) {
                        $reader->close();
                        throw new RuntimeException('Missing PRODUCT_ID or STATUS column.');
                    }

                    continue;
                }

                if ($this->isEmptyRow($cells)) {
                    continue;
                }

                $rowsBuffer[] = array_combine($headers, array_pad($cells, count($headers), null));
                $processed++;

                if (count($rowsBuffer) >= 500) {
                    $this->products->applyStatusUpdates(Collection::make($rowsBuffer));
                    $rowsBuffer = [];
                    $this->updateProgress($upload->id, $processed, $totalRows);
                }
            }
            break;
        }

        if (!empty($rowsBuffer)) {
            $this->products->applyStatusUpdates(Collection::make($rowsBuffer));

            $this->updateProgress($upload->id, $processed, $totalRows);
        }

        $reader->close();
        
        Redis::setex("upload:progress:{$upload->id}", 300, 100);

        broadcast(new UploadProgressUpdated($upload->id, 100));
    }

    /**
     * Count non-empty data rows in spreadsheet.
     *
     * @param string $path
     * @return int
     */
    private function countRows(string $path): int
    {
        $reader = ReaderFactory::createFromFile($path);
        $reader->open($path);

        $count = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex === 1) {
                    continue;
                }

                if (!$this->isEmptyRow($row->toArray())) {
                    $count++;
                }
            }
            break;
        }

        $reader->close();

        return $count;
    }

    /**
     * Normalize spreadsheet headers into uppercase snake case.
     *
     * @param array $headers
     * @return array
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(
            fn ($header) => strtoupper(str_replace(' ', '_', TextNormalizer::clean((string) $header))),
            $headers
        );
    }

    /**
     * Determine whether row cells are all empty.
     *
     * @param array $cells
     * @return bool
     */
    private function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Update progress in Redis and broadcast progress updates.
     *
     * @param string $uploadId
     * @param int $processed
     * @param int $total
     * @return void
     */
    private function updateProgress(string $uploadId, int $processed, int $total): void
    {
        $progress = (int) min(100, round(($processed / max($total, 1)) * 100));
        Redis::setex("upload:progress:{$uploadId}", 300, $progress);

        if ($progress % 5 === 0 || $progress === 100) {
            broadcast(new UploadProgressUpdated($uploadId, $progress));
        }
    }

    /**
     * Set upload status and broadcast status update.
     *
     * @param Upload $upload
     * @param UploadStatus $status
     * @return void
     */
    private function setStatus(Upload $upload, UploadStatus $status): void
    {
        $upload->update([
            'status' => $status,
            'processed_at' => now(),
        ]);

        broadcast(new UploadStatusUpdated($upload));
    }
}
