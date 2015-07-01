<?php
namespace Poensis\Origami\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    public function listAction(Application $app)
    {
        $repository = $app['orm.em']->getRepository('Poensis\Origami\Entity\User');
        $users = array_map(
            function ($user) {
                return $user->toArray();
            },
            $repository->findAll()
        );

        return new JsonResponse($users);
    }

    public function createAction()
    {
        return "Hello world";
    }
}
