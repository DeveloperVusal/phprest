<?php

namespace Core\Engine;

abstract class BaseModel {
    protected string $primaryKey = 'id';
    protected string $keyType = 'BIGINT';
    protected bool $autoIncrement = true;
    protected bool $timestamps = true;

    protected string $dbCoon = 'mysql';

    protected array $columns = [];

    private object $resultOne;
    private object $resultAll;

    function __construct(mixed $value = null, string $columnName = 'id')
    {
        if ($value == null) {
            $this->findModelAll();
        } else {
            $this->findModelFirst($columnName, $value);
        }
    }

    private function findModelAll(string $columnName = 'id', mixed $value = null)
    {
        return '0';
    }

    private function findModelFirst(string $columnName = 'id', mixed $value = null)
    {
        return '0';
    }

    private function buildColumns()
    {
        $cols = [
            $this->primaryKey => ['type' => $this->keyType, 'auto_increment' => $this->autoIncrement],
        ];

        foreach ($this->columns as $key => $value) {
            $cols[$key] = $value;
        }

        if ($this->timestamps) {
            $cols['updated_at'] = ['type' => 'DATETIME', 'default' => 'NULL'];
            $cols['created_at'] = ['type' => 'DATETIME', 'default' => 'NULL'];
        }
    }

    private function silentCreate()
    {
        
    }
}