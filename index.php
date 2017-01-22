<?php

use Archibald\Archibald;

require_once(__DIR__ . '/vendor/autoload.php');
require_once('base.php');

if (file_exists(__DIR__ . '/custom.php')) {
    require_once('custom.php');
}

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
                    <?php foreach ($archie->getConfigErrors() as $error) : ?>
                        <?php if (is_array($error)) : ?>
                            <li class="error">
                                <strong><?php echo $error['type']; ?>:</strong>
                                <?php echo $error['error']; ?>
                            </li>
                        <?php else : ?>
                            <li class="error"><?php echo $error; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else : ?>
            <?php if ($archie->hasMessages()) : ?>
                <p class="info ok">
                    <?php foreach ($archie->getMessages() as $message) : ?>
                        <?php echo $message; ?><br>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>
            <p class="info ok">I have everything I need. You’re good to go!<p>
        <?php endif; ?>

        <p><strong>This is your Slash Command URL</strong></p>
        <input type="text" value="http<?php echo (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/api.php'; ?>">
        <p>Now head over to your Slack Integration settings and insert it into the URL field of the Slash Command Integration:</p>
        <img src="https://cloud.githubusercontent.com/assets/2084481/8761294/eb2c06ca-2d49-11e5-9d93-0c345706a658.png" />
    </div>
</body>
</html>


