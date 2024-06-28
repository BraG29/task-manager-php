<?php

use App\Interface\Dtos\ProjectDTO;
use App\Interface\ProjectController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

return function (App $app, ProjectController $projectController) {

    // create project JSON example
    /*
    {
        "id" : 0, // leave as 0
        "name" : "Example Project",
        "description" : "Example Description",
        "state" : "ACTIVE",
        "userId" : 1, //replace your user id here,
    }
    */
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


    $app->get('/GetProjectDataByUser/{userId}',
        function (Request $request, Response $response, $args) use ($projectController) {
            $userId = $args['userId'];
            $project = $projectController->getProjectDataByUser($userId);
            if ($project == null) {
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            } else {
                $response->getBody()->write(json_encode($project));
                return $response;
            }
        }
    );

    //Edit project JSON example
    /*
     *
     {
        "name" : "Example Project",
        "description" : "Example Description",
     }
     *
     */

    // this should only edit the Project Information such as name or description
    $app->put('/UpdateProject/{projectId}', function (Request $request, Response $response, $args) use ($projectController) {
        $json = $request->getBody();
        $data = json_decode($json, true);


        $projectDto = new ProjectDTO(
            id: $args['projectId'],
            name: $data['name'],
            description: $data['description'],
            state: $data['state'] //idk why its needed but its needed, should do nothing anyway
        );

        $updatedProjectId = $projectController->editProject($projectDto);

        if ($updatedProjectId == 0) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        } else {
            $response->getBody()->write(json_encode("Project updated with ID: " . $updatedProjectId));
            return $response;
        }
    });


    $app->delete('/DeleteProject/{projectId}', function (Request $request, Response $response, $args) use ($projectController) {
        $projectId = $args['projectId'];

        $deletedProjectId = $projectController->deleteProject($projectId);

        if ($deletedProjectId == 0) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        } else {
            $response->getBody()->write(json_encode("Project deleted with ID: " . $deletedProjectId));
            return $response;
        }
    });

    $app->get('/GetProjectData/{projectId}',
        function (Request $request, Response $response, $args) use ($projectController) {
            $projectId = $args['projectId'];
            $project = $projectController->getProjectData($projectId);
            if ($project == null) {
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            } else {
                $response->getBody()->write(json_encode($project));
                return $response;
            }
    });


    //TODO could be implemented if needed
    /*$app->get('/GetAllProjects',
        function (Request $request, Response $response, $args) use ($projectController) {
            $project = $projectController->getAllProjects(); //needs implementation
            if ($project == null) {
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            } else {
                $response->getBody()->write(json_encode($project));
                return $response;
            }
    });*/

    //Edit project JSON example
    /*
     *
     {
        "id" : 1, //replace your project id here
        "name" : "Example Project",
        "description" : "Example Description",
        "state" : "ACTIVE",
     }
     *
     */

    // this should only edit the Project Information such as name or description or state
    // Addition of Tasks should be handled by the TaskController in theory
    $app->get('/EditProject',
        function (Request $request, Response $response, $args) use ($projectController) {
            $json = $request->getBody();
            $data = json_decode($json, true);
            $projectDTO = ProjectDTO::fromArray($data);
            $projectId = $projectController->editProject($projectDTO);
            if ($projectId == 0) {
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            } else {
                $response->getBody()->write(json_encode("Proyecto editado con id: " . $projectId));
                return $response;
            }
    });

};
