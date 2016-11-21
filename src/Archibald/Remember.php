<?php

namespace Archibald;

use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Helpers\Validate;
use Lazer\Classes\LazerException;

class Remember
{
    private $tableName = 'remember';

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

        echo 'Ha! You can now use *"' . implode('"* or *"', $tags) . '"* to run that masterpiece from ' . $url
             . '. Nobody will know :wink:';
    }

    public function getRemember($tag)
    {
        try {
            $rows = Lazer::table($this->tableName)->where('tag', '=', $tag)->findAll()->asArray();
            return $rows;
        } catch (\Exception $e) {
            return false;
        }
    }

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
