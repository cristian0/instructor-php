<?php

namespace Cognesy\Instructor\ApiClient\Factories;

use Cognesy\Instructor\ApiClient\ApiClient;
use Cognesy\Instructor\ApiClient\Contracts\CanCallApi;
use Cognesy\Instructor\Events\EventDispatcher;

class ApiClientFactory
{
    protected CanCallApi $defaultClient;

    public function __construct(
        public EventDispatcher $events,
        public ApiRequestFactory $apiRequestFactory,
        public array $clients = [],
    ) {}

    public function fromName(string $clientName) : CanCallApi {
        if (!isset($this->clients[$clientName])) {
            throw new \InvalidArgumentException("Client '$clientName' does not exist");
        }

        $client = $this->clients[$clientName];
        if (!$client instanceof ApiClient) {
            throw new \InvalidArgumentException("Client '$clientName' is not an instance of ApiClient");
        }

        return $client->withApiRequestFactory($this->apiRequestFactory);
    }

    public function fromClient(CanCallApi $client) : ApiClient {
        return $client
            ->withEventDispatcher($this->events)
            ->withApiRequestFactory($this->apiRequestFactory);
    }

    public function getDefault() : CanCallApi {
        if (!$this->defaultClient) {
            throw new \RuntimeException("No default client has been set");
        }
        return $this->defaultClient;
    }

    public function setDefault(CanCallApi $client) : self {
        $this->defaultClient = $client
            ->withEventDispatcher($this->events)
            ->withApiRequestFactory($this->apiRequestFactory);
        return $this;
    }
}
