<?php
namespace Poensis\Origami\Controller;

use Silex\Application;
use Poensis\Origami\Entity\User;
use Symfony\Component\HttpFoundation\Request;
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

    public function createAction(Request $request, Application $app)
    {
        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));

        $app['orm.em']->persist($user);
        $app['orm.em']->flush();

        return new JsonResponse($user->toArray());
    }
}
