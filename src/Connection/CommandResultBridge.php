<?php

declare(strict_types=1);

namespace App\Connection;

use Amp\Mysql\CommandResult as AmpCommandResult;
use Doctrine\DBAL\Driver\ResultStatement as DoctrineStatement;

class CommandResultBridge implements \Iterator, DoctrineStatement
{
    private $commandResult;

    public function __construct(AmpCommandResult $commandResult)
    {
        $this->commandResult = $commandResult;
    }

    public function current()
    {
        return $this->commandResult->affectedRows();
    }

    public function next()
    {
        return false;
    }

    public function key()
    {
        return null;
    }

    public function valid()
    {
        return false;
    }

    public function rewind()
    {
    }

    public function closeCursor()
    {
    }

    public function columnCount()
    {
        return 0;
    }

    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
    }

    public function fetch($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return $this->commandResult->affectedRows();
    }

    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        return [];
    }

    public function fetchColumn($columnIndex = 0)
    {
        return $this->commandResult->affectedRows();
    }

}
