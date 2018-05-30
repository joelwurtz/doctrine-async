<?php

declare(strict_types=1);

namespace App\Connection;

use function Amp\GreenThread\await;
use Amp\Mysql\CommandResult;
use Amp\Mysql\Pool;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\ParameterType;

class ConnectionBridge implements Connection
{
    private $pool;

    private $lastId;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function prepare($prepareString)
    {
        return new StatementBridge(await($this->pool->prepare($prepareString)));
    }

    public function query()
    {
        $args = func_get_args();
        $argsCount = count($args);

        if ($argsCount == 2) {
            return $this->resultToStatement(await($this->pool->execute($args[0], $args[1])));
        }

        return $this->resultToStatement(await($this->pool->query($args[0])));
    }

    public function quote($input, $type = ParameterType::STRING)
    {
    }

    public function exec($statement)
    {
        $result = await($this->pool->query($statement));

        if ($result instanceof CommandResult) {
            $this->lastId = $result->insertId();

            return $result->affectedRows();
        }

        return 0;
    }

    public function lastInsertId($name = null)
    {
        return $this->lastId;
    }

    private function resultToStatement($result)
    {
        if ($result instanceof CommandResult) {
            return new CommandResultBridge($result);
        }

        return new ResultSetBridge($result);
    }

    public function beginTransaction()
    {
        $this->pool->transaction();
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }

    public function rollBack()
    {
        // TODO: Implement rollBack() method.
    }

    public function errorCode()
    {
        // TODO: Implement errorCode() method.
    }

    public function errorInfo()
    {
        // TODO: Implement errorInfo() method.
    }
}
