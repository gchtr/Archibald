<?php

namespace Archibald;

require 'config_local.php';
require 'vendor/autoload.php';

if (isset($_POST['command']) && '/archie' == $_POST['command']) {
	$post = $_POST;
	//print_r($post);
	$request = new Api($post);
}
