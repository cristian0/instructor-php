<?php

namespace Cognesy\Instructor\Utils\Codebase\Actions;

use Cognesy\Instructor\Utils\Codebase\Data\CodeFile;
use Cognesy\Instructor\Utils\Codebase\Enums\CodeFileType;

class ExtractFiles
{
    private CodeFileType $type;

    public function __construct(CodeFileType $type) {
        $this->type = $type;
    }

    public function __invoke(string $path) : array {
        $list = [];
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $list[] = new CodeFile($path, $file, $this->type);
        }
        return $list;
    }
}
