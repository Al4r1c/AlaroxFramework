<?php
namespace AlaroxFramework\cfg\rest;

class Auth
{
    /**
     * @var string
     */
    private $_authentifMethode;

    /**
     * @var string
     */
    private $_username;

    /**
     * @var string
     */
    private $_privateKey;

    /**
     * @return string
     */
    public function getAuthentifMethode()
    {
        return $this->_authentifMethode;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param string $authentifMethode
     * @throws \InvalidArgumentException
     */
    public function setAuthentifMethode($authentifMethode)
    {
        if (!is_string($authentifMethode)) {
            throw new \InvalidArgumentException('Expected string for authentification method.');
        }

        $this->_authentifMethode = $authentifMethode;
    }

    /**
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPrivateKey($password)
    {
        if (!is_string($password)) {
            throw new \InvalidArgumentException('Expected string for password.');
        }

        $this->_privateKey = $password;
    }


    /**
     * @param string $username
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        if (!is_string($username)) {
            throw new \InvalidArgumentException('Expected string for username.');
        }

        $this->_username = $username;
    }
}