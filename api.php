<?php

namespace Archibald;

if (!file_exists('config.php')) {
	die("Error" . " File: " . __FILE__ . " on line: " . __LINE__ . " - config.php not found!");
}

require 'config.php';
require 'vendor/autoload.php';

if (isset($_POST['command']) && '/archie' == $_POST['command']) {
	$post = $_POST;
	$request = new Api($post);
}
