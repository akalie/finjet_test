<?php declare(strict_types=1);


namespace Finjet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @param int $id
 * @param string $login
 * @param string $pass_hash
 * @param string $token
 * @param string $token_expires_at
 *
 */
class User extends Model
{
    public const TOKEN_EXPIRATION_TIME = '1 month';

    protected $table = 'users';
    // not really safe
    protected $guarded = [];
    public $timestamps = false;

    protected $hidden = [
        'pass_hash', 'token', 'token_expires_at'
    ];
}