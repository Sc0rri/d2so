<?php

namespace components;

use Dotenv\Dotenv;

/**
 * Class EnvHelper removes .env variables from _SERVER variable for not log sensitive data
 * and check require env variable
 */
class EnvHelper
{
    /**
     * @var null|Dotenv
     */
    private static $dotenv = null;
    /**
     * @var null|array
     */
    private static $previousKeys = null;

    public static function load()
    {
        self::beforeLoad();
        self::loadEnv();
        self::afterLoad();
        self::validateEnv();
    }

    private static function beforeLoad()
    {
        self::$previousKeys = array_keys($_ENV);
    }

    private static function loadEnv()
    {
        self::$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        self::$dotenv->load();
    }

    private static function validateEnv()
    {
        self::$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'])->notEmpty();
        self::$dotenv->required('APP_ENV')->allowedValues(['prod', 'dev']);
    }

    private static function afterLoad()
    {
        $currentKeys = array_keys($_ENV);
        $newKeys = array_diff($currentKeys, self::$previousKeys);
        array_map(function ($key) {
            unset($_SERVER[$key]);
        }, $newKeys);
    }
}