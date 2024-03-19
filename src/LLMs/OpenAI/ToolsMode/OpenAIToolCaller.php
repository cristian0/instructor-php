<?php

namespace Cognesy\Instructor\LLMs\OpenAI\ToolsMode;

use Cognesy\Instructor\Contracts\CanCallFunction;
use Cognesy\Instructor\Core\Data\ResponseModel;
use Cognesy\Instructor\Events\EventDispatcher;
use Cognesy\Instructor\Utils\Result;
use OpenAI\Client;

class OpenAIToolCaller implements CanCallFunction
{
    public function __construct(
        private EventDispatcher $eventDispatcher,
        private Client $client,
    ) {}

    /**
     * Handle LLM function call
     */
    public function callFunction(
        array $messages,
        ResponseModel $responseModel,
        string $model,
        array $options,
    ) : Result {
        $request = array_merge([
            'model' => $model,
            'messages' => $messages,
            'tools' => [$responseModel->functionCall],
            'tool_choice' => [
                'type' => 'function',
                'function' => ['name' => $responseModel->functionName]
            ]
        ], $options);

        return match($options['stream'] ?? false) {
            true => (new StreamedToolCallHandler($this->eventDispatcher, $this->client, $request, $responseModel))->handle(),
            default => (new ToolCallHandler($this->eventDispatcher, $this->client, $request, $responseModel))->handle()
        };
    }
}