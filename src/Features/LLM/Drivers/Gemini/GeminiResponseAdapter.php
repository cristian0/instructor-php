<?php

namespace Cognesy\Instructor\Features\LLM\Drivers\Gemini;

use Cognesy\Instructor\Features\LLM\Contracts\ProviderResponseAdapter;
use Cognesy\Instructor\Features\LLM\Data\LLMResponse;
use Cognesy\Instructor\Features\LLM\Data\PartialLLMResponse;
use Cognesy\Instructor\Features\LLM\Data\ToolCall;
use Cognesy\Instructor\Features\LLM\Data\ToolCalls;
use Cognesy\Instructor\Features\LLM\Data\Usage;
use Cognesy\Instructor\Utils\Json\Json;

class GeminiResponseAdapter implements ProviderResponseAdapter
{
    public function fromResponse(array $data): ?LLMResponse {
        return new LLMResponse(
            content: $this->makeContent($data),
            finishReason: $data['candidates'][0]['finishReason'] ?? '',
            toolCalls: $this->makeToolCalls($data),
            usage: $this->makeUsage($data),
            responseData: $data,
        );
    }

    public function fromStreamResponse(array $data): ?PartialLLMResponse {
        if (empty($data)) {
            return null;
        }
        return new PartialLLMResponse(
            contentDelta: $this->makeContentDelta($data),
            toolId: $data['candidates'][0]['id'] ?? '',
            toolName: $this->makeToolName($data),
            toolArgs: $this->makeToolArgs($data),
            finishReason: $data['candidates'][0]['finishReason'] ?? '',
            usage: $this->makeUsage($data),
            responseData: $data,
        );
    }

    public function fromStreamData(string $data): string|bool {
        if (!str_starts_with($data, 'data:')) {
            return '';
        }
        $data = trim(substr($data, 5));
        return match(true) {
            $data === '[DONE]' => false,
            default => $data,
        };
    }

    // INTERNAL /////////////////////////////////////////////

    private function makeToolCalls(array $data) : ToolCalls {
        return ToolCalls::fromMapper(array_map(
            callback: fn(array $call) => $call['functionCall'] ?? [],
            array: $data['candidates'][0]['content']['parts'] ?? []
        ), fn($call) => ToolCall::fromArray(['name' => $call['name'] ?? '', 'arguments' => $call['args'] ?? '']));
    }

    private function makeContent(array $data) : string {
        $partCount = count($data['candidates'][0]['content']['parts'] ?? []);
        if ($partCount === 1) {
            return $this->makeContentPart($data, 0);
        }
        $content = '';
        for ($i = 0; $i < $partCount; $i++) {
            $part = $this->makeContentPart($data, $i) . "\n\n";
            $content .= $part;
        }
        return $content;
    }

    private function makeContentPart(array $data, int $index) : string {
        return $data['candidates'][0]['content']['parts'][$index]['text']
            ?? Json::encode($data['candidates'][0]['content']['parts'][$index]['functionCall']['args'] ?? '')
            ?? '';
    }

    private function makeContentDelta(array $data): string {
        $partCount = count($data['candidates'][0]['content']['parts'] ?? []);
        if ($partCount === 1) {
            return  $this->makeContentDeltaPart($data, 0);
        }

        $content = '';
        for ($i = 0; $i < $partCount; $i++) {
            $part = $this->makeContentDeltaPart($data, $i) . "\n";
            $content .= $part;
        }
        return $content;
    }

    private function makeContentDeltaPart(array $data, int $index) : string {
        return $data['candidates'][0]['content']['parts'][$index]['text']
            ?? Json::encode($data['candidates'][0]['content']['parts'][$index]['functionCall']['args'] ?? '')
            ?? '';
    }

    private function makeToolName(array $data) : string {
        return $data['candidates'][0]['content']['parts'][0]['functionCall']['name'] ?? '';
    }

    private function makeToolArgs(array $data) : string {
        $value = $data['candidates'][0]['content']['parts'][0]['functionCall']['args'] ?? '';
        return is_array($value) ? Json::encode($value) : '';
    }

    private function makeUsage(array $data) : Usage {
        return new Usage(
            inputTokens: $data['usageMetadata']['promptTokenCount'] ?? 0,
            outputTokens: $data['usageMetadata']['candidatesTokenCount'] ?? 0,
            cacheWriteTokens: 0,
            cacheReadTokens: 0,
            reasoningTokens: 0,
        );
    }
}