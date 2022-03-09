<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/", name="to_do_list")
     */
    public function index()
    {
        $tasks= $this->getDoctrine()->getRepository(Task::class)->findBy([],['id'=>"DESC"]);

        return $this->render('index.html.twig',['tasks'=>$tasks]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $total= $this->getDoctrine()->getRepository(Task::class)->CountAllRows();
        $done= $this->getDoctrine()->getRepository(Task::class)->CountAllDone();
        $overdue= $this->getDoctrine()->getRepository(Task::class)->Countoverdue();

        return $this->render('dashboard.html.twig',['total'=>$total,'done'=>$done,'overdue'=>$overdue]);
    }

    /**
     * @Route("/stat", name="stat", methods={"POST"})
     */
    public function stat(Request $request)
    {
        $date1= trim($request->request->get('date1')); //trim(): remove free spaces from the sides
        $date2= trim($request->request->get('date2'));

        $total= $this->getDoctrine()->getRepository(Task::class)->CountRowsByDateRange($date1,$date2);
        $done=0;
        $overdue=0;

        return $this->render('dashboard.html.twig',['total'=>$total,'done'=>$done,'overdue'=>$overdue]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request)
    {
        $title= trim($request->request->get('title')); //trim(): remove free spaces from the sides
        $deadline= trim($request->request->get('deadline'));
        if (empty($title))
            return $this->redirectToRoute('to_do_list');


        $em= $this->getDoctrine()->getManager();

        $task= new Task();
        $task->setTitle($title);
        $task->setDeadline(\DateTime::createFromFormat('Y-m-d', $deadline));
        $task->setCreatedAt(new \DateTime());
        $em->persist($task);
        $em->flush();

        $this->addFlash(
            'notice',
            'Task Created !'
        );

        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id)
    {
        $em= $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);

        $task->setStatus( !$task->getStatus() );//inverse status

        $em->flush();

        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/delete/{id}", name="delete_task")
     */
    public function delete(Task $id)
    {
        $em = $this->getDoctrine()->getManager();
//        $task = $em->getRepository(Task::class)->find($id);
//composer require sensio/framework-extra-bundle :: Param Converter
        $em->remove($id);
        $em->flush();

        $this->addFlash(
            'notice',
            'Task deleted !'
        );

        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/deleteAll", name="delete_all")
     */
    public function deleteAll()
    {
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository(Task::class)->findAll();
        for ($i=0; $i<count($tasks); $i++){
            $em->remove($tasks[$i]);
        }
        $em->flush();

        $this->addFlash(
            'notice',
            'All tasks has been Deleted !'
        );

        return $this->redirectToRoute('to_do_list');
    }
    //    /**
//     * @Route("/to/do/list", name="to_do_list")
//     */
//    public function index(): Response
//    {
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/ToDoListController.php',
//        ]);
//    }
}
