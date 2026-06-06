<?php

declare(strict_types=1);

namespace App\Shared\Nanoid;

use Hidehalo\Nanoid\Client;
use Tempest\Container\Singleton;

/**
 * Container-managed service that generates nanoid strings.
 * Registered as a singleton so the underlying {@see Client} is shared.
 */
#[Singleton]
final class NanoidGenerator
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function generate(int $size = 21): string
    {
        return $this->client->generateId($size);
    }
}
