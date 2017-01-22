<?php

namespace Archibald\Remember\Database;

use Archibald\Request\RequestError;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class SqlDatabase
 *
 * @package Archibald\Remember\Database
 */
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

    /**
     * Creates the database when it doesn’t exist yet.
     *
     * @return bool|\Exception|string
     */
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

    /**
     * Save a custom image file with a list of tags.
     *
     * @see \Archibald\Remember\Remember::saveRemember()
     *
     * @param $tags
     * @param $url
     * @param $user
     * @param $userId
     *
     * @return RequestError|bool
     */
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

    /**
     * Get all images for a tag.
     *
     * @see \Archibald\Remember\Remember::getRemember()
     *
     * @param string $tag Tag to search the database for.
     *
     * @return array|bool Array on success, false when no tags are found.
     */
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

    /**
     * Get a list of tags that are saved in the remembered-database.
     *
     * @see \Archibald\Remember\Remember::getRemembered()
     *
     * @return mixed   Array on success, RequestError when database is empty.
     */
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
