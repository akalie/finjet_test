<?php


namespace Finjet\Repositories;


use Finjet\Entities\User;

interface UsersRepositoryInterface extends ApiRepositoryInterface
{
    public function getByLoginAndHash(string $login, string $passHash): ?User;

    public function getByToken(string $token): ?User;
}