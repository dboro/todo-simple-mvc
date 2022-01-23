<?php

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../App/Views');
return new \Twig\Environment($loader);