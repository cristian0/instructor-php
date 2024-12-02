<?php
namespace Cognesy\Instructor\Features\LLM;

use Cognesy\Instructor\Enums\Mode;
use Cognesy\Instructor\Events\EventDispatcher;
use Cognesy\Instructor\Features\Http\Contracts\CanHandleHttp;
use Cognesy\Instructor\Features\LLM\Contracts\CanHandleInference;
use Cognesy\Instructor\Features\LLM\Data\CachedContext;
use Cognesy\Instructor\Features\LLM\Data\LLMConfig;

/**
 * Class Inference
 *
 * Handles LLM inference operations including configuration management, HTTP client handling, and event dispatching.
 */
class Inference
{
    protected EventDispatcher $events;
    protected LLM $llm;
    protected CachedContext $cachedContext;

    /**
     * Constructor for initializing dependencies and configurations.
     *
     * @param LLM|null $llm LLM object.
     * @param EventDispatcher|null $events Event dispatcher.
     *
     * @return void
     */
    public function __construct(
        LLM                $llm = null,
        EventDispatcher    $events = null,
    ) {
        $this->events = $events ?? new EventDispatcher();
        $this->llm = $llm ?? new LLM(events: $this->events);
    }

    // STATIC //////////////////////////////////////////////////////////////////

    /**
     * Generates a text response based on the provided messages and configuration.
     *
     * @param string|array $messages The input messages to process.
     * @param string $connection The connection string.
     * @param string $model The model identifier.
     * @param array $options Additional options for the inference.
     *
     * @return string The generated text response.
     */
    public static function text(
        string|array $messages,
        string       $connection = '',
        string       $model = '',
        array        $options = []
    ): string {
        return (new Inference)
            ->withConnection($connection)
            ->create(
                messages: $messages,
                model: $model,
                options: $options,
                mode: Mode::Text,
            )
            ->toText();
    }

    // PUBLIC //////////////////////////////////////////////////////////////////

    public function withLLM(LLM $llm): self {
        $this->llm = $llm;
        return $this;
    }

    /**
     * Updates the configuration and re-initializes the driver.
     *
     * @param LLMConfig $config The configuration object to set.
     *
     * @return self
     */
    public function withConfig(LLMConfig $config): self {
        $this->llm->withConfig($config);
        return $this;
    }

    /**
     * Sets the connection and updates the configuration and driver.
     *
     * @param string $connection The connection string to be used.
     *
     * @return self Returns the current instance with the updated connection.
     */
    public function withConnection(string $connection): self {
        if (empty($connection)) {
            return $this;
        }
        $this->llm->withConnection($connection);
        return $this;
    }

    /**
     * Sets a custom HTTP client and updates the inference driver accordingly.
     *
     * @param CanHandleHttp $httpClient The custom HTTP client handler.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function withHttpClient(CanHandleHttp $httpClient): self {
        $this->llm->withHttpClient($httpClient);
        return $this;
    }

    /**
     * Sets the driver for inference handling and returns the current instance.
     *
     * @param CanHandleInference $driver The inference handler to be set.
     *
     * @return self
     */
    public function withDriver(CanHandleInference $driver): self {
        $this->llm->withDriver($driver);
        return $this;
    }

    /**
     * Enable or disable debugging for the current instance.
     *
     * @param bool $debug Whether to enable debug mode. Default is true.
     *
     * @return self
     */
    public function withDebug(bool $debug = true): self {
        $this->llm->withDebug($debug);
        return $this;
    }

    /**
     * Sets a cached context with provided messages, tools, tool choices, and response format.
     *
     * @param string|array $messages Messages to be cached in the context.
     * @param array $tools Tools to be included in the cached context.
     * @param string|array $toolChoice Tool choices for the cached context.
     * @param array $responseFormat Format for responses in the cached context.
     *
     * @return self
     */
    public function withCachedContext(
        string|array $messages = [],
        array        $tools = [],
        string|array $toolChoice = [],
        array        $responseFormat = [],
    ): self {
        $this->cachedContext = new CachedContext($messages, $tools, $toolChoice, $responseFormat);
        return $this;
    }

    /**
     * Creates an inference request and returns the inference response.
     *
     * @param InferenceRequest $request The inference request object.
     *
     * @return InferenceResponse The response from the inference request.
     */
    public function withRequest(InferenceRequest $request): InferenceResponse {
        return new InferenceResponse(
            response: $this->llm->handleInferenceRequest($request),
            driver: $this->llm->driver(),
            config: $this->llm->config(),
            isStreamed: $request->options['stream'] ?? false,
            events: $this->events,
        );
    }

    /**
     * Creates an inference request and returns the inference response.
     *
     * @param string|array $messages The input messages for the inference.
     * @param string $model The model to be used for the inference.
     * @param array $tools The tools to be used for the inference.
     * @param string|array $toolChoice The choice of tools for the inference.
     * @param array $responseFormat The format of the response.
     * @param array $options Additional options for the inference.
     * @param Mode $mode The mode of operation for the inference.
     *
     * @return InferenceResponse The response from the inference request.
     */
    public function create(
        string|array $messages = [],
        string       $model = '',
        array        $tools = [],
        string|array $toolChoice = [],
        array        $responseFormat = [],
        array        $options = [],
        Mode         $mode = Mode::Text
    ): InferenceResponse {
        return $this->withRequest(new InferenceRequest(
            messages: $messages,
            model: $model,
            tools: $tools,
            toolChoice: $toolChoice,
            responseFormat: $responseFormat,
            options: $options,
            mode: $mode,
            cachedContext: $this->cachedContext ?? null
        ));
    }
}
