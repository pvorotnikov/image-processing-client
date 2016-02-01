<?php

// set default timezone
date_default_timezone_set('Europe/Sofia');

// handle special home page case
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'home';
}

// check if page exists
if (!file_exists('pages/' . $page . '.php')) {
    $page = '404';
}
