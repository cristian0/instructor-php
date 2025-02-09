---
title: 'Customize parameters of LLM driver'
docname: 'custom_llm'
---

## Overview

You can provide your own LLM configuration instance to Instructor. This is useful
when you want to initialize OpenAI client with custom values - e.g. to call
other LLMs which support OpenAI API.

## Example

```php
<?php
$loader = require 'vendor/autoload.php';
$loader->add('Cognesy\\Instructor\\', __DIR__ . '../../src/');

use Cognesy\Instructor\Enums\Mode;
use Cognesy\Instructor\Features\LLM\Data\LLMConfig;
use Cognesy\Instructor\Features\LLM\Enums\LLMProviderType;
use Cognesy\Instructor\Instructor;
use Cognesy\Instructor\Utils\Env;

class User {
    public int $age;
    public string $name;
}

// Create instance of LLM client initialized with custom parameters
$config = new LLMConfig(
    apiUrl: 'https://api.together.xyz/v1',
    apiKey: Env::get('TOGETHER_API_KEY'),
    endpoint: '/chat/completions',
    model: 'mistralai/Mixtral-8x7B-Instruct-v0.1',
    maxTokens: 128,
    httpClient: 'guzzle',
    providerType: LLMProviderType::OpenAICompatible,
);

// Get Instructor with the default client component overridden with your own
$instructor = (new Instructor)->withLLMConfig($config);

// Call with custom model and execution mode
$user = $instructor->respond(
    messages: "Our user Jason is 25 years old.",
    responseModel: User::class,
    mode: Mode::Tools,
);


dump($user);

assert(isset($user->name));
assert(isset($user->age));
?>
```
