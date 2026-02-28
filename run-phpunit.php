#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;

// 直接创建测试套件并运行
$suite = new TestSuite();
$suite->addTestFile(__DIR__ . '/tests/unit/UserTest.php');

$runner = new TestRunner();
$result = $runner->run($suite);

exit($result->wasSuccessful() ? 0 : 1);
