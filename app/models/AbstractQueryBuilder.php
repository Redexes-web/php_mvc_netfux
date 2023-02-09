<?php

namespace App\Models;

use Lib\Utils;

abstract class AbstractQueryBuilder
{
    protected function getQueryColumnFromArray($columnNames)
    {
        $result = "";
        foreach ($columnNames as $columnName) {
            if ($columnName == end($columnNames))
                $result .= "`$columnName`";
            else
                $result .= "\`$columnName\`, ";
        }
        return $result;
    }
    protected function buildQuery(string $operation, string $tableName, array $properties = [])
    {
        switch ($operation) {
            case 'create':
                return $this->buildInsertQuery($tableName, $properties);
                break;
            case 'select':
                return $this->buildSelectQuery($tableName, $properties);
                break;
            case 'delete':
                return $this->buildDeleteQuery($tableName);
                break;

            default:
                throw new \Exception("Operation $operation not found", 1);

                break;
        }
    }
    private function buildSelectQuery($tableName, $whereParameters = [])
    {
        $query = 'SELECT * FROM ' . $tableName . ' WHERE ';
        $i = 0;
        foreach ($whereParameters as $property => $value) {
            $query .= '`' . $property . '` = :' . $property;
            if ($i + 1 != count($whereParameters)) {
                $query .= ' AND ';
            }
            $i++;
        }
        empty($whereParameters) ? $query .= ' 1' : null;
        // SELECT *
        // FROM table
        // WHERE condition
        // GROUP BY expression
        // ORDER BY expression
        // LIMIT count
        return $query;
    }

    private function buildInsertQuery($tableName, $properties)
    {
        $result = '';
        $result .= "REPLACE INTO `" . $tableName . "`";
        $result .= $this->buildColumnsString($properties);
        $result .= 'VALUES';
        $result .= $this->buildParametersString($properties);
        return $result;
    }
    private function buildDeleteQuery(string $tableName)
    {
        return "DELETE FROM `$tableName`
        WHERE id = :id";
    }

    protected function buildParameterArrayToExecute(array $array): array
    {
        // Utils::dd($array);
        $res = [];
        foreach ($array as $key => $value) {
            if ($value != null && !is_array($value) && !is_object($value)) {
                $res[':' . $key] = $value;
            }
        }
        return $res;
    }
    private function buildColumnsString($properties)
    {
        $i = 0;
        $result = '';
        $properties = Utils::removeEmptyValuesInArray($properties);

        foreach ($properties as $key => $property) {
            if ($property == null || is_array($property) || is_object($property)) {
                continue;
            }
            if ($i == 0) {
                $result .= '(';
            }
            $result .= "`" . $key . ($i == (count($properties) - 1) ? "`)" : "`,");
            $i++;
        }
        return $result;
    }
    private function buildParametersString($properties)
    {
        $j = 0;
        $result = '';
        $properties = Utils::removeEmptyValuesInArray($properties);
        foreach ($properties as $key => $property) {
            if ($property == null || is_array($property) || is_object($property)) {
                continue;
            }
            if ($j == 0) {
                $result .= '(';
            }
            $result .= ':' . $key . ($j == (count($properties) - 1) ? ")" : ",");
            $j++;
        }
        return $result;
    }
}
