<?php

namespace Cognesy\Instructor\Extras\ToolUse\Traits\Tools;

use Cognesy\Instructor\Extras\ToolUse\Contracts\ToolInterface;

trait HandlesMutation
{
    public function withParallelCalls(bool $parallelToolCalls = true): self {
        $this->parallelToolCalls = $parallelToolCalls;
        return $this;
    }

    public function withTool(ToolInterface $tool): self {
        $this->tools[$tool->name()] = $tool;
        return $this;
    }

    public function addTool(ToolInterface $tool): self {
        $this->tools[$tool->name()] = $tool;
        return $this;
    }

    public function removeTool(string $name): self {
        unset($this->tools[$name]);
        return $this;
    }
}