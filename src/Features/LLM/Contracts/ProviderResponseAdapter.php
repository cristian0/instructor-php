<?php

namespace Cognesy\Instructor\Features\LLM\Contracts;

use Cognesy\Instructor\Features\LLM\Data\LLMResponse;
use Cognesy\Instructor\Features\LLM\Data\PartialLLMResponse;

interface ProviderResponseAdapter
{
    public function fromResponse(array $data): ?LLMResponse;
    public function fromStreamResponse(array $data): ?PartialLLMResponse;
    public function fromStreamData(string $data): string|bool;
}