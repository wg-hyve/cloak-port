<?php

declare(strict_types=1);

namespace ClockPort\Tests\Traits;

trait HasPayload
{
    protected function load(string $name): string
    {
        $dir = realpath(sprintf('%s/../Data/%s', __DIR__, $name));

        if($dir) {

            return file_get_contents($dir);
        }

        return '';
    }

    protected function loadJson(string $name): array
    {
        return json_decode($this->load($name), true);
    }
}
