<?php
namespace Modules\Main;

use Core\Patterns\Singleton;

class Application
{
    use Singleton;

    public readonly string $root;

    public readonly RequestList $get;
    public readonly RequestList $post;
    public readonly RequestList $request;
    public readonly RequestList $files;
    public readonly RequestList $session;
    public readonly RequestList $cookie;


    private function __construct()
    {
        $this->root = dirname($_SERVER['DOCUMENT_ROOT']);

        $this->request = new RequestList($_REQUEST);
        $this->session = new RequestList($_SESSION);
        $this->cookie = new RequestList($_COOKIE);
        $this->get = new RequestList($_GET);
        $this->post = new RequestList($_POST);
        $this->files = new RequestList($_FILES);
    }
}