<?php declare(strict_types=1);

namespace Finjet\Repositories;

use Finjet\Entities\Category;
use Finjet\Entities\Item;
use Illuminate\Database\Eloquent\Model;

class ItemsRepository extends AbstractEloquentRepository implements ItemsRepositoryInterface
{
    public function getModelClass(): string
    {
        return Item::class;
    }

    public function getAll(): iterable
    {
        return $this->getModelClass()::with('categories')->get();
    }

    public function getByCategory(string $category): iterable
    {
        $category = Category::whereName($category)->with('items.categories')->first();
        if ($category) {
            return $category->items->toArray();
        }

        return [];
    }

    public function create(array $params, $categories = []): ?Model
    {
        $cats = [];

        if (!empty($categories) && is_array($categories)) {
            $cats = Category::whereIn('name', $categories)->get();
        }
        $modelClass = $this->getModelClass();
        /** @var Item $model */
        $model = new $modelClass($params);

        if (!$model->save()) {
            return null;
        }

        if (!empty($cats)) {
            $model->categories()->saveMany($cats);
        }

        return $model;
    }
}