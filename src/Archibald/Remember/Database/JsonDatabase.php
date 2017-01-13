<?php

namespace Archibald\Remember\Database;

use Archibald\Request\RequestError;
use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Helpers\Validate;
use Lazer\Classes\LazerException;

class JsonDatabase implements DatabaseInterface
{
    private $tableName = 'remember';

    /**
     * Creates the database files when the database doesn’t exist yet.
     */
    public function createDatabaseIfNotExists()
    {
        try {
            Validate::table($this->tableName)->exists();
        } catch (LazerException $e) {
            try {
                // Make sure folder exists
                if (!file_exists(LAZER_DATA_PATH)) {
                    mkdir(LAZER_DATA_PATH, 0755, true);
                }

                Lazer::create($this->tableName, array(
                    'id'     => 'integer',
                    'tag'    => 'string',
                    'url'    => 'string',
                    'user'   => 'string',
                    'userid' => 'string'
                ));

                return 'Database created successfully';
            } catch (\Exception $e) {
                return $e;
            }
        }

        return true;
    }

    public function connect()
    {
        // Do nothing
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
        $row = Lazer::table($this->tableName);

        foreach ($tags as $tag) {
            $row->tag = trim($tag);
            $row->url = trim($url);
            $row->user = $user;
            $row->userid = $userId;

            try {
                $row->save();
            } catch (\Exception $e) {
                return new RequestError('database', $e->getMessage());
            }
        }

        return true;
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
        try {
            $rows = Lazer::table($this->tableName)->where('tag', '=', $tag)->findAll()->asArray();
            return $rows;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a list of tags that are saved in the remembered-database.
     *
     * @return mixed   Array on success, RequestError when database is empty.
     */
    public function getRemembered()
    {
        try {
            $rows = Lazer::table($this->tableName)->findAll();

            if (count($rows) < 1) {
                return new RequestError('not-found');
            }

            $tags = [];

            foreach ($rows as $row) {
                $tags[] = $row->tag;
            }

            return $tags;
        } catch (\Exception $e) {
            return new RequestError('database', $e->getMessage());
        }
    }
}
