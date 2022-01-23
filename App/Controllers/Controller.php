<?php

namespace App\Controllers;

class Controller
{
    protected function isAuth() : bool
    {
        return $_SESSION['auth'] ?? false;
    }

    protected function setFlash(string $key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    protected function getFlash(string $key)
    {
        if (isset($_SESSION['_flash'][$key])) {
            $value = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);

            return $value;
        }
        else {
            return null;
        }
    }

    protected function redirect(string $path)
    {
        header('Location: ' . $path);
        exit();
    }
}