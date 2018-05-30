<?php

declare(strict_types=1);

namespace App\Connection;

use function Amp\GreenThread\await;
use Amp\Mysql\ResultSet;
use Doctrine\DBAL\Driver\ResultStatement as DoctrineStatement;
use Amp\Mysql\ResultSet as AmpResultSet;
use Doctrine\DBAL\FetchMode;
use Traversable;

class ResultSetBridge implements \IteratorAggregate, DoctrineStatement
{
    const FETCH_MODE_MAPPING = [
        FetchMode::ASSOCIATIVE => ResultSet::FETCH_ASSOC,
        FetchMode::NUMERIC => ResultSet::FETCH_ARRAY,
        FetchMode::MIXED => ResultSet::FETCH_ASSOC,
    ];

    private $resultSet;
    private $fetchMode;
    private $results;

    public function __construct(AmpResultSet $resultSet, $fetchMode = FetchMode::ASSOCIATIVE)
    {
        $this->resultSet = $resultSet;
        $this->fetchMode = $fetchMode;
    }

    public function closeCursor()
    {
        unset($this->resultSet);
    }

    public function columnCount()
    {
        return \count(await($this->resultSet->getFields()));
    }

    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
       $this->fetchMode = $fetchMode;
    }

    public function fetch($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return next($this->getIterator());
    }

    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        if ($this->results) {
            return $this->results;
        }

        $fetchMode = self::FETCH_MODE_MAPPING[$fetchMode ?? $this->fetchMode];
        $this->results = new \ArrayObject();

        while (await($this->resultSet->advance($fetchMode))) {
            $this->results[] = $this->resultSet->getCurrent();
        }

        return $this->results;
    }

    public function fetchColumn($columnIndex = 0)
    {
        // TODO: Implement fetchColumn() method.
    }

    public function getIterator()
    {
        if (!$this->results) {
            $this->fetchAll();
        }

        return $this->results;
    }
}
