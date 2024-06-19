<?php

namespace App\Application\Middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as Res;

class JwtMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = new Res();

        $authHeader = $request->getHeader('Authorization');
        $jwt = $authHeader ? explode(' ', $authHeader[0])[1] : '';

        if($jwt){
            try{
                $config = file_get_contents(__DIR__ . "/../../../config.json");
                $config = json_decode($config, true);

                $secretKey = $config['api']['JWT_SECRET'];
                $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

                $request = $request->withAttribute('user', $decoded);

                return $handler->handle($request);

            }catch (Exception $e){
                $response->getBody()->write(json_encode([
                    'error' => 'Token inválido',
                    'message' => $e->getMessage()
                ]));

            }

        }else{
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));

        }

        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}