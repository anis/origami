<?php
namespace tests\unit\Poensis\Origami\Entity;

use mageekguy\atoum;

class User extends atoum
{
    /**
     * @dataProvider provider___getterSetter
     */
    public function test___getterSetter($property)
    {
        $setterName = 'set' . ucfirst($property);
        $getterName = 'get' . ucfirst($property);

        $this
            ->given($this->newTestedInstance)
                ->and($value = uniqid())
                ->and($this->testedInstance->$setterName($value))
            ->then
            ->string($this->testedInstance->$getterName())
                ->isIdenticalTo($value);
    }

    protected function provider___getterSetter()
    {
        return array(
            array('username'),
            array('email'),
            array('password'),
        );
    }

    public function test___toArray()
    {
        $this
            ->given($this->newTestedInstance)
                ->and($username = uniqid())
                ->and($this->testedInstance->setUsername($username))
                ->and($email = uniqid())
                ->and($this->testedInstance->setEmail($email))
                ->and($password = uniqid())
                ->and($this->testedInstance->setPassword($password))
            ->then
                ->array($this->testedInstance->toArray())
                    ->isIdenticalTo(array(
                        'username' => $username,
                        'email'    => $email,
                        'password' => $password,
                    ));
    }
}
