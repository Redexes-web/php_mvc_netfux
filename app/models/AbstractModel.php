<?php

namespace App\Models;

use App\Entity\AbstractEntity;
use App\Entity\EntityInterface;
use PDO;
use Exception;
use Lib\Utils;
use Lib\Database;

abstract class AbstractModel extends AbstractQueryBuilder implements ModelInterface
{
    protected $db;
    protected $database;
    protected $entityName;
    public function __construct()
    {
        $this->database = new Database;
        $this->db = (new Database)->getPdo();
    }
    public function find(int $id, $hydrate = true): object
    {
        $tmpEntityNameArray = explode('\\', $this->entityName);
        $entity = strtolower(end($tmpEntityNameArray));
        $req = $this->db->prepare($this->buildQuery('select', $entity, ["id" => $id]));
        $req->execute(['id' => $id]);
        $objectName = "App\\Entity\\" . $this->entityName;
        $result = $req->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception("$this->entityName not found");
        }
        $r =  ($this->getEntityFromArray($this->entityName, $result));
        if ($hydrate) {
            if (!empty($this->entityName::MANY_TO_ONE)) {
                foreach ($this->entityName::MANY_TO_ONE as $fieldToHydrate => $foreignEntityName) {
                    $r = $this->hydrate(Utils::camelCaseToSnakeCase($fieldToHydrate), $r, $foreignEntityName);
                }
            }
            if (!empty($this->entityName::ONE_TO_MANY)) {
                foreach ($this->entityName::ONE_TO_MANY as $fieldToHydrate => $foreignEntityName) {
                    try {
                        //code...
                        $r = $this->hydrate(Utils::camelCaseToSnakeCase($fieldToHydrate), $r, $foreignEntityName, 'ONE_TO_MANY');
                    } catch (\Throwable $th) {
                        //throw $th;
                        // (Utils::dd([Utils::camelCaseToSnakeCase($fieldToHydrate), $r, $foreignEntityName]));
                    }
                }
            }
        }
        return $r;
    }
    public function findBy(array $parameters, bool $hydrate = false, $entityName = null): ?array
    {
        $entityName = $entityName ?? $this->entityName;
        $tmpEntityNameArray = explode('\\', $entityName);
        $tableName = Utils::camelCaseToSnakeCase(end($tmpEntityNameArray));
        $req = $this->db->prepare($this->buildQuery('select', $tableName, $parameters));
        $req->execute($this->buildParameterArrayToExecute($parameters));
        $queryResults = $req->fetchAll(PDO::FETCH_ASSOC);
        if (empty($queryResults)) {
            return [];
        }
        $results = [];
        foreach ($queryResults as $key => $result) {
            $r = $this->getEntityFromArray($entityName, $result);
            if ($hydrate) {
                if (!empty($this->entityName::MANY_TO_ONE)) {
                    foreach ($this->entityName::MANY_TO_ONE as $fieldToHydrate => $foreignEntityName) {
                        $r = $this->hydrate(Utils::camelCaseToSnakeCase($fieldToHydrate), $r, $foreignEntityName);
                    }
                }
                if (!empty($this->entityName::ONE_TO_MANY)) {
                    foreach ($this->entityName::ONE_TO_MANY as $fieldToHydrate => $foreignEntityName) {
                        $r = $this->hydrate(Utils::camelCaseToSnakeCase($fieldToHydrate), $r, $foreignEntityName, 'ONE_TO_MANY');
                    }
                }
            }
            $results[] = $r;
        }
        return $results;
    }


    public function add(EntityInterface $entity)
    {
        $entity = $this->removeRelationshipPropertiesInEntity($entity);
        $propertyArray = $this->getArrayFromEntity($entity);
        $propertyArray = Utils::arrayCamelCaseToSnakeCase($propertyArray, true);
        $classArray = explode('\\', get_class($entity));
        $entityName = end($classArray);
        $tableName =  Utils::camelCaseToSnakeCase(lcfirst($entityName));
        $query = $this->buildQuery('create', $tableName, $propertyArray);

        $req = $this->db->prepare($query);
        $req->execute($this->buildParameterArrayToExecute($propertyArray));
        return $this->db->lastInsertId();
    }
    public function remove(object $entity)
    {

        $propertyArray = $this->getArrayFromEntity($entity);
        $propertyArray = Utils::arrayCamelCaseToSnakeCase($propertyArray, true);
        $classArray = explode('\\', get_class($entity));
        $entityName = end($classArray);
        $tableName =  Utils::camelCaseToSnakeCase(lcfirst($entityName));
        $query = $this->buildQuery('delete', $tableName);
        $req = $this->db->prepare($query);
        $req->execute(["id" => $entity->get('id')]);
        return $req;
    }
    protected function getEntityFromArray($entityClassName, array $entityArray)
    {
        $entity = new $entityClassName();
        foreach ($entityArray as $property => $value) {
            $property = Utils::snakeToCamelCase($property);
            if (substr($property, -2) === "At") {
                $value = $this->convertStringToDateTime($value);
            }
            $entity->set($property, $value);
        }
        return $entity;
    }
    protected function getArrayFromEntity(EntityInterface $entity)
    {
        $array = [];
        if(method_exists($entity, 'toArray')){
            $entity = $entity->toArray();
        }
        foreach ($entity as $property => $value) {
            if (substr($property, -2) === "At") {
                if (is_string($value)) {
                    $value = $this->convertStringToDateTime($value);
                }
                $array[$property] = $this->convertDateTimeToString($value);
            } else {
                $array[$property] = $value;
            }
        }
        return $array;
    }


    protected function convertDateTimeToString(?\DateTime $date)
    {
        return $date ? $date->format('Y-m-d H:i:s') : null;
    }
    protected function convertStringToDateTime(?string $dateString)
    {
        return $dateString ? new \DateTime($dateString) : null;
    }

    protected function hydrate($fieldToHydrate, $entityToHydrate, $foreignEntityName, $relationType = "MANY_TO_ONE")
    {
        if ($relationType === "MANY_TO_ONE") {
            $classArray = explode('\\', get_class($entityToHydrate));
            $entityName = end($classArray);
            $tableName =  Utils::camelCaseToSnakeCase(lcfirst($entityName));
            $foreignField = $tableName . "_id";
            $results = $this->findBy([$foreignField => $entityToHydrate->get("id")], false, $foreignEntityName);
            $entityToHydrate->$fieldToHydrate = [];
            foreach ($results as $result) {
                $entityToHydrate->$fieldToHydrate[] = $result;
            }
        } else {
            if ($entityToHydrate->get(Utils::snakeToCamelCase($fieldToHydrate)) === null) {
                # code...
            } else {
                $results = $this->findBy(['id' => $entityToHydrate->get(Utils::snakeToCamelCase($fieldToHydrate))], false, $foreignEntityName);
                isset($results[0]) ? $entityToHydrate->set(substr(Utils::snakeToCamelCase($fieldToHydrate), 0, -2), $results[0]) : null;
            }
        }
        return $entityToHydrate;
    }

    private function removeRelationshipPropertiesInEntity(EntityInterface $entity)
    {
        foreach ($entity::MANY_TO_ONE as $key => $value) {
            $entity->set($key, null);
        }
        foreach ($entity::ONE_TO_MANY as $key => $value) {
            if (is_object($value)) {
                $entity->set($key, $value->get('id'));
            }
        }
        return $entity;
    }
}
