<?php

namespace Archibald\Remember\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class SqlDatabase implements DatabaseInterface
{
    private $tableName = 'remember';

    public function connect()
    {
        /**
         * Configure the database and boot Eloquent
         */
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'archibald',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => ''
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        /**
         * http://stackoverflow.com/a/32730177/1059980
         */
        Capsule::connection()->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function createDatabaseIfNotExists()
    {
        try {
            if (!Capsule::schema()->hasTable($this->tableName)) {
                // Create table
                Capsule::schema()->create($this->tableName, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('tag');
                    $table->string('url');
                    $table->string('user');
                    $table->string('userid');
                });

                echo 'Database created';
            }
        } catch (\Exception $e) {
            echo 'Database could not be created';
        }
    }

    public function saveRemember($tags, $url, $user, $userId)
    {
        $rows = [];

        foreach ($tags as $tag) {
            $rows[] = [
                'tag' => trim($tag),
                'url' => trim($url),
                'user' => $user,
                'userid' => $userId,
            ];
        }

        try {
            Capsule::table($this->tableName)->insert($rows);
        } catch (\Exception $e) {
            echo 'Ah, that didn’t work. The database is not nice to me today.';
            die();
        }

        // Print success message
        echo 'Ha! You can now use *"' . implode('"* or *"', $tags) . '"* to run that masterpiece from ' . $url
             . '. Nobody will know :wink:';
    }

    public function getRemember($tag)
    {
        try {
            $rows = Capsule::table('remember')->where('tag', '=', $tag)->get()->toArray();

            // Return as array
            return $rows;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getRemembered()
    {
        try {
            $rows = Capsule::table($this->tableName)->get()->toArray();

            if (empty($rows)) {
                return false;
            }

            $tags = [];

            foreach ($rows as $row) {
                $tags[] = $row['tag'];
            }

            return $tags;
        } catch (\Exception $e) {
            return false;
        }
    }
}
