<?php

use App\Application\Middlewares\JwtMiddleware;
use App\Domain\Entities\Enums\RoleType;
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
        $json = $request->getBody();
        $data = json_decode($json, true);
        $email = $data['email'];
        $password = $data['password'];
        $user = $userController->signIn($email, $password);

        if($user){
            if(!$user->isVerified()){
                $response->getBody()->write(json_encode(['error' => 'Para poder iniciar sesion debes verificarte.']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');

            }else{
                $generateJwt = require __DIR__ . '/generateJwt.php';
                $response->getBody()->write(json_encode(['token' => $generateJwt($user)]));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
        }else{
            $response->getBody()->write(json_encode(['error' => 'Credenciales incorrectas']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

//    Example: http://localhost:8080/users
    $app->get('/users', function (Request $request, Response $response, $args) use ($userController) {
        $users = $userController->getUsers();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/usersByProject/{id}', function (Request $request, Response $response, $args) use ($userController) {
        $id = $args['id'];
        try{

            $projects = $userController->getUsersByProject($id);
            $response->getBody()->write(json_encode($projects));
            return $response->withHeader('Content-Type', 'application/json');

        }catch(Exception $e){
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }


    });
    
//    Example: http://localhost:8080/users/1
    $app->get('/users/{id}', function (Request $request, Response $response, $args) use ($userController) {
        $userId = $args['id'];
        $user = $userController->getUser($userId);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/users', function (Request $request, Response $response, $args) use ($userController) {
        $json = $request->getBody();
        $data = json_decode($json, true);
        $userDto = UserDTO::fromArray($data);

        try{
            $userId = $userController->registerUser($userDto);

            if($userId == 0){
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);

            }else{
                $response->getBody()->write(json_encode("Usuario creado con id: " . $userId));
                return $response;
            }
        }catch (Exception $e){
            $errorData = ['error' => $e->getMessage()];
            $response->getBody()->write(json_encode($errorData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    $app->get('/verifyEmail', function (Request $request, Response $response, $args) use ($userController) {

        //parametros en el link.
        $params = $request->getQueryParams();
        $id = $params['userId'];
        $verification = $userController->verifyEmail($id);

        if($verification){

            $config = file_get_contents(__DIR__ . "/../config.json");
            $config = json_decode($config, true);
            $frontUrl = $config['api']['frontUrl'];

            $url = $frontUrl."/login";
            $sleep = 5000;
            $htmlPage = file_get_contents(__DIR__ . '/../public/pages/loginRedirect.html');
            $html = str_replace(['{url}', '{sleep}'], [$url, $sleep], $htmlPage);
            $response->getBody()->write($html);

            return $response->withHeader('Content-Type', 'text/html');
        }else{
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    $app->post('/sendInvitation', function (Request $request, Response $response, $args) use ($userController) {

        $json = $request->getBody();
        $params = json_decode($json, true);

        $projectId = $params['projectId'];
        $userEmail= $params['invitedEmail'];
        $ownerId = $params['ownerId'];
        $role = $params['role'];

        try{

            $role = RoleType::from((int)$role);
            $userController->inviteUserToProject($ownerId, $userEmail, $projectId, $role);
            $response->getBody()->write(json_encode("Correo de invitacion enviado con exito"));
            return $response;

        }catch(Exception $e){
            $errorData = ['error' => $e->getMessage()];
            $response->getBody()->write(json_encode($errorData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

    });

    $app->get('/linkUser', function (Request $request, Response $response, $args) use ($userController) {

        $params = $request->getQueryParams();

        try{
            $projectId = $params['projectId'];
            $userEmail= $params['invitedEmail'];
            $ownerId = $params['userOwnerId'];
            $roleName =  isset($params['role']['value']) ? intval($params['role']['value']) : null;
            $action = $params['action'];

            $role = RoleType::from($roleName);

            if($action == 'accepted'){
                $userController->linkUserToProject(intval($ownerId), $userEmail, intval($projectId), $role);
                $response->getBody()->write(json_encode(["Usuario vinculado con exito."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }else{
                $response->getBody()->write(json_encode("El usuario rechazado la invitacion."));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        }catch (Exception $e){
            $errorData = ['error' => $e->getMessage()];
            $response->getBody()->write(json_encode($errorData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

    });


    $app->post('/updateRole', function (Request $request, Response $response, $args) use ($userController) {

        $params = $request->getParsedBody();

        try{
            $projectId = $params['projectId'];
            $userId= $params['userId'];
            $roleName = $params['role'];

            $role = RoleType::from($roleName);
            $userController->updateRole($projectId, $role, $userId);
            $response->getBody()->write(json_encode(["Usuario vinculado con exito."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }catch(Exception $e){
            $errorData = ['error' => $e->getMessage()];
            $response->getBody()->write(json_encode($errorData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    $app->put('/updateUser', function (Request $request, Response $response, $args) use ($userController) {

        try {
            $json = $request->getBody();
            $data = json_decode($json, true);
            $userDTO = UserDTO::fromArray($data);
            $userController->updateUser($userDTO);

            $response->getBody()->write(json_encode(["message" => "Usuario actualizado con éxito."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

};
