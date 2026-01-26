<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Core/bootstrap.php';

use Controllers\UserController;
use Controllers\AuthController;
use Modules\Api\Router;

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

Router::add('GET', '/users', UserController::class, 'getList');
Router::add('GET', '/users/{id}', UserController::class, 'findById');

Router::add('POST', '/auth/register', AuthController::class, 'register');
Router::add('POST', '/auth/login', AuthController::class, 'login');

Router::run();