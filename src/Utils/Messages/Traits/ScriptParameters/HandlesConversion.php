<?php

namespace Cognesy\Instructor\Utils\Messages\Traits\ScriptParameters;

trait HandlesConversion
{
    public function toArray() : array {
        return $this->parameters;
    }

    public function keys() : array {
        return array_keys($this->parameters);
    }

    public function values() : array {
        return array_values($this->parameters);
    }
}