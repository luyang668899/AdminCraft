#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use PHPUnit\TextUI\Command;

// 运行 PHPUnit 测试
$exitCode = Command::main(false);

exit($exitCode);
