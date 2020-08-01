<?php

$file = 'cron-test.txt';

$current = date('Y-m-d H:i:s') . "\n";

file_put_contents($file, $current, FILE_APPEND | LOCK_EX);