<?php
/**
 * Task controller.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController.
 *
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\TaskRepository            $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="task_index",
     * )
     */
    public function index(Request $request, TaskRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            Task::NUMBER_OF_ITEMS
        );

        return $this->render(
            'task/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Task $task Task entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="task_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Task $task): Response
    {
        return $this->render(
            'task/view.html.twig',
            ['task' => $task]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\TaskRepository            $repository Task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="task_new",
     * )
     */
    public function new(Request $request, TaskRepository $repository): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($task);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Task                          $task       Task entity
     * @param \App\Repository\TaskRepository            $repository Task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="task_edit",
     * )
     */
    public function edit(Request $request, Task $task, TaskRepository $repository): Response
    {
        $form = $this->createForm(TaskType::class, $task, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($task);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\Task                          $task       Task entity
     * @param \App\Repository\TaskRepository            $repository Task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="task_delete",
     * )
     */
    public function delete(Request $request, Task $task, TaskRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $task, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($task);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/delete.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }
}
