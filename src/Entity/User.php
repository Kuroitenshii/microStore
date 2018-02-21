<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint as Assert;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @var string
     *
     * @ORM\Column(name="id", type="integer", nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\RandomIdGenerator")
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=6, nullable=false)
     */
    private $password;


    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="pseudo", type="string", length=255, nullable=false)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=false)
     */
    private $role;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="mail", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * User constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo(string $pseudo)
    {
        $this->pseudo = $pseudo;
    }

    public function generate()
    {
        $min_value = 100000;
        $max_value = 999999;

        return mt_rand($min_value, $max_value);
    }



    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }


    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getSalt(){
        return null;
    }

    public function eraseCredentials(){

    }

    public function getRoles(){
        return array($this->role);
    }

    public function serialize(){
        return serialize(array(
            $this->userName,
            $this->password
        ));
    }

    public function unserialize($serialized){
        list(

            $this->userName,
            $this->password
            ) = unserialize($serialized);
    }




}
