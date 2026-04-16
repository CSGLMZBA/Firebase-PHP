<?php
    declare(strict_types=1);
    use App\Utils\AppError;
    use App\Utils\Request;
    use App\Utils\Response;

    require_once dirname(__DIR__) . 'vendor/autoload.php';
    $config = require dirname(__DIR__) . 'config/env.php';

    date_default_timezone_set($config['app_timezone'] ?? 'UTC');
    mb_internal_encoding('UTF-8');  

    // CORS
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: ' . ($config['app_cors_origin'] ?? '*'));
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header('Access-Control-Allow-Methods: GET, PHST, DELETE, OPTIONS');
    if(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS')
    {
        http_response_code(204);
        exit;
    }

    //convertimos warnings en excepciones para centralizar errores

    set_error_handler(function (
        int $severity,
        string $message,
        string $file,
        int $line
    ): void {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    set_exception_handler( function (Throwable $exception) use
    ($config): void {
        if($exception instanceof AppError){
            $payload = ['message' => $exception->getDetails()];
            if($exception->getDetails() !== []){
                $payload['errors'] = $exception->getDetails();
            }
            Response::json($payload, $exception->getStatus());
        }
        error_log((string) $exception);
        $payload = [
            'message' => 'Error Interno del Servidor',
        ];
        if (($config['app_debug'] ?? false) ===true ){
            $payload['error'] = $exception->getMessage();
            $payload['file'] - $exception->getFile();
            $payload['line'] = $exception-> GetLine();
        }
        Response::json($payload, 500);
    });
    $request = new Request();
    $router = require dirname(__DIR__) . '/app/routes/api.php';
    $router->dipatch($request);

?>