<?php

namespace Archibald\Remember\Database;

use Archibald\Request\RequestError;
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
            'driver'    => DB_DRIVER,
            'host'      => DB_HOST,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => DB_PREFIX
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        /**
         * Fetch results as arrays.
         *
         * See http://stackoverflow.com/a/32730177/1059980
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

                return 'Database created successfully';
            }
        } catch (\Exception $e) {
            return $e;
        }

        return true;
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
            return new RequestError('database', $e->getMessage());
        }

        return true;
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
                return new RequestError('not-found');
            }

            $tags = [];

            foreach ($rows as $row) {
                $tags[] = $row['tag'];
            }

            return $tags;
        } catch (\Exception $e) {
            return new RequestError('database', $e->getMessage());
        }
    }
}
