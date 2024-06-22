<?php

use App\Interface\Dtos\ProjectDTO;
use App\Interface\ProjectController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

return function (App $app, ProjectController $projectController) {

    $app->post('/CreateProject',
        function (Request $request, Response $response, $args) use ($projectController) {

            $json = $request->getBody();
            $data = json_decode($json, true);

            $projectDto = ProjectDTO::fromArray($data);
            $projectId = $projectController->createProject($projectDto, $data['userId']);
            if ($projectId == 0) {
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            } else {
                $response->getBody()->write(json_encode("Proyecto creado con id: " . $projectId));
                return $response;
            }
        });
};
