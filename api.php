<?php

namespace Archibald;

use Archibald\Archibald;

require 'static.php';
require 'vendor/autoload.php';


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
