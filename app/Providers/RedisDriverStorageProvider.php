<?php

namespace App\Providers;

use App\Services\RedisClient;
use App\Services\RedisDriverStorage;
use Illuminate\Support\ServiceProvider;
use Predis\Client;

class RedisDriverStorageProvider extends ServiceProvider
{

    private const REDIS_SERVER_ADDRESS = 'localhost';
    private const REDIS_DB = 0;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RedisDriverStorage::class, function () {
            $servers = self::REDIS_SERVER_ADDRESS;
            $db   = self::REDIS_DB;
            $password = null;
            $replication = null;
            $service = null;

            $redisClient = new Client(
                $servers, array(
                    'replication' => $replication,
                    'service' => $service,
                    'parameters' => array(
                        'password' => $password,
                        'database' => $db,
                    ),
                )
            );

            return new RedisDriverStorage($redisClient, null);
        });
    }
}
