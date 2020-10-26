<?php declare(strict_types=1);

namespace Finjet\Repositories;

use Finjet\Entities\Category;


class CategoriesRepository extends AbstractEloquentRepository implements CategoriesRepositoryInterface
{
    public function getModelClass(): string
    {
        return Category::class;
    }
}