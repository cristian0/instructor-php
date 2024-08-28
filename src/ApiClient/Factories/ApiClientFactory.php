<?php
namespace Cognesy\Instructor\ApiClient\Factories;

use Cognesy\Instructor\ApiClient\Contracts\CanCallApi;
use Cognesy\Instructor\ApiClient\Enums\ClientType;
use Cognesy\Instructor\Events\EventDispatcher;
use InvalidArgumentException;

class ApiClientFactory
{
    protected CanCallApi $defaultClient;
    protected array $config = [];

    public function __construct(
        public string $configPath,
        public EventDispatcher $events,
        public ApiRequestFactory $apiRequestFactory,
    ) {
        $this->config = $this->readConfig($this->configPath);

        $defaultConnection = $this->config['defaultConnection'] ?? null;
        if (!$defaultConnection) {
            throw new InvalidArgumentException("No default client connection found");
        }
        $this->defaultClient = $this->client($defaultConnection);
    }

    public function client(string $connection) : CanCallApi {
        $clientConfig = $this->config['connections'][$connection] ?? null;
        if (!$clientConfig) {
            throw new InvalidArgumentException("No client connection config found for '{$connection}'");
        }
        return $this
            ->getFromConfig($clientConfig)
            ->withApiRequestFactory($this->apiRequestFactory);
    }

    public function getDefault() : CanCallApi {
        return $this->defaultClient;
    }

    public function setDefault(CanCallApi $client) : self {
        $this->defaultClient = $client;
        return $this;
    }

    // INTERNAL //////////////////////////////////////////////////////////////////////

    protected function getFromConfig(array $config): CanCallApi {
        $clientType = ClientType::from($config['clientType']);
        $clientClass = $clientType->toClientClass();
        return (new $clientClass(
            apiKey: $config['apiKey'] ?? '',
            baseUri: $config['apiUrl'] ?? '',
            connectTimeout: $config['connectTimeout'] ?? 3,
            requestTimeout: $config['requestTimeout'] ?? 30,
            metadata: $config['metadata'] ?? [],
            events: $this->events,
        ))
            ->withModel($config['defaultModel'] ?? '')
            ->withMaxTokens($config['defaultMaxTokens'] ?? 1024);
    }

    protected function readConfig(string $configPath): array {
        return require __DIR__ . $configPath;
    }
}
