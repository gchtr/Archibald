<?php

namespace Archibald\Remember;

use Archibald\Remember\Database\JsonDatabase;
use Archibald\Remember\Database\SqlDatabase;

/**
 * Class Remember
 *
 * Wrapper to handle different database implementations of Remember feature.
 *
 * @package Archibald\Remember
 */
class Remember
{
    public $remember = null;
    private $useRemember = true;

    public function __construct()
    {
        // Sort out which type of Database to use
        if ('JSON' === DB_TYPE) {
            $this->remember = new JsonDatabase();
        } elseif ('SQL' === DB_TYPE) {
            $this->remember = new SqlDatabase();
        } else {
            $this->useRemember = false;
        }

        if ($this->useRemember && null !== $this->remember) {
            $this->remember->connect();
        }
    }

    public function useRemember()
    {
        return $this->useRemember;
    }

    /**
     * TODO: add error handling
     */
    public function createDatabaseIfNotExists()
    {
        return $this->remember->createDatabaseIfNotExists();
    }

    public function saveRemember($tags, $url, $user, $userId)
    {
        return $this->remember->saveRemember($tags, $url, $user, $userId);
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
