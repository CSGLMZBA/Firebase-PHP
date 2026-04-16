<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Utils\AppError;
use App\Utils\Jwt;
use App\Utils\Request;

class AuthMiddleware
{
    public function __construct(private array $config)
    {
    }

    public function handle(Request $request): void
    {
        $authorization = (string) $request->header('authorization', '');

        if ($authorization === '' || !preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            throw new AppError('No autorizado', 401);
        }

        $token = trim($matches[1]);
        $payload = Jwt::decode($token, $this->config['jwt_secret']);

        $request->setUser($payload);
    }
}