<?php

$ROUTES = [
    "/fetch" => function () {
        require_once "app/fetch.php";
    },
    "/" => function () {
        require_once('app/views/main_page.php');
    },
];

foreach ($ROUTES as $route => $action) {
    if (
        substr($_SERVER['REQUEST_URI'], 0, strlen($route))  === $route
    ) {
        $action();
        exit;
    }
}
