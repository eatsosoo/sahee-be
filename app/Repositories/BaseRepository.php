<?php

namespace App\Repositories;

use App\Exceptions\DB\CannotSaveToDBException;
use App\Exceptions\DB\IdIsNotProvidedException;
use App\Exceptions\DB\RecordIsNotFoundException;
use App\Exceptions\DB\RecordIsNotFoundException as DBRecordIsNotFoundException;
use App\Helpers\Common\MetaInfo;
use App\Helpers\Filters\BasicFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

abstract class BaseRepository implements IRepository
{
    /**
     * @param BasicFilter $filter
     * @param Builder|null $query
     * @return Builder
     */
    protected function filter(BasicFilter $filter, Builder $query = null): Builder
    {
        if (is_null($query)) {
            $modelClass = $this->getRepositoryModelClass();
            $query = (new $modelClass())->query();
        }

        if ($filter->conditions && count($filter->conditions) > 0) {
            foreach ($filter->conditions as $value) {
                $query = $this->queryOnAField($value, $query);
            }
        }

        if ($filter->skip != null && $filter->skip > 0) {
            $query = $query->skip($filter->skip);
        }
        if ($filter->limit != null && $filter->limit > 0) {
            $query = $query->take($filter->limit);
        }
        if ($filter->orders) {
            foreach ($filter->orders as $value) {
                $query = $query->orderBy($value[0], $value[1]);
            }
        }
        return $query;
    }

    /**
     * search model items using a given basic filter (context filter) or return a query builder
     *
     * @param BasicFilter|null $filter
     * @param bool $onlyActive
     * @return Builder
     */
    public function search(BasicFilter $filter = null, bool $onlyActive = true): Builder
    {
        if (is_null($filter)) {
            $filter = new BasicFilter();
        }
        $filter->conditions = $filter->conditions === null ? [] : $filter->conditions;
        $modelClass = $this->getRepositoryModelClass();
        $model = new $modelClass();
        $query = $model->query();
        $query = $this->filter($filter, $query);
        if (!$onlyActive) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * find a single object base on its id
     *
     * @param mixed $id
     * @param string $idColumnName
     * @return mixed
     */
    public function getSingleObject(mixed $id, string $idColumnName = 'id'): mixed
    {
        if ($id == null) {
            return null;
        }

        $modelClass = $this->getRepositoryModelClass();
        $query = (new $modelClass())->query();
        $query = $query->where($idColumnName, $id);
        return $query->first();
    }

    /**
     * try to create the object using the given info
     *
     * @param array<string, mixed> $form
     * @param MetaInfo|null $meta
     * @param string $idColumnName
     * @return mixed
     * @throws CannotSaveToDBException
     */
    public function create(array $form, MetaInfo $meta = null, string $idColumnName = 'id'): mixed
    {
        if (in_array($idColumnName, array_keys($form))) {
            unset($form[$idColumnName]);
        }

        $modelClass = $this->getRepositoryModelClass();

        $entity = new $modelClass();
        foreach ($entity->getFillable() as $key) {
            $entity->$key = $form[$key] ?? null;
        }
        $entity->fill($form);
        $entity->setMetaInfo($meta, true);
        $isSaved = $entity->save();
        if ($isSaved) {
            return $entity;
        } else {
            throw new CannotSaveToDBException();
        }
    }

    /**
     * try to save the object using the given info
     *
     * @param array<string, mixed> $form
     * @param MetaInfo|null $meta
     * @param string $idColumnName
     * @return mixed
     * @throws CannotSaveToDBException
     * @throws IdIsNotProvidedException
     * @throws DBRecordIsNotFoundException
     */
    public function update(array $form, MetaInfo $meta = null, string $idColumnName = 'id'): mixed
    {
        if (!in_array($idColumnName, array_keys($form))) {
            throw new IdIsNotProvidedException();
        }
        $entity = $this->getSingleObject($form[$idColumnName], $idColumnName);
        if (isset($entity)) {
            $entity->fill($form);
            $entity->setMetaInfo($meta, false);
            if ($entity->save() !== false) {
                return $entity;
            } else {
                throw new CannotSaveToDBException();
            }
        }
        throw new DBRecordIsNotFoundException();
    }

    /**
     * try to delete a model based on its id
     *
     * @param int|string $id
     * @param bool $soft
     * @param MetaInfo|null $meta
     * @return bool
     * @throws RecordIsNotFoundException
     */
    public function delete($id, $soft = false, $meta = null, $idColumnName = 'id')
    {
        $object = $this->getSingleObject($id, $idColumnName);
        if ($object === null) {
            throw new DBRecordIsNotFoundException();
        }
        if (class_uses($object) && $soft && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($object))) {
            $object->setMetaInfo($meta, false);
            return $object->delete();
        } else {
            return $object->forceDelete();
        }
    }

    /**
     * try to restore a model record
     *
     * @param mixed $id
     * @param bool $soft
     * @param MetaInfo|null $meta
     * @param string $idColumnName
     * @return bool
     */
    public function restore(mixed $id, bool $soft = false, MetaInfo $meta = null, string $idColumnName = 'id'): bool
    {
        $modelClass = $this->getRepositoryModelClass();

        $model = new $modelClass();
        if (class_uses($model) && !in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
            return false;
        }

        return $model::withTrashed()->where($idColumnName, $id)->restore();
    }

    /**
     * try to count matched items based on the given filter
     *
     * @param BasicFilter|null $filter
     * @param bool $onlyActive
     * @return int
     */
    public function count(BasicFilter $filter = null, bool $onlyActive = true): int
    {
        $result = $this->search($filter, $onlyActive)->count();
        return ($result == null) ? 0 : $result;
    }

    /** query by applying a filter condition on a field name
     *
     * @param  array<mixed>|null $condition
     * @param  Builder|QueryBuilder $query
     * @param  array<mixed>|null $positionalBindings
     * @param  string $searchCond
     * @return Builder
     */
    public function queryOnAField($condition = null, $query = null, $positionalBindings = null, $searchCond = 'and')
    {
        $modelClass = $this->getRepositoryModelClass();
        $query = $query ?? (new $modelClass())->query();

        // 1. ensure inputs
        $condition = is_null($condition) ? [] : $condition;
        if (count($condition) == 2) {
            $newCondition = [$condition[0], '=', $condition[1]];
            return $this->queryOnAField($newCondition, $query, $positionalBindings);
        } elseif (count($condition) != 3) {
            return $query;
        }

        // 2. applying condition
        if (is_null($positionalBindings) || count($positionalBindings) == 0) {
            return $query->whereRaw($condition[0] . ' ' . $condition[1] . ' ?', $condition[2]);
        } else {
            return $query->whereRaw($condition[0] . ' ' . $condition[1] . ' ' . $condition[2], $positionalBindings);
        }
    }

    /**
     * add extra relationship field to query
     *
     * @param array<string> $withs
     * @param Builder|null $query
     * @return Builder
     */
    public function withs(array $withs, Builder $query = null): Builder
    {
        $modelClass = $this->getRepositoryModelClass();
        $query = $query ?? (new $modelClass())->query();
        foreach ($withs as $with) {
            $query = $query->with($with);
        }
        return $query;
    }

    /**
     * is id exists
     *
     * @param  int|string $id
     * @param  string $idColumnName
     * @return bool|null
     */
    public function isIdExists($id, $idColumnName = 'id')
    {
        if ($id == null) {
            return null;
        }

        $modelClass = $this->getRepositoryModelClass();
        $query = (new $modelClass())->query();
        $query = $query->where($idColumnName, $id);
        return $query->exists();
    }

    /**
     * delete many
     *
     * @param  int|string $id
     * @param  string $idColumnName
     * @param  bool $force
     * @return bool
     */
    public function deleteMany($id, $idColumnName = 'id', $force = false)
    {
        $modelClass = $this->getRepositoryModelClass();
        $query = (new $modelClass())->query();
        $query = $query->where($idColumnName, $id);
        if ($force) {
            return $query->forceDelete();
        } else {
            return $query->delete();
        }
    }
}
