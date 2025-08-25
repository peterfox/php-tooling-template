<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use VendorName\Skeleton\Rector\ExampleRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ExampleRector::class);
};
