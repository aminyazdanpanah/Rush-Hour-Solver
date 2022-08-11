<?php

namespace Tests\RushHour;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public $srcDir;

    public function setUp(): void
    {
        $this->srcDir = __DIR__ . '/files';
    }

    public function getRushHourSamplePath(string $filename): string
    {
        return sprintf($this->srcDir . "/%s.txt", $filename);
    }
}
