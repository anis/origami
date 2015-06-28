<?php
namespace Poensis\Origami\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityRepository;

class UserController
{
    protected $users;

    public function __construct(EntityRepository $users)
    {
        $this->users = $users;
    }

    public function listAction()
    {
        $users = array_map(
            function ($user) {
                return $user->toArray();
            },
            $this->users->findAll()
        );

        return new JsonResponse($users);
    }
}
