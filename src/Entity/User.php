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
    protected $username;

    /**
     * @Column(type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @Column(type="string", length=255)
     */
    protected $password;

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

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

    public function toArray()
    {
        return array(
            'username' => $this->getUsername(),
            'email'    => $this->getEmail(),
            'password' => $this->getPassword(),
        );
    }
}
