<?php

declare(strict_types=1);

namespace Paysera\Interfaces;

interface HttpClientInterface
{
    public function get(string $url): string|false;
}
