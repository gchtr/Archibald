<?php

namespace Archibald;

require 'config.php';
require 'vendor/autoload.php';

if (isset($_POST['command']) && '/archie' == $_POST['command']) {
	$post = $_POST;
	$request = new Api($post);
}
