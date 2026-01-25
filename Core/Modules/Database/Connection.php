<?php
namespace Modules\Database;

use Modules\Main\Application;

class Connection
{
    private static ?\PDO $pdo = null;
    private static array $config = [];

    private static function getConnect(): \PDO
    {
        if (empty(self::$config))
        {
            self::$config = self::getDatabaseConfig();
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            self::$config['host'],
            self::$config['dbname']
        );

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_PERSISTENT => true,
        ];

        try
        {
            return new \PDO($dsn, self::$config['user'], self::$config['password'], $options);
        }
        catch (\PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    private static function getDatabaseConfig(): array
    {
        $configPath = Application::getInstance()->root . '/config/database.config.php';

        if (!file_exists($configPath))
        {
            throw new \RuntimeException('Database config file not found');
        }

        return require $configPath;
    }

    public static function getPdo(): \PDO
    {
        if (!self::$pdo)
        {
            self::$pdo = self::getConnect();
        }

        try
        {
            self::$pdo->query('SELECT 1');
        }
        catch (\PDOException $e)
        {
            self::$pdo = self::getConnect();
        }

        return self::$pdo;
    }
}