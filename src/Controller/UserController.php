<?php
namespace Poensis\Origami\Controller;

use Silex\Application;
use Poensis\Origami\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $username = $request->get('username', null);
        if ($username === null) {
            return new JsonResponse(array(
                'errors' => array(
                    'username is mandatory',
                ),
            ), 400);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));

        $app['orm.em']->persist($user);
        $app['orm.em']->flush();

        return new JsonResponse($user->toArray());
    }
}
