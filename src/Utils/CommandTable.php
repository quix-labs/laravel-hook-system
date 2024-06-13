<?php

namespace QuixLabs\LaravelHookSystem\Utils;

use Illuminate\Support\Facades\Blade;
use phpDocumentor\Reflection\Types\ClassString;
use QuixLabs\LaravelHookSystem\Hook;

use Termwind\HtmlRenderer;
use function Termwind\render;

class CommandTable
{
    /**
     * @param array $rows
     * @param class-string<Hook>|null $hook
     * @return string
     */
    public static function asString(array &$rows, ?string $hook = null): string
    {
        if ($hook) {
            $hook::send($rows);
        }
        $html = Blade::render(
            string: file_get_contents(__DIR__ . '/design/command_table.blade.php'),
            data: compact('rows'),
            deleteCachedView: true,
        );
        return (new HtmlRenderer)->parse($html)->toString();
    }
}
