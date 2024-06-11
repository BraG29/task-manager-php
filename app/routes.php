<?php

use App\Interface\UserController;
use Slim\App;

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
    $app->get('/users/{id}', function ($request, $response, $args) use ($userController) {
        $userId = $args['id'];
        $user = $userController->getUser($userId);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
