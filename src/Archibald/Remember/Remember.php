<?php

namespace Archibald\Remember;

use Archibald\Remember\Database\JsonDatabase;
use Archibald\Remember\Database\SqlDatabase;

class Remember
{
    public $remember = null;

    public function __construct()
    {
        // Sort out which type of Database to use
        if ('JSON' === DB_TYPE) {
            $this->remember = new JsonDatabase();
        } elseif ('MYSQL' === DB_TYPE) {
            $this->remember = new SqlDatabase();
        }

        if (null !== $this->remember) {
            $this->remember->connect();
        }
    }

    public function createDatabaseIfNotExists()
    {
        $this->remember->createDatabaseIfNotExists();
    }

    public function saveRemember($tags, $url, $user, $userId)
    {
        $this->remember->saveRemember($tags, $url, $user, $userId);
    }

    public function getRemember($tag)
    {
        return $this->remember->getRemember($tag);
    }

    public function getRemembered()
    {
        return $this->remember->getRemembered();
    }
}
