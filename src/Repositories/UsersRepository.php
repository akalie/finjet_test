<?php declare(strict_types=1);

namespace Finjet\Repositories;

use Finjet\Entities\User;

class UsersRepository extends AbstractEloquentRepository implements UsersRepositoryInterface
{
    public function getModelClass(): string
    {
        return User::class;
    }

    public function getByLoginAndHash(string $login, string $passHash): ?User
    {
        return User::where([
            'login' => $login,
            'pass_hash' => $passHash,
        ])->first();
    }

    public function getByToken(string $token): ?User
    {
        return User::where([
            ['token', '=', $token],
            ['token_expires_at', '>', date('Y-m-d H:i:s')],
        ])->first();
    }
}