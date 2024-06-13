<?php

namespace QuixLabs\LaravelHookSystem\Utils;

use Illuminate\Support\Facades\Blade;
use QuixLabs\LaravelHookSystem\Hook;
use Termwind\HtmlRenderer;

class CommandTable
{
    /**
     * @param  class-string<Hook>|null  $hook
     */
    public static function asString(array &$rows, ?string $hook = null): string
    {
        if ($hook) {
            $hook::send($rows);
        }
        $html = Blade::render(
            string: file_get_contents(__DIR__.'/design/command_table.blade.php'),
            data: compact('rows'),
        );

        return (new HtmlRenderer)->parse($html)->toString();
    }
}
