<?php
namespace tests\unit\Poensis\Origami\Entity;

use mageekguy\atoum;

class User extends atoum
{
    public function test___emailGetterSetter()
    {
        $this
            ->given($this->newTestedInstance)
                ->and($email = uniqid())
                ->and($this->testedInstance->setEmail($email))
            ->then
            ->string($this->testedInstance->getEmail())
                ->isIdenticalTo($email);
    }

    public function test___passwordGetterSetter()
    {
        $this
            ->given($this->newTestedInstance)
                ->and($password = uniqid())
                ->and($this->testedInstance->setPassword($password))
            ->then
            ->string($this->testedInstance->getPassword())
                ->isIdenticalTo($password);
    }
}
