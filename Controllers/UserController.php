<?php
namespace Controllers;

use Models\User;
use Modules\Orm\Repository;

class UserController
{
    public function getList()
    {
        $itemsList = new Repository(new User())->findAll();
        header('Content-Type: application/json');
        echo json_encode($itemsList);
    }
}