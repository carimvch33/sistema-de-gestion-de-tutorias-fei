<?php
require_once '../config/config.php';
require_once '../controllers/MenuController.php';

$menuController = new MenuController();
$menuController->displayMenu();