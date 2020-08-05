<?php

$file = sprintf('%s/cron-test.txt', __DIR__);

$current = date('Y-m-d H:i:s') . "\n";

file_put_contents($file, $current, FILE_APPEND | LOCK_EX);