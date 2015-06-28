<?php
namespace tests\unit\Poensis\Origami\Controller;

use mageekguy\atoum;
use Symfony\Component\HttpFoundation\Request;

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

    protected function get($uri)
    {
        global $app;
        return $app->handle(Request::create($uri));
    }
}
