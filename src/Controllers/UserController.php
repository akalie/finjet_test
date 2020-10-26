<?php declare(strict_types=1);

namespace Finjet\Controllers;

use Finjet\Entities\User;
use Finjet\ErrorJsonResponse;
use Finjet\Repositories\UsersRepositoryInterface;
use Finjet\ResultJsonResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class UserController extends ApiController
{
    // actually it should be stored somewhere safe
    protected const SOME_SALT_FROM_OPTIONS = 'dsfasfasdfasdfasfasdfasdfaf';

    public function __construct(LoggerInterface $logger, UsersRepositoryInterface $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function auth(ServerRequest $request): JsonResponse
    {
        if ($errors = $this->validateFields($request->getParsedBody())) {
            return new ErrorJsonResponse($errors);
        }

        $login = trim($request->getParsedBody()['login'] ?? '');
        $pass = $request->getParsedBody()['pass'] ?? '';
        $hash = password_hash($pass, PASSWORD_DEFAULT, ['salt' => self::SOME_SALT_FROM_OPTIONS]);
        $user = $this->repository->getByLoginAndHash($login, $hash);

        if (!$user) {
            return new ErrorJsonResponse([
                'auth' => 'No such user'
            ]);
        }

        $user->token = Uuid::uuid4();
        $expiresAt = (new \DateTime())
            ->modify('+' . User::TOKEN_EXPIRATION_TIME)
            ->format('Y-m-d H:i:s');

        $res = $this->repository->update($user->id, [
            'token' => $user->token,
            'token_expires_at' => $expiresAt,
        ]);

        if (!$res) {
            return new ErrorJsonResponse([
                'auth' => 'Something went wrong'
            ], 500);
        }

        return new ResultJsonResponse([
            'token' => $user->token,
            'expires_at' => $expiresAt,
        ]);
    }

    function validateFields(array $attrs): array
    {
        $errors = [];

        if (!preg_match('/^[a-zA-Z0-9]+$/', trim($attrs['login']))) {
            $errors[] = 'Wrong login format';
        }

        return $errors;
    }
}