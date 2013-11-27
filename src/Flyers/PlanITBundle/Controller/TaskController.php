<?php

namespace Flyers\PlanITBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;

use Flyers\PlanITBundle\Entity\User;
use Flyers\PlanITBundle\Entity\Project;
use Flyers\PlanITBundle\Entity\Task;
use Flyers\PlanITBundle\Form\TaskType;

class TaskController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\Get("/api/tasks")
     */
    public function cgetAction()
    {
        $em = $this->container->get("doctrine")->getManager();
        
        $entities = $em->getRepository("PlanITBundle:Task")->findAll();

        $view = $this->view(array(
            'error' => 'success',
            'message' => '',
            'tasks' => $entities
            ), 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/api/tasks/{slug}")
     */
    public function getTasksProjectAction($slug)
    {
        $em = $this->container->get("doctrine")->getManager();

        $id = intval($slug);

        if ($id <= 0)
        {
            $view = $this->view(array(
                    'error' => 'error',
                    'message' => 'Invalid Project'
                ), 200);
            return $this->handleView($view);
        }

        $project = $em->getRepository("PlanITBundle:Project")->find($slug);
        if (!$project)
        {
            $view = $this->view(array(
                    'error' => 'error',
                    'message' => 'No Project Found !'
                ), 200);
            return $this->handleView($view);
        }

        $entities = $em->getRepository("PlanITBundle:Task")->findByProject($project);

        $view = $this->view(array(
            'error' => 'success',
            'message' => '',
            'tasks' => $entities
            ), 200);
        return $this->handleView($view);

    }

    /**
     * @Rest\Get("/api/task/{id}")
     */
    public function getAction($id)
    {
        $em = $this->container->get("doctrine")->getManager();

        $entity = $em->getRepository("PlanITBundle:Task")->find($id);
        if (!$entity) {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'Task not found !'
                ), 200);
            return $this->handleView($view);
        }

        $view = $this->view(array(
            'error' => 'success',
            'message' => '',
            'task' => $entity
            ), 200);
        return $this->handleView($view);

    }

    /**
     * params: name,description,begin,end,user
     * @Rest\Post("/api/task")
     */
    public function cpostAction(Request $request)
    {
        $em = $this->container->get("doctrine")->getManager();
        $entityRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Task');

        $entity = new Task();
        $form = $this->createForm(new TaskType(), $entity);

        $data = array();

        $userId = intval($request->request->get('user'));
        $projectId = intval($request->request->get('project'));

        $data["name"] = $request->request->get('name');
        $data["description"] = $request->request->get('description');
        $data["employees"] = $request->request->get('employees');
        $data["estimate"] = floatval($request->request->get('estimate'));

        $user = $em->getRepository("PlanITBundle:User")->find($userId);
        if (!$user) {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'User not found !'
                ), 200);
            return $this->handleView($view);
        }

        $project = $em->getRepository("PlanITBundle:Project")->find($projectId);
        if (!$project) {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'Project not found !'
                ), 200);
            return $this->handleView($view);
        }

        $form->bind($data);

        if ($form->isValid()) {
            $entity->setProject($project);
            $em->persist($entity);
            $em->flush();

            $view = $this->view(array(
                'error' => 'success',
                'message' => 'Task saved with success',
                'task' => $entity
                ), 200);
            return $this->handleView($view);
        } else {
            $view = $this->view(array(
                'error' => 'error',
                'message' => $form->getErrorsAsString()
                ), 200);
            return $this->handleView($view);
        }

    }

    /**
     * params: name,description,begin,end
     * @Rest\Put("/api/task/{id}")
     * @Rest\View()
     */
    public function putAction(Request $request, $id)
    {
        $em = $this->container->get("doctrine")->getManager();
        $entityRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Task');

    }

    /**
     * @Rest\Delete("/api/task/{id}")
     * @Rest\View()
     */
    public function deleteAction($id)
    {
        $em = $this->container->get("doctrine")->getManager();
        $entityRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Task');
        
        $entity = $entityRepository->find($id);

        if (!is_null($entity))
        {
            $em->remove($entity);
            $em->flush();
        }

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }


}