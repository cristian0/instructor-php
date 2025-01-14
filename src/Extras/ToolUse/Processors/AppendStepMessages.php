<?php

namespace Cognesy\Instructor\Extras\ToolUse\Processors;

use Cognesy\Instructor\Extras\ToolUse\Contracts\CanProcessStep;
use Cognesy\Instructor\Extras\ToolUse\ToolUseContext;
use Cognesy\Instructor\Extras\ToolUse\ToolUseStep;

class AppendStepMessages implements CanProcessStep
{
    public function processStep(ToolUseStep $step, ToolUseContext $context): ToolUseStep {
        $context->appendMessages($step->messages());
        return $step;
    }
}