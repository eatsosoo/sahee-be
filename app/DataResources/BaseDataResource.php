<?php

namespace App\DataResources;

use App\Exceptions\Business\InvalidModelInstanceException;
use App\Models\BaseModel;
use Carbon\Carbon;

abstract class BaseDataResource implements IDataResource
{
    abstract public function modelClass(): string;
    /**
     * @var array| string[]
     */
    protected array $fields = [];

    /**
     * @param array<mixed> $extraFields
     * @throws InvalidModelInstanceException
     */
    public function __construct(mixed $object, array $extraFields = [])
    {
        if (is_null($object)) {
            return;
        }

        // 1. check model class
        $objectClass = get_class($object);
        $modelClass = $this->modelClass();
        if ($modelClass != $objectClass) {
            throw new InvalidModelInstanceException(`Expected type $modelClass but found $objectClass`);
        }

        // 2. copy attributes from model to resource for output
        $this->withFields($extraFields);
        $this->load($object);
    }

    /**
     * dynamic load attributes
     *
     * @param BaseModel $object
     * @return void
     */
    abstract public function load(mixed $object): void;

    /**
     * @param mixed $object
     * @param string[] $props
     * @return void
     */
    protected function copy(mixed $object, array $props = []): void
    {
        if (!isset($object)) {
            return;
        }
        $attributes = array_keys($object->getAttributes());
        $props = (count($props) == 0) ? $this->fields : $props;
        foreach ($props as $key) {
            if (in_array($key, $attributes)) {
                $this->$key = $object[$key];
            }
        }
    }

    /**
     * dynamical register extra field
     *
     * @param string $fieldName
     * @return void
     */
    public function withField(string $fieldName): void
    {
        if (!in_array($fieldName, $this->fields)) {
            $this->fields[] = $fieldName;
        }
    }

    /**
     * Dynamical register extra fields
     * @param array<string> $fields
     * @return void
     */
    public function withFields(array $fields): void
    {
        foreach ($fields as $field) {
            $this->withField($field);
        }
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->fields as $key) {
            if (!property_exists($this, $key)) {
                continue;
            }

            if ($this->$key instanceof Carbon) {
                $result[$key] = $this->$key;
            } elseif (is_array($this->$key) || is_object($this->$key)) {
                $result[$key] = BaseDataResource::objectToArray($this->$key);
            } else {
                $result[$key] = $this->$key;
            }
        }
        return $result;
    }

    /**
     * convert list of model into resources
     *
     * @param mixed $items
     * @param string $className
     * @param array<string> $extraFields
     * @return array<mixed>
     */
    public static function generateResources(mixed $items, string $className, array $extraFields = []): array
    {
        if (is_null($items)) {
            return [];
        }
        $result = [];
        foreach ($items as $item) {
            $object = new $className($item, $extraFields);
            $result[] = $object;
        }
        return $result;
    }

    /**
     * convert resource items into array
     *
     * @param mixed $object
     * @return mixed
     */
    public static function objectToArray(mixed $object): mixed
    {
        if (!is_array($object) && !($object instanceof BaseDataResource)) {
            return $object;
        }
        if ($object instanceof BaseDataResource) {
            return $object->toArray();
        }
        $array = [];
        foreach ($object as $key => $value) {
            $result = null;
            if ($value instanceof BaseDataResource) {
                $result = $value->toArray();
            } else {
                $result = is_array($value) ? BaseDataResource::objectToArray($value) : $value;
            }

            $array[$key] = $result;
        }
        return $array;
    }
}
