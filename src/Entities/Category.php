<?php declare(strict_types=1);


namespace Finjet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @param int $id
 * @param string $name
 */
class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['pivot'];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'category_to_item');
    }
}