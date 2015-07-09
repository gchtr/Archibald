#!/bin/bash


cat > /archibald/config.php <<- EOM
<?php

define('SLASHCOMMAND_TOKEN', '$SLASHCOMMAND_TOKEN');
define('WEBHOOK_URL', '$WEBHOOK_URL');

EOM

php -S 0.0.0.0:80
