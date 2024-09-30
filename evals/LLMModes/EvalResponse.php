<?php

namespace Cognesy\Evals\LLMModes;

use Exception;

class EvalResponse
{
    public function __construct(
        public string $id = '',
        public string $answer = '',
        public bool $isCorrect = false,
        public float $timeElapsed = 0.0,
        public ?Exception $exception = null,
        public int $inputTokens = 0,
        public int $outputTokens = 0,
    ) {}

    public function totalTps() : float {
        if ($this->timeElapsed === 0) {
            return 0;
        }
        return ($this->inputTokens + $this->outputTokens) / $this->timeElapsed;
    }

    public function outputTps() : float {
        if ($this->timeElapsed === 0) {
            return 0;
        }
        return $this->outputTokens / $this->timeElapsed;
    }
}
