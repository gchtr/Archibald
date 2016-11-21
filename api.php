<?php

namespace Archibald;

use Archibald\Archibald;

require 'vendor/autoload.php';
define('DOCUMENT_ROOT', dirname(__FILE__));

$archie = new Archibald();

$archie->loadConfig();
$archie->setupConfigVars();

/**
 * Return errors when there are
 */
if ($archie->hasConfigErrors()) {
	$errors = $archie->getConfigErrors();

	foreach ($errors as $error) {
		echo $error . ' ';
	}
}
else {
	/**
	 * Make API request when required POST vars are present
	 */
	if (isset($_POST['command']) && '/archie' == $_POST['command']) {
		$post = $_POST;
		$request = new Api($post);
	}
}
