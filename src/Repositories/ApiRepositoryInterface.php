<?php declare(strict_types=1);


namespace Finjet\Repositories;


use Illuminate\Database\Eloquent\Model;

interface ApiRepositoryInterface
{
    public function getAll(): iterable;

    public function update(int $id, array $params): bool;

    public function delete(int $id): bool;

    public function create(array $params): ?Model;
}