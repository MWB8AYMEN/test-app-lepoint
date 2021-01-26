<?php

namespace App\Services;

use Predis\Client;

class RedisDriverStorage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $key;

    public function __construct(Client $client, ?string $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    public function get($offset, $lines)
    {
        return $this->client->lrange($this->key, $offset, $lines);
    }

    public function add($value)
    {
        $this->client->rpush($this->key, [$value]);
    }

    public function remove($value)
    {
        $this->client->lrem($this->key, 0, $value);
    }

    public function exists()
    {
        return $this->client->exists($this->key);
    }

    public function reset()
    {
        $this->client->flushdb();
    }

    public function setKey(string $key)
    {
        $this->key = $key;
    }

}
