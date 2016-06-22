<?php

if (-1 == version_compare(PHP_VERSION, '5.6.0')) {
    echo "This extension requires PHP version 5.6 or higher\n";

    exit(1);
}

exit(0);
