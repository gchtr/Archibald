<?php

namespace Archibald\Remember;

use Archibald\Remember\Database\JsonDatabase;
use Archibald\Remember\Database\SqlDatabase;
use Archibald\Request\RequestError;

/**
 * Class Remember
 *
 * Wrapper to handle different database implementations of Remember feature.
 *
 * @package Archibald\Remember
 */
class Remember
{
    /**
     * The Database interface to use.
     *
     * @var SqlDatabase|JsonDatabase
     */
    public $remember = null;

    /**
     * Whether the Remember feature is activated or not.
     * @var bool
     */
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

    /**
     * @return bool
     */
    public function useRemember()
    {
        return $this->useRemember;
    }

    public function createDatabaseIfNotExists()
    {
        return $this->remember->createDatabaseIfNotExists();
    }

    /**
     * Save a custom image file with a list of tags.
     *
     * @param array  $tags   An array of tags that are assigned to the image.
     * @param string $url    The url of the image.
     * @param string $user   The name of the user who saves the image.
     * @param string $userId The userId of the user who save the image.
     *
     * @return RequestError|bool
     */
    public function saveRemember($tags, $url, $user, $userId)
    {
        return $this->remember->saveRemember($tags, $url, $user, $userId);
    }

    /**
     * Get all images for a tag.
     *
     * @param string $tag Tag to search the database for.
     *
     * @return array|bool Array on success, false when no tags are found.
     */
    public function getRemember($tag)
    {
        return $this->remember->getRemember($tag);
    }

    /**
     * Get a list of tags that are saved in the remembered-database.
     *
     * @return mixed   Array on success, RequestError when database is empty.
     */
    public function getRemembered()
    {
        return $this->remember->getRemembered();
    }
}
