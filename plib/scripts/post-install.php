<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.

// This code is just an example of post-install script, do not use it in production

if (false !== ($upgrade = array_search('upgrade', $argv))) {
    $upgradeVersion = $argv[$upgrade + 1];
    echo "upgrading from version $upgradeVersion\n";

    if (version_compare($upgradeVersion, '1.2') < 0) {
        pm_Settings::set('history', 'upgrade');
    }

    echo "Upgrade finished.\n";
    exit(0);
}

pm_Settings::set('history', 'install');
echo "Installation finished.\n";
exit(0);
