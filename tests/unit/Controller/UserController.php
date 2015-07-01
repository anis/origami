<?php
namespace tests\unit\Poensis\Origami\Controller;

use mageekguy\atoum;
use Symfony\Component\HttpFoundation\Request;
use Poensis\Origami\Entity\User;
use Poensis\Origami\Controller\UserController as Controller;

class UserController extends atoum
{
    /***************************************************************************
     * List action
     **************************************************************************/

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
        // Inject fake users into the business code
        $fakeUsers = array();
        for ($i = 0; $i < 3; $i++) {
            $fakeUsers[] = $this->getNewUser();
        }

        $mock = $this->getRepository('Poensis\Origami\Entity\User');
        $this->calling($mock)->findAll = function () use ($fakeUsers) {
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

    /***************************************************************************
     * Create action
     **************************************************************************/

    public function test___createAction___returns200()
    {
        $this
            ->given($response = $this->post('/users/'))
            ->then
            ->integer($response->getStatusCode())
                ->isIdenticalTo(200);
    }

    /***************************************************************************
     * Utilities
     **************************************************************************/

    /**
     * Creates a new full instance of User
     *
     * @return array
     */
    protected function getNewUser()
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

    /**
     * Creates a mock repository for the given entity
     *
     * The mock is automatically injected into the business code.
     *
     * @param string $entityName Name of the entity class
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($entityName)
    {
        // Guess the name of the repository class, based on the entity name
        $repositoryName = str_replace('Entity\\', 'Repository\\', $entityName) . 'Repository';

        if (!class_exists($repositoryName)) {
            $repositoryName = 'Doctrine\ORM\EntityRepository';
        }

        $mockName = '\mock\\' . $repositoryName;

        // Create the mock instance
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new $mockName();
        $this->mockGenerator->unshuntParentClassCalls();

        // Inject the mock into the business code
        global $app;
        $app['users'] = $app->factory(function () use ($mock) {
            return new Controller($mock);
        });

        return $mock;
    }

    /**
     * Syntaxic sugar for faking HTTP requests to the application
     *
     * The parameter $arguments should respect the following definition:
     *  [0] string $uri        URI to be requested (actually, the path only)
     *
     * @param string $verb      HTTP verb (GET, POST, ...)
     * @param array  $arguments Other request arguments
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function __call($method, $arguments)
    {
        if (!in_array($method, array('get', 'post'))) {
            return parent::__call($method, $arguments);
        }

        list($uri) = $arguments;

        global $app;
        return $app->handle(
            Request::create(
                $uri,
                strtoupper($method)
            )
        );
    }
}
