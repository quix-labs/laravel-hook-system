<?php

namespace UniDeal\LaravelHookable\Utils;

use Illuminate\Support\Facades\Blade;
use UniDeal\LaravelHookable\Hook;
use function Termwind\render;


class CommandTable
{

    public function __construct(public array &$rows, public string|Hook|null $hook = null)
    {
    }

    public function render(): void
    {
        if ($this->hook) {
            $this->hook::send($this->rows);
        }

        render(Blade::render(
            string: file_get_contents(__DIR__ . '/design/command_table.blade.php'),
            data: ['rows' => $this->rows],
            deleteCachedView: true,
        ));
    }

    public static function display(array &$rows, ?string $hook = null): void
    {
        $table = new self($rows, $hook);
        $table->render();
    }
}
