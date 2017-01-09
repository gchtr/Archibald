<?php

namespace Archibald;

use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Helpers\Validate;
use Lazer\Classes\LazerException;

class Remember
{
    private $tableName = 'remember';

    /**
     * Creates the database files when the database doesn’t exist yet.
     */
    public function initDatabase()
    {
        try {
            Validate::table($this->tableName)->exists();
        } catch (LazerException $e) {
            Lazer::create($this->tableName, array(
                'id' => 'integer',
                'tag' => 'string',
                'archie' => 'string',
                'user' => 'string',
                'userid' => 'string'
            ));

            echo 'Database created';
        }
    }

    /**
     * Save a custom image file with a list of tags.
     *
     * @param array     $tags   An array of tags that are assigned to the image.
     * @param string    $url    The url of the image.
     * @param string    $user   The name of the user who saves the image.
     * @param string    $userId The userId of the user who save the image.
     */
    public function saveRemember($tags, $url, $user, $userId)
    {
        $row = Lazer::table($this->tableName);

        foreach ($tags as $tag) {
            $row->tag = trim($tag);
            $row->archie = trim($url);
            $row->user = $user;
            $row->userid = $userId;

            try {
                $row->save();
            } catch (\Exception $e) {
                echo 'Ah, that didn’t work. The database is not nice to me today.';
                die();
            }
        }

        // Print success message
        echo 'Ha! You can now use *"' . implode('"* or *"', $tags) . '"* to run that masterpiece from ' . $url
             . '. Nobody will know :wink:';
    }

    /**
     * Get all images for a tag.
     *
     * @param string    $tag    Tag to search the database for.
     * @return array|bool       Array on success, false when no tags are found.
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
     * @return array|bool   Array on success, false when database is empty.
     */
    public function getRemembered()
    {
        try {
            $rows = Lazer::table($this->tableName)->findAll();
            $tags = array();

            foreach ($rows as $row) {
                $tags[] = $row->tag;
            }

            return $tags;
        } catch (\Exception $e) {
            return false;
        }
    }
}
