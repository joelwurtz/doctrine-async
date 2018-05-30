<?php

declare(strict_types=1);

namespace App\Connection;

use function Amp\GreenThread\await;
use Amp\Mysql\CommandResult;
use Amp\Mysql\Statement as AMPStatement;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Driver\Statement as DoctrineStatement;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Traversable;

class StatementBridge implements \IteratorAggregate, DoctrineStatement
{
    private $ampStatement;
    private $resultSet;
    private $fetchMode = FetchMode::ASSOCIATIVE;

    public function __construct(AMPStatement $statement)
    {
        $this->ampStatement = $statement;
    }

    public function closeCursor()
    {
        $this->ampStatement->close();
    }

    public function columnCount()
    {
        return \count(await($this->ampStatement->getFields()));
    }

    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        if ($this->resultSet) {
            $this->resultSet->setFetchMode($fetchMode, $arg2, $arg3);
        }

        $this->fetchMode = $fetchMode;
    }

    public function fetch($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        $this->getResultSet()->fetch($fetchMode, $cursorOrientation, $cursorOffset);
    }

    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        $this->getResultSet()->fetch($fetchMode, $fetchArgument, $ctorArgs);
    }

    public function fetchColumn($columnIndex = 0)
    {
        $this->getResultSet()->fetchColumn($columnIndex);
    }

    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        // Doctrine Start at 1, Amp at 0
        if (\is_int($param)) {
            --$param;
        }

        $this->ampStatement->bind($param, $value);
    }

    public function bindParam($column, &$variable, $type = ParameterType::STRING, $length = null)
    {
        if (\is_int($column)) {
            --$column;
        }

        $this->ampStatement->bind($column, $variable);
    }

    public function errorCode()
    {
    }

    public function errorInfo()
    {
    }

    public function execute($params = null)
    {
        $this->getResultSet();
    }

    public function rowCount()
    {
        return \count($this->getResultSet()->fetchAll());
    }

    private function getResultSet(): ResultStatement
    {
        if (!$this->resultSet) {
            $result = await($this->ampStatement->execute());

            if ($result instanceof CommandResult) {
                $this->resultSet = new CommandResultBridge($result);
            } else {
                $this->resultSet = new ResultSetBridge($result, $this->fetchMode);
            }
        }

        return $this->resultSet;
    }

    public function getIterator()
    {
        return $this->getResultSet();
    }
}
