<?php declare(strict_types=1);

namespace Finjet\Repositories;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepository implements ApiRepositoryInterface
{
    protected Manager $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    abstract public function getModelClass(): string;

    public function getAll(): iterable
    {
        return $this->getModelClass()::all();
    }

    public function update(int $id, array $params): bool
    {
        $model = $this->getModelClass()::find($id);
        if (!$model) {
            return false;
        }

        return $model->update($params);
    }

    public function delete(int $id): bool
    {
        return (bool)$this->getModelClass()::destroy($id);
    }

    public function create(array $params): ?Model
    {
        $modelClass = $this->getModelClass();
        $model = new $modelClass($params);
        if ($model->save()) {
            return $model;
        }

        return null;
    }
}