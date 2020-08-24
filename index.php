<?php

// This script should never be executed in a properly configured web server,
// so let's just redirect to main index.php which redirects to admin/index.php
// that shows environment warning about misconfigured server.

@header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
@header('Location: server/index.php');
