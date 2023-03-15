<?php

namespace GeekBrains\LevelTwo\Http\Actions\Auth;

use DateTimeImmutable;
use GeekBrains\LevelTwo\Blog\AuthToken;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class LogOut implements ActionInterface
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function handle(Request $request): Response 
    {
        $container = require 'bootstrap.php';
        $logger = $container->get(LoggerInterface::class);

        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $token = $request->query('token');
        } catch (HttpException $e) {
            $logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $this->authTokensRepository->get($token);

        $updatedToken = new AuthToken(
            $token,
            $user->uuid(),
            (new DateTimeImmutable())->modify('-1 day')
        );
        $this->authTokensRepository->save($updatedToken);

        return new SuccessfulResponse([
            'token' => $updatedToken->token(),
        ]);
	}
}
