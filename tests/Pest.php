<?php

use QuixLabs\LaravelHookSystem\Tests\TestCaseOrdered;
use QuixLabs\LaravelHookSystem\Tests\TestCaseUnordered;

uses(TestCaseOrdered::class)->in(__DIR__.'/Ordered');
uses(TestCaseUnordered::class)->in(__DIR__.'/Unordered');
