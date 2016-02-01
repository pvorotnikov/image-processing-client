<?php
require_once('config.php');
require_once('includes/top.php');
require_once('includes/functions.php');

require_once('fragments/header.php');
require_once('pages/' . $page . '.php');
require_once('fragments/footer.php');
