<?php
namespace Cognesy\Instructor\Clients\Azure\Traits;

use Cognesy\Instructor\Enums\Mode;

trait HandlesRequestBody
{
    protected function model() : string {
        return $this->model;
    }

    public function messages(): array {
        return $this->messages;
    }

    public function tools(): array {
        return $this->tools;
    }

    public function getToolChoice(): string|array {
        if (empty($this->tools)) {
            return '';
        }
        return $this->toolChoice ?: 'auto';
    }

    protected function getResponseSchema() : array {
        return $this->jsonSchema ?? [];
    }

    protected function getResponseFormat(): array {
        return match($this->mode) {
            Mode::Json => ['type' => 'json_object'],
            default => $this->responseFormat ?? []
        };
    }
}
