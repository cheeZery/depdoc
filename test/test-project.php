<?php

$rootDirectory = __DIR__ . '/..';
$testProjectDirectory = "{$rootDirectory}/test/test-project";

$composerAutoload = "{$rootDirectory}/vendor/autoload.php";
require $composerAutoload;

chdir($testProjectDirectory);

DepDoc\Runner::run([$argv[1] ?? 'validate']);
