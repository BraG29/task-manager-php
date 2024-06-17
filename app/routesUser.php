<?php

use App\Interface\UserController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

use App\Interface\Dtos\UserDTO;

return function (App $app, UserController $userController) {
    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write('Hello World!');
        return $response;
    });

//    Example: http://localhost:8080/users
    $app->get('/users', function ($request, $response, $args) use ($userController) {
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
};
