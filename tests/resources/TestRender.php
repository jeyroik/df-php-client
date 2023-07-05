<?php
namespace tests\resources;

use extas\components\Replace;

class TestRender
{
    public function render(string $path, array $data): string
    {
        $template = file_get_contents($path);
        return Replace::please()->apply($data)->to($template);
    }
}