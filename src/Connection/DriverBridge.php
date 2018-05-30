<?php

declare(strict_types=1);

namespace App\Connection;

use function Amp\Mysql\pool;
use Doctrine\DBAL\Driver\AbstractMySQLDriver;

class DriverBridge extends AbstractMySQLDriver
{
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        return new ConnectionBridge(pool($this->constructPdoDsn($params, $username, $password)));
    }

    /**
     * Constructs the MySql PDO DSN.
     *
     * @param array $params
     *
     * @return string The DSN.
     */
    protected function constructPdoDsn(array $params, $username = null, $password = null)
    {
        $dsn = '';

        if (isset($params['host']) && $params['host'] != '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }
        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }
        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        }
        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }
        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }

        if ($username) {
            $dsn .= 'user=' . $username . ';';
        }

        if ($password) {
            $dsn .= 'password=' . $password . ';';
        }

        return trim($dsn, ';');
    }

    public function getName()
    {
        return 'amphp_mysql';
    }
}
