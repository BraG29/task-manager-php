<?php

use App\Application\Middlewares\JwtMiddleware;
use App\Interface\UserController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

use App\Interface\Dtos\UserDTO;

return function (App $app, UserController $userController) {
    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write('Hello World!');
        return $response;

    })->add(JwtMiddleware::class);

    $app->post('/login', function (Request $request, Response $response, $args) use ($userController) {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];
        $user = $userController->signIn($email, $password);

        if($user){
            if(!$user->isVerified()){
                $response->getBody()->write(json_encode(['error' => 'Para poder iniciar sesion debes verificarte.']));

            }else{
                $generateJwt = require __DIR__ . '/generateJwt.php';
                $response->getBody()->write(json_encode(['token' => $generateJwt($user)]));

                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
        }else{
            $response->getBody()->write(json_encode(['error' => 'Credenciales incorrectas']));
        }
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    });

//    Example: http://localhost:8080/users
    $app->get('/users', function (Request $request, Response $response, $args) use ($userController) {
        $users = $userController->getUsers();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    });
    
//    Example: http://localhost:8080/users/1
    $app->get('/users/{id}', function (Request $request, Response $response, $args) use ($userController) {
        $userId = $args['id'];
        $user = $userController->getUser($userId);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/users', function (Request $request, Response $response, $args) use ($userController) {
        $data = $request->getParsedBody();
        $userDto = UserDTO::fromArray($data);

        $userId = $userController->registerUser($userDto);

        if($userId == 0){
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);

        }else{
            $response->getBody()->write("Usuario creado con id: " . $userId);
            return $response;
        }
    });

    $app->get('/verifyEmail', function (Request $request, Response $response, $args) use ($userController) {

        //parametros en el link.
        $params = $request->getQueryParams();
        $id = $params['userId'];

        $verification = $userController->verifyEmail($id);

        if($verification){
            $response->getBody()->write("Correo verificado con exito.");
            return $response;
        }else{
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);

        }
    });
};
