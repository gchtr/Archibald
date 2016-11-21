<?php

namespace Archibald;

define('DOCUMENT_ROOT', dirname(__FILE__));

require 'static.php';
require 'vendor/autoload.php';

$archie = new Archibald();

$archie->loadConfig();
$archie->setupConfigVars();
$archie->setupRemember();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Archibald</title>
    <link rel="stylesheet" href="dist/css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Your Archibald server is up and running</h1>
        <?php if ($archie->hasConfigErrors()) : ?>
            <div class="info bad">
                <p>Uh oooh! Troubles ahead&hellip;</p>
                <ul class="errors">
                <?php
                    $errors = $archie->getConfigErrors();

                    foreach ($errors as $error) :
                ?>
                    <li class="error"><?php echo $error; ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php else : ?>
            <p class="info ok">I have everything I need. You’re good to go!<p>
        <?php endif; ?>
        <p><strong>This is your Slash Command URL</strong></p>
        <input type="text" value="http://<?= $_SERVER['HTTP_HOST'] . '/api.php'; ?>">
        <p>Now head over to your Slack Integration settings and insert it into the URL field of the Slash Command Integration:</p>
        <img src="https://cloud.githubusercontent.com/assets/2084481/8761294/eb2c06ca-2d49-11e5-9d93-0c345706a658.png" />
    </div>
</body>
</html>


