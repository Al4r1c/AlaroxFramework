<?php
namespace AlaroxFramework\utils\session;

class Session
{
    public function startSession()
    {
        session_start();
    }

    /**
     * @return mixed
     */
    public function &getSession()
    {
        return $_SESSION;
    }
}