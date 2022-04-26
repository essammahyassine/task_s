<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
     /**
     * @Route("/task", name="app_task")
     */
    public function index(TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->findAll();
        return $this->render('task/index.html.twig', ['task' => $task,]);
    }



    /**
     * @Route("/auto_ajout", name="create_product")
     */
    public function createProduct(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $task = new Task();
        $task->setName('my task');
        $task->setStatus('test production');
        $task->setDescription('Ergonomic and stylish!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($task);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$task->getId());
    }


    /**
     * @Route("/task/{id}", name="product_show")
     */
    public function show(int $id, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);
        return $this->render('task/show.html.twig', ['task' => $task,]);
    }

     /**
     * @Route("/delete/{id}" , name="delete_task")
     */
    public function delete(int $id,ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_task');
    }

    /**
     * @Route("/new" , name="addnew")
     */
    public function new(Request $request,ManagerRegistry $doctrine): Response
    {
        // just set up a fresh $task object (remove the example data)
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $task = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($task);        
            $entityManager->flush();

            return $this->redirectToRoute('app_task');
        }

        return $this->renderForm('task/new.html.twig', [
            'form' => $form,
        ]);
    }

}
