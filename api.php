<?php
/**
 * This file will be called by your Slash Command Integration.
 */
use Archibald\Request\Request;
use Archibald\Archibald;

require_once(__DIR__ . '/vendor/autoload.php');
require_once('base.php');

if (file_exists(__DIR__ . '/custom.php')) {
    require_once('custom.php');
}

$archie = new Archibald();

$archie->loadConfig();
$archie->setupConfigVars();

// Return errors when there are any
if ($archie->hasConfigErrors()) {
    $errors = $archie->getConfigErrors();

    echo implode("\n", $errors);
} else {
    // Make API request when required POST vars are present
    if (isset($_POST['command'])) {
        $post = $_POST;
        $request = new Request($post);
    }
}
