<?php

namespace Cognesy\Instructor\Extras\Task;

use Cognesy\Instructor\Extras\Signature\Contracts\Signature;
use Cognesy\Instructor\Utils\Pipeline;

class PipelineTask extends ExecutableTask
{
    private Pipeline $pipeline;

    public function __construct(
        string|Signature $signature,
        Pipeline $pipeline
    ) {
        parent::__construct($signature);
        $this->pipeline = $pipeline;
    }

    public function forward(mixed $input): mixed {
        return $this->pipeline->process($input);
    }
}
