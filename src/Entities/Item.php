<?php declare(strict_types=1);


namespace Finjet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @param int $id
 * @param string $name
 */
class Item extends Model
{
    protected $table = 'items';
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['pivot'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_to_item');
    }
}
