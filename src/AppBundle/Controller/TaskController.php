<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\Post;


class TaskController extends FOSRestController
{
    public function getTasksAction()
    {
        $data = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findAll();

        $view = $this->view($data, 200)
            ->setTemplate("MyBundle:Users:getUsers.html.twig")
            ->setTemplateVar('users');

        return $this->handleView($view);
    }
    public function getTaskAction($id)
    {
        $data = $this->getDoctrine()->getRepository(Task::class)
            ->find($id);

        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    public function postTaskAction(Request $request)
    {
        $data = new Task();
        $name = $request->get('name');
        $done = $request->get('done');
        $due = new \DateTime("now");

        if (empty($name) && empty($done))
        {
            throw new Exception("Null values are not allowed", 406);
        }

        $data->setName($name)->setDue($due)->setDone($done);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        $view = $this->view($data,200);
        return $this->handleView($view);

    }
    public function updateTaskAction($id, Request $request)
    {
        $name = $request->get('name');
        $done = $request->get('done');

        $em = $this->getDoctrine()->getManager();
        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);

        if (empty($task))
        {
            throw new Exception("task is not found");
        }
        elseif(isset($name) && isset($done))
        {
            $task->setName($name);
            $task->setDone($done);
            $em->flush();
            $view = $this->view($task, 200);
            return $this->handleView($view);
        }
        elseif (isset($name) && is_null($done))
        {
            $task->setName($name);
            $em->flush();
            $view = $this->view($task, 200);
            return $this->handleView($view);
        }
        elseif (is_null($name) && isset($done))
        {
            $task->setDone($done);
            $em->flush();
            $view = $this->view($task, 200);
            return $this->handleView($view);
        }
    }
    public function deleteTaskAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $this->getDoctrine()->getRepository('AppBundle:Task')->find($id);

        if (empty($task))
        {
            throw new Exception("Task not found");
        }
        else{
            $em->remove($task);
            $em->flush();
            $view = $this->view($task, 202);
            return $this->handleView($view);
        }
    }


}
