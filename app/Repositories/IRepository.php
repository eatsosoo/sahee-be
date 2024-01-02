<?php

namespace App\Repositories;

use App\Helpers\Common\MetaInfo;
use App\Helpers\Filters\BasicFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface IRepository
{
    /**
     * get corresponding model class name
     * @return string
     */
    public function getRepositoryModelClass(): string;

    /**
     * search model items using a given basic filter (context filter) or return a query builder
     *
     * @param BasicFilter $filter
     * @param bool $onlyActive
     * @return Builder
     */
    public function search(BasicFilter $filter, bool $onlyActive = true): Builder;

    /**
     * find a single object base on its id
     *
     * @param mixed $id
     * @param string $idColumnName
     * @return mixed
     */
    public function getSingleObject(mixed $id, string $idColumnName = 'id'): mixed;

    /**
     * try to create the object using the given info
     *
     * @param array<string, mixed> $form
     * @param MetaInfo|null $meta
     * @param string $idColumnName
     * @return mixed
     */
    public function create(array $form, MetaInfo $meta = null, string $idColumnName = 'id'): mixed;

    /**
     * try to save the object using the given info
     *
     * @param array<mixed> $form
     * @param MetaInfo|null $meta
     * @param string $idColumnName
     * @return mixed
     */
    public function update(array $form, MetaInfo $meta = null, string $idColumnName = 'id'): mixed;

    /**
     * try to delete a model based on its id
     *
     * @param int|string $id
     * @param bool $soft
     * @param MetaInfo|null $meta
     * @param string $idColumnName
     * @return bool
     */
    public function delete($id, $soft = false, $meta = null, $idColumnName = 'id');

    /**
     * try to restore a model record
     *
     * @param mixed $id
     * @param bool $soft
     * @param MetaInfo|null $meta
     * @param string $idIdColumnName
     * @return bool
     */
    public function restore(mixed $id, bool $soft = false, MetaInfo $meta = null, string $idIdColumnName = 'id'): bool;

    /**
     * try to count matched items based on the given filter
     *
     * @param BasicFilter|null $filter
     * @param bool $onlyActive
     * @return int
     */
    public function count(BasicFilter $filter = null, bool $onlyActive = true): int;

    /**
     * query by applying a filter condition on a field name
     *
     * @param array<mixed>|null $condition
     * @param Builder|null $query
     * @return Builder
     */
    /**
     * query by applying a filter condition on a field name
     *
     * @param  array<string,mixed> $condition
     * @param  Builder $query
     * @param  array<string,mixed> $positionalBindings
     * @return Builder
     */
    public function queryOnAField($condition = null, $query = null, $positionalBindings = null);

    /**
     * add extra relationship field to query
     *
     * @param array<string> $withs
     * @param Builder|null $query
     * @return Builder
     */
    public function withs(array $withs, Builder $query = null): Builder;

    /**
     * delete many
     *
     * @param  int|string $id
     * @param  string $idColumnName
     * @param  bool $force
     * @return bool
     */
    public function deleteMany($id, $idColumnName = 'id', $force = false);
}
