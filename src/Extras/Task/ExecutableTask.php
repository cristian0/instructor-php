<?php

namespace Cognesy\Instructor\Extras\Task;

use Cognesy\Instructor\Extras\Signature\Contracts\Signature;
use Cognesy\Instructor\Extras\Task\Enums\TaskStatus;
use Exception;
use Throwable;

abstract class ExecutableTask extends Task
{
    protected Throwable $error;

    public function __construct(string|Signature $signature) {
        parent::__construct($signature);
    }

    public function with(array|Signature $inputs) : static {
        $this->setInputs(match(true) {
            is_array($inputs) => $inputs,
            ($inputs instanceof Signature) => $inputs->asInputArgs(),
            default => throw new Exception('Invalid inputs'),
        });
        return $this;
    }

    protected function execute(array $inputs) : mixed {
        try {
            $this->changeStatus(TaskStatus::InProgress);
            $this->setInputs($inputs);
            $result = $this->forward(...$this->inputs());
            $outputs = match(true) {
                is_array($result) => $result,
                default => $this->scalarOutput($result),
            };
            $this->setOutputs($outputs);
            $this->changeStatus(TaskStatus::Completed);
        } catch (Throwable $e) {
            $this->changeStatus(TaskStatus::Failed);
            $this->error = $e;
            throw $e;
        }
        return $result;
    }

    public function get() : mixed {
        return $this->execute($this->inputs());
    }

    public function error() : Throwable {
        return $this->error;
    }
}
