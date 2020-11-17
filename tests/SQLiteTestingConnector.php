<?php

namespace Brunoocto\Sample\Tests;

use Illuminate\Database\Connectors\SQLiteConnector;

class SQLiteTestingConnector extends SQLiteConnector
{
    /**
     * Keep memory connection opened
     */
    protected static $shared_memory_connection = null;

    /**
     * Establish a database connection.
     *
     * When the database is :shared-memory:, save the created PDO and return it after that.
     *
     * @param  array  $config
     * @return \PDO
     *
     * @throws \InvalidArgumentException
     */
    public function connect(array $config)
    {
        // Create a new type of database connection
        if ($config['database'] === ':memory:') {

            // If no connection exists yet
            if (self::$shared_memory_connection === null) {
                $options = $this->getOptions($config);
                self::$shared_memory_connection = $this->createConnection('sqlite::memory:', $config, $options);
            }
            // Return the shared memory connection
            return self::$shared_memory_connection;
        }

        return parent::connect($config);
    }
}
