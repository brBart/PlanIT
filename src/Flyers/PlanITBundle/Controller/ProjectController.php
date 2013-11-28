<?php

namespace Flyers\PlanITBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;

use Flyers\PlanITBundle\Entity\User;
use Flyers\PlanITBundle\Entity\Project;
use Flyers\PlanITBundle\Form\ProjectType;

class ProjectController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\Get("/api/projects")
     */
    public function cgetAction()
    {
        $em = $this->container->get("doctrine")->getManager();
        
        $entities = $em->getRepository("PlanITBundle:Project")->findAll();

        $view = $this->view(array(
            'error' => 'success',
            'message' => '',
            'projects' => $entities
            ), 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/api/projects/{slug}")
     */
    public function getProjectsUserAction($slug)
    {
        $em = $this->container->get("doctrine")->getManager();

        $id = intval($slug);

        if ($id <= 0)
        {
            $view = $this->view(array(
                    'error' => 'error',
                    'message' => 'Invalid User'
                ), 200);
            return $this->handleView($view);
        }

        $user = $em->getRepository("PlanITBundle:User")->find($slug);
        if (!$user)
        {
            $view = $this->view(array(
                    'error' => 'error',
                    'message' => 'No User Found !'
                ), 200);
            return $this->handleView($view);
        }

        $entities = $em->getRepository("PlanITBundle:Project")->findAllByUser($user);

        $view = $this->view(array(
            'error' => 'success',
            'message' => '',
            'projects' => $entities
            ), 200);
        return $this->handleView($view);

    }

    /**
     * @Rest\Get("/api/project/{id}")
     */
    public function getAction($id)
    {
        $em = $this->container->get("doctrine")->getManager();

        $entity = $em->getRepository("PlanITBundle:Project")->find($id);
        if (!$entity) {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'Project not found !'
                ), 200);
            return $this->handleView($view);
        }

        $begin = $entity->getBegin()->format('d/m/Y');
        $end = $entity->getEnd()->format('d/m/Y');

        $entity->setBegin($begin);
        $entity->setEnd($end);

        $view = $this->view(array(
            'error' => 'success',
            'message' => '',
            'project' => $entity
            ), 200);
        return $this->handleView($view);

    }

    /**
     * params: name,description,begin,end,user
     * @Rest\Post("/api/project")
     */
    public function cpostAction(Request $request)
    {
        $em = $this->container->get("doctrine")->getManager();
        $userRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:User');

        $entity = new Project();
        $form = $this->createForm(new ProjectType(), $entity);
        
        $data = array();

        $userId = intval($request->request->get('user'));
        $data["name"] = $request->request->get('name');
        $data["description"] = $request->request->get('description');
        $data["begin"]   = null;
        $data["end"]     = null;

        $begin  = \DateTime::createFromFormat('d/m/Y', $request->request->get('begin'));
        $end    = \DateTime::createFromFormat('d/m/Y', $request->request->get('end'));

        $begin  = ($begin) ? $begin->format('d/m/Y') : null;
        $end    = ($end) ? $end->format('d/m/Y') : null;

        if ( !is_null($begin) ) $data['begin'] = $begin;
        if ( !is_null($end) ) $data['end'] = $end;

        $user = $em->getRepository("PlanITBundle:User")->find($userId);
        if (!$user) {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'User not found !'
                ), 200);
            return $this->handleView($view);
        }

        $form->bind($data);

        if ($form->isValid()) {
            $entity->addUser($user);
            $em->persist($entity);
            $em->flush();

            $view = $this->view(array(
                'error' => 'success',
                'message' => 'Project saved with success',
                'project' => $entity
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
     * @Rest\Put("/api/project/{id}")
     * @Rest\View()
     */
    public function putAction(Request $request, $id)
    {
        $em = $this->container->get("doctrine")->getManager();
        $entityRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Project');

        $entity = $entityRepository->find($id);
        if (!$entity) {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'Project not found !'
                ), 200);
            return $this->handleView($view);
        }
        $form = $this->createForm(new ProjectType(), $entity);

        $data = array();

        $data["name"] = $request->request->get('name');
        $data["description"] = $request->request->get('description');
        $data["begin"]   = null;
        $data["end"]     = null;

        $begin  = \DateTime::createFromFormat('d/m/Y', $request->request->get('begin'));
        $end    = \DateTime::createFromFormat('d/m/Y', $request->request->get('end'));

        $begin  = ($begin) ? $begin->format('d/m/Y') : null;
        $end    = ($end) ? $end->format('d/m/Y') : null;

        if ( !is_null($begin) ) $data['begin'] = $begin;
        if ( !is_null($end) ) $data['end'] = $end;
        
/*        
        $tmpDatetime = new \DateTime($request->request->get('begin'));
        $project["begin"] = array();
        $project["begin"]["year"] = $tmpDatetime->format('Y');
        $project["begin"]["month"] = $tmpDatetime->format('m');
        $project["begin"]["day"] = $tmpDatetime->format('d');
        $project["begin"]["hour"] = $tmpDatetime->format('H');
        $project["begin"]["minute"] = $tmpDatetime->format('i');
        $project["begin"]["second"] = $tmpDatetime->format('s');

        $tmpDatetime = new \DateTime($request->request->get('end'));
        $project["end"] = array();
        $project["end"]["year"] = $tmpDatetime->format('Y');
        $project["end"]["month"] = $tmpDatetime->format('m');
        $project["end"]["day"] = $tmpDatetime->format('d');
        $project["end"]["hour"] = $tmpDatetime->format('H');
        $project["end"]["minute"] = $tmpDatetime->format('i');
        $project["end"]["second"] = $tmpDatetime->format('s');
*/        

        $form->bind($data);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $view = $this->view(array(
                'error' => 'success',
                'message' => 'Project saved with success',
                'project' => $entity
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
     * @Rest\Delete("/api/project/{id}")
     * @Rest\View()
     */
    public function deleteAction($id)
    {
        $em = $this->container->get("doctrine")->getManager();
        $entityRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Project');
        
        $entity = $entityRepository->find($id);

        if (!is_null($entity))
        {
            $em->remove($entity);
            $em->flush();
        }

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Post("/api/participant/add/{id}")
     * @Rest\View()
     */
    /*
    public function participantAddAction(Request $request, $id)
    {
        $em = $this->container->get("doctrine")->getManager();
        $projectRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Project');
        $employeeRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Employee');
        $jobRepository = $this->container->get("doctrine")->getRepository('PlanITBundle:Job');

        $employee = $employeeRepository->findByEmail($request->request->get('email'));
        if(!$employee)
        {
            $employee = new Employee();
        }

        $form = $this->createForm(new ParticipantType(), $employee);

        $project = $projectRepository->find($id);
        if(!$project)
        {
            $view = $this->view(array(
                'error' => 'error',
                'message' => 'Project not found !'
                ), 200);
            return $this->handleView($view);
        }

        if (is_null($request->request->get('job'))) {
            $job = null;
        } else {
            $job = $jobRepository->find($request->request->get('job'));
            if(!$job)
                $job = null;
        }

        $participant = array();
        $participant["lastname"] = $request->request->get('lastname');
        $participant["firstname"] = $request->request->get('firstname');
        $participant["phone"] = $request->request->get('phone');
        $participant["salary"] = floatval( $request->request->get('salary') );
        $participant["email"] = $request->request->get('email');
        
        if (!is_null($job)) $participant["job"] = $job->getName();
        else $participant["job"] = $job;

        $form->bind($participant);

        if ($form->isValid())
        {
            $employee->addProject($project);
            $em->persist($employee);
            $em->flush();

            $view = $this->view(array(
                'error' => 'success',
                'message' => 'Participant added with success',
                'project' => $employee
                ), 200);
            return $this->handleView($view);
        }
        else
        {
            $view = $this->view(array(
                'error' => 'error',
                'message' => $form->getErrorsAsString()
                ), 200);
            return $this->handleView($view);
        }

    }
    */

}