<?php

namespace App\Enums;

/**
 * Upload processing status values.
 */
enum UploadStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
