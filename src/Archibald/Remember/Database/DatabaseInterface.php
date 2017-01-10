<?php

namespace Archibald\Remember\Database;

interface DatabaseInterface
{
    public function connect();
    public function createDatabaseIfNotExists();
    public function saveRemember($tags, $url, $user, $userId);
    public function getRemember($tag);
    public function getRemembered();
}
