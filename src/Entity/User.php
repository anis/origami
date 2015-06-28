<?php
namespace Poensis\Origami\Entity;

/** 
 * @Entity
 */
class User
{
    /**
     * @Id
     * @Column(type="string", length=255)
     */
    protected $email;

    /**
     * @Column(type="string", length=255)
     */
    protected $password;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}
