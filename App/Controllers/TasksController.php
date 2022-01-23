<?php

namespace App\Controllers;

use App\Models\Task;
use App\Helpers\Paginator;
use Rakit\Validation\Validator;
use Twig\Environment;

class TasksController extends Controller
{
    public function __construct(
        private Environment $view,
        private Validator $validator,
        private Task $model
    ) {

    }

    public function index()
    {
        $validation = $this->validator->validate($_GET, [
            'page' => ['numeric'],
            'sortValue' => ['in:asc,desc'],
            'sortKey' => ['in:name,email,is_done']
        ]);

        if ($validation->fails()) {
            $this->redirect('/error/400');
        }
        else {
            $page = $_GET['page'] ?? 1;
            $sortKey = $_GET['sortKey'] ?? null;
            $sortValue = $_GET['sortValue'] ?? null;

            $urlPattern = '?page=(:num)';

            if ($sortKey && $sortValue) {
                $urlPattern .= "&sortKey=$sortKey&sortValue=$sortValue";
            }

            echo $this->view->render('index.twig', [
                'isAuth' => $this->isAuth(),
                'tasks' => $this->model->getAll($page, $sortKey, $sortValue),
                'message' => $this->getFlash('message'),
                'sortKey' => $sortKey,
                'sortValue' => $sortValue,
                'pagination' => new Paginator(
                    $this->model->count(), Task::LIMIT, $page, $urlPattern
                )
            ]);
        }
    }

    public function create()
    {
        echo $this->view->render('create.twig', [
            'errors' => $this->getFlash('errors'),
            'task' => $this->getFlash('task')
        ]);
    }

    public function store()
    {
        $validation = $this->validator->validate($_POST, [
            'name' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email', 'max:50'],
            'description' => ['required', 'max:255']
        ]);

        if ($validation->fails()) {
            $this->setFlash('errors', $validation->errors()->toArray());
            $this->setFlash('task', $validation->getValidatedData());
            $this->redirect('/tasks/create');
        }
        else {

            if ($this->model->create($validation->getValidatedData())) {
                $this->setFlash('message', [
                    'type' => 'success',
                    'text' => 'Task created successfully!'
                ]);
            }
            else {
                $this->setFlash('message', [
                    'type' => 'warning',
                    'text' => 'Something went wrong.'
                ]);
            }

            $this->redirect('/');
        }
    }

    public function edit(int $id)
    {
        if (! $this->isAuth()) {
            $this->redirect('/error/401');
        }

        $task = $this->model->findById($id);

        if (! $task) {
            $this->redirect('/error/404');
        }

        echo $this->view->render('edit.twig', [
            'task' => $task,
            'errors' => $this->getFlash('errors')
        ]);
    }

    public function update($id)
    {
        if (! $this->isAuth()) {
            $this->redirect('/error/401');
        }

        if (! $this->model->exists($id)) {
            $this->redirect('/error/404');
        }

        $validation = $this->validator->validate($_POST, [
            'description' => ['required', 'max:255'],
        ]);

        if ($validation->fails()) {
            $this->setFlash('errors', $validation->errors()->toArray());
            $this->redirect('/tasks/edit/' . $id);
        }
        else {
            $data = $validation->getValidatedData();
            $data['is_done'] = $_POST['is_done'] ?? 0;

            if ($this->model->update($id, $data)) {
                $this->setFlash('message', [
                    'type' => 'success',
                    'text' => 'Task update successfully!'
                ]);
            }
            else {
                $this->setFlash('message', [
                    'type' => 'warning',
                    'text' => 'Something went wrong.'
                ]);
            }

            $this->redirect('/');
        }
    }
}