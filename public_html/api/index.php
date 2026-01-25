<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Core/bootstrap.php';

use Controllers\UserController;
use Modules\Api\Router;

Router::add('GET', '/users', UserController::class, 'getAll');
Router::add('GET', '/users/{id}', UserController::class, 'getById');

Router::run();