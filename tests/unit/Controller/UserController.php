<?php
namespace tests\unit\Poensis\Origami\Controller;

use mageekguy\atoum;
use Symfony\Component\HttpFoundation\Request;
use Poensis\Origami\Entity\User;
use Poensis\Origami\Controller\UserController as Controller;

class UserController extends atoum
{
    public function test___listAction___returns200()
    {
        $this
            ->given($response = $this->get('/users/'))
            ->then
            ->integer($response->getStatusCode())
                ->isIdenticalTo(200);
    }

    public function test___listAction___returnsJson()
    {
        $this
            ->given($response = $this->get('/users/'))
            ->then
            ->object($response)
                ->isInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    public function test___listAction___returnsAllUsers()
    {
        // Create the mock
        $fakeUsers = array();
        for ($i = 0; $i < 3; $i++) {
            $fakeUsers[] = $this->createUser();
        }

        $mock = $this->mockRepository('Poensis\Origami\Repository\UserRepository');
        $mock->getMockController()->findAll = function () use ($fakeUsers) {
            return array_map(function ($user) { return $user['entity']; }, $fakeUsers);
        };

        // Test
        $this
            ->given($response = $this->get('/users/'))
                ->and($content = json_decode($response->getContent(), true))
            ->then
                ->array($content)
                    ->isIdenticalTo(array_map(function ($user) { return $user['raw']; }, $fakeUsers));
    }

    protected function createUser()
    {
        $user = new User();
        $user->setUsername(uniqid());
        $user->setEmail(uniqid());
        $user->setPassword(uniqid());

        return array(
            'entity' => $user,
            'raw'    => $user->toArray(),
        );
    }

    protected function mockRepository($repositoryClass)
    {
        if (!class_exists($repositoryClass)) {
            $repositoryClass = 'Doctrine\ORM\EntityRepository';
        }

        $mockClass = '\mock\\' . $repositoryClass;

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockRepository = new $mockClass();
        $this->mockGenerator->unshuntParentClassCalls();

        global $app;
        $app['users'] = $app->factory(function () use ($mockRepository) {
            return new Controller($mockRepository);
        });

        return $mockRepository;
    }

    protected function get($uri)
    {
        global $app;
        return $app->handle(Request::create($uri));
    }
}
