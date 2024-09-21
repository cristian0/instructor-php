<?php
namespace Cognesy\Instructor\Extras\LLM;

use Cognesy\Instructor\ApiClient\Responses\ApiResponse;
use Cognesy\Instructor\Extras\LLM\Contracts\CanInfer;
use Cognesy\Instructor\Extras\LLM\Drivers\AnthropicDriver;
use Cognesy\Instructor\Extras\LLM\Drivers\AzureOpenAIDriver;
use Cognesy\Instructor\Extras\LLM\Drivers\CohereDriver;
use Cognesy\Instructor\Extras\LLM\Drivers\GeminiDriver;
use Cognesy\Instructor\Extras\LLM\Drivers\MistralDriver;
use Cognesy\Instructor\Extras\LLM\Drivers\OpenAIDriver;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Cognesy\Instructor\Enums\Mode;
use Cognesy\Instructor\Utils\Settings;
use Cognesy\Instructor\ApiClient\Enums\ClientType;
use Cognesy\Instructor\ApiClient\Requests\ApiRequest;

class LLM
{
    protected Client $client;
    protected LLMConfig $config;
    protected CanInfer $driver;

    protected Mode $mode;
    protected array $body;
    protected array $headers;

    protected ApiRequest $apiRequest;
    protected string $schemaName;
    protected array $messages = [];
    protected array $tools;
    protected array $toolChoice;
    protected array $responseFormat;
    protected array $jsonSchema;
    protected array $cachedContext;
    protected array $options;

    public function __construct() {
        $this->client = new Client();
        $this->config = LLMConfig::load(Settings::get('llm', "defaultConnection"));
        $this->driver = $this->getDriver($this->config->clientType);
    }

    public function withConnection(string $connection): self {
        $this->config = LLMConfig::load($connection);
        $this->driver = $this->getDriver($this->config->clientType);
        return $this;
    }

    public function withModel(string $model): self {
        $this->config->model = $model;
        return $this;
    }

    public function infer(
        string|array $messages = [],
        string $model = '',
        array $tools = [],
        string|array $toolChoice = [],
        array $responseFormat = [],
        array $options = [],
        Mode $mode = Mode::Text
    ): ApiResponse {
        if (is_string($messages)) {
            $messages = [['role' => 'user', 'content' => $messages]];
        }
        return $this->driver->infer($messages, $model, $tools, $toolChoice, $responseFormat, $options, $mode);
    }

    // INTERNAL ///////////////////////////////////////

    protected function getDriver(ClientType $clientType): CanInfer {
        return match ($clientType) {
            ClientType::Anthropic => new AnthropicDriver($this->client, $this->config),
            ClientType::Azure => new AzureOpenAIDriver($this->client, $this->config),
            ClientType::Cohere => new CohereDriver($this->client, $this->config),
            ClientType::Fireworks => new OpenAIDriver($this->client, $this->config),
            ClientType::Gemini => new GeminiDriver($this->client, $this->config),
            ClientType::Groq => new OpenAIDriver($this->client, $this->config),
            ClientType::Mistral => new MistralDriver($this->client, $this->config),
            ClientType::Ollama => new OpenAIDriver($this->client, $this->config),
            ClientType::OpenAI => new OpenAIDriver($this->client, $this->config),
            ClientType::OpenAICompatible => new OpenAIDriver($this->client, $this->config),
            ClientType::OpenRouter => new OpenAIDriver($this->client, $this->config),
            ClientType::Together => new OpenAIDriver($this->client, $this->config),
            default => throw new InvalidArgumentException("Unknown client: {$this->client}"),
        };
    }
}
