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
        $users = $this->users->findAll();
        $response = array();

        foreach ($users as $user) {
            $response[] = array(
                'email'    => $user->getEmail(),
                'password' => $user->getPassword(),
            );
        }

        return new JsonResponse($response);
    }
}
