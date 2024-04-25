<?php
namespace Cognesy\Instructor\Clients\TogetherAI\ToolsCall;

use Cognesy\Instructor\ApiClient\Requests\ApiToolsCallRequest;

class ToolsCallRequest extends ApiToolsCallRequest
{
    protected string $endpoint = '/chat/completions';

    protected function getToolChoice(): string|array {
        return $this->toolChoice ?: 'auto';
    }
}
