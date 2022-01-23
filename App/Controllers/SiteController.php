<?php

namespace App\Controllers;

use App\Models\Task;
use App\Helpers\Paginator;
use Rakit\Validation\Validator;
use Twig\Environment;

class SiteController extends Controller
{
    public function __construct(
        private Validator $validator,
        private Environment $view
    ) {

    }

    public function login()
    {
        echo $this->view->render('login.twig', [
            'errors' => $this->getFlash('errors'),
            'message' => $this->getFlash('message')
        ]);
    }

    public function signIn()
    {
        $validation = $this->validator->validate($_POST, [
            'name' => ['required'],
            'password' => ['required'],
        ]);

        if ($validation->fails()) {
            $this->setFlash('errors', $validation->errors()->toArray());
            $this->redirect('/login');
        }
        else {

            $credentials = ['name' => 'admin', 'password' => '123'];

            if ($credentials === $validation->getValidatedData()) {
                $this->setFlash('message', [
                    'type' => 'success',
                    'text' => 'Sign In successfully!'
                ]);

                $_SESSION['auth'] = true;

                $this->redirect('/');
            }
            else {
                $this->setFlash('message', [
                    'type' => 'danger',
                    'text' => 'Credentials are incorrect.'
                ]);

                $this->redirect('/login');
            }
        }
    }

    public function signOut()
    {
        if ($this->isAuth()) {
            unset($_SESSION['auth']);
            $this->redirect('/');
        }
        else {
            $this->redirect('/error/400');
        }
    }

    public function error($code)
    {
        echo $this->view->render('error.twig', [
            'code' => $code,
        ]);
    }
}