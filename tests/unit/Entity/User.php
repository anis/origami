<?php
namespace tests\unit\Poensis\Origami\Entity;

use mageekguy\atoum;
use Poensis\Origami\Entity\User as Entity;

class User extends atoum
{
    public function test___emailGetterSetter()
    {
        $this
            ->given($entity = new Entity())
                ->and($email = uniqid())
                ->and($entity->setEmail($email))
            ->then
            ->string($entity->getEmail())
                ->isIdenticalTo($email);
    }

    public function test___passwordGetterSetter()
    {
        $this
            ->given($entity = new Entity())
                ->and($password = uniqid())
                ->and($entity->setPassword($password))
            ->then
            ->string($entity->getPassword())
                ->isIdenticalTo($password);
    }
}
