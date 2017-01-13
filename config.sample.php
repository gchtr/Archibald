<?php
/**
 * Slash Command Token
 *
 * You will get a token when you add a new Slash Command integration in your Slack Integration settings.
 */
define('SLASHCOMMAND_TOKEN', '');

/**
 * Webhook URL
 *
 * You will find your Webhook URL when you add a new Incoming WebHook integration in your Slack Integration settings.
 */
define('WEBHOOK_URL', '');

/**
 * Define database type to use for Remember feature.
 *
 * You can use one of the following values
 *
 * false    Do not use Remember feature.
 * 'SQL'    Uses an SQL database. Define connections below.
 * 'JSON'   Uses a JSON-file base database.
 */
define('DB_TYPE', false);

/**
 * The following constants only need to be defined when you use
 * 'MYSQL' as DB_TYPE.
 *
 * Archibald uses https://github.com/illuminate/database for its database, so
 *
 * MySQL (mysql)
 * Postgres (pgsql)
 * SQL Server (sqlsrv)
 * and SQLite (sqlite)
 *
 * should all be supported.
 */
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_PREFIX', '');
