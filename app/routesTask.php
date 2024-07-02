<?php

use App\Interface\Dtos\TaskDTO;
use App\Interface\TaskController;
use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


return function (App $app, TaskController $taskController) {

    //el body tiene que tener un json encoded o un map
    //ORRRR un objeto que implementa Json Serializable
    //hay ejemplos en usuario

    //http://localhost:8080/tasks/create
    $app->post("/tasks/create", function (Request $request, Response $response, $args) use ($taskController) {

        $json = $request->getBody();
        $data = json_decode($json, true);


        $taskDTO = TaskDTO::fromArray($data);


        try {

            $taskId = $taskController->createTask($taskDTO);
            $response->getBody()->write(json_encode(["message"=>"Tarea creada con id: " . $taskId]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        }catch(Exception $e){
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

    });

    //http://localhost:8080/tasks/{id}
    $app->get("/tasks/{id}", function (Request $request, Response $response, $args) use ($taskController) {
        try {

            $task = $taskController->getTaskById($args["id"]);


            $response->getBody()->write(json_encode($task->jsonSerialize()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }catch (Exception $e){
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(204);
        }
    });

    //http://localhost:8080/tasksByProject/{id}
    $app->get("/tasksByProject/{id}", function (Request $request, Response $response, $args) use ($taskController) {

        $userId = $args['id'];
        try {
            $tasks = $taskController->getTasksByProject($userId);

            $response->getBody()->write(json_encode($tasks));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }catch (Exception $e){

            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(204);
        }
    });


    //http://localhost:8080/tasksByUser/{id}
    $app->get("/tasksByUser/{id}", function (Request $request, Response $response, $args) use ($taskController) {

        $userId = $args['id'];
        $json = $request->getBody();
        $data = json_decode($json, true);

        try {
            $tasks = $taskController->getTasksByUser($userId);
            $response->getBody()->write(json_encode($tasks));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }catch (Exception $e){
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(204);

        }
    });


    //http://localhost:8080/tasks/delete
    $app->delete("/tasks/delete", function (Request $request, Response $response, $args) use ($taskController) {

        $params = $request->getQueryParams();
        $userId = $params['userId'];
        $taskId = $params['taskId'];

        try {
            var_dump($taskId, $userId);
            $taskController->deleteTask($taskId, $userId);

            $response->getBody()->write(json_encode(["message"=>"Tarea eliminada con id: " . $data['taskId']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }catch (Exception $e){
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(204);
        }
    });

    $app->put("/tasks/update", function (Request $request, Response $response, $args) use ($taskController) {
        $json = $request->getBody();
        $data = json_decode($json, true);

        $taskDTO = TaskDTO::fromArray($data);

        try {
            $taskController->updateTask($taskDTO, $data['userID']);
            $response->getBody()->write(json_encode(["message"=>"Tarea actualizada con id: " . $data['id']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }catch (Exception $e){
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    });
};
