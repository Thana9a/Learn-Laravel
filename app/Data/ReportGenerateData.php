<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ReportGenerateData extends Data
{
    public function __construct(
        public string $title,
        public string $type,
    ) {}
}
