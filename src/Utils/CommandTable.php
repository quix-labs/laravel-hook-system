<?php

namespace QuixLabs\LaravelHookSystem\Utils;

use Illuminate\Support\Facades\Blade;
use QuixLabs\LaravelHookSystem\Hook;
use Termwind\HtmlRenderer;

class CommandTable
{
    public static function asString(array &$rows): string
    {
        $html = Blade::render(
            string: file_get_contents(__DIR__.'/design/command_table.blade.php'),
            data: compact('rows'),
        );

        return (new HtmlRenderer)->parse($html)->toString();
    }
}
