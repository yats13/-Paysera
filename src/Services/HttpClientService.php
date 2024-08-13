<?php
declare(strict_types=1);

namespace Paysera\Services;

use Paysera\Interfaces\HttpClientInterface;

class HttpClientService implements HttpClientInterface
{
    public function get(string $url): string|false
    {
        return file_get_contents($url);
    }
}
