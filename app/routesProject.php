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
        "id" : 0, // replace with project id
        "title" : "Example Project",
        "description" : "Example Description",
        "userId" : 1 //replace your user id here
     }
     *
     */

    // this should only edit the Project Information such as name or description
    $app->put('/EditProject', function (Request $request, Response $response, $args) use ($projectController) {
        $json = $request->getBody();
        $data = json_decode($json, true);
        try {
            $projectController->editProject(ProjectDTO::fromArray($data), $data['userId']);
            $response->getBody()->write(json_encode("Projecto editado con id: " . $data['id']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        catch(Exception $e){
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

    });



    $app->delete('/DeleteProject', function (Request $request, Response $response, $args) use ($projectController) {
        $json = $request->getBody();
        $data = json_decode($json, true);
        try{
            $projectController->deleteProject($data['projectId'], $data['userId']);
            $response->getBody()->write(json_encode("Project deleted with ID: " . $data['projectId']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error"=>$e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
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

};
