<?php
namespace Cognesy\config;

use Cognesy\Instructor\Contracts\CanCallFunction;
use Cognesy\Instructor\Contracts\CanDeserializeResponse;
use Cognesy\Instructor\Contracts\CanValidateResponse;
use Cognesy\Instructor\Core\EventDispatcher;
use Cognesy\Instructor\Core\RequestHandler;
use Cognesy\Instructor\Core\ResponseHandler;
use Cognesy\Instructor\Core\ResponseModelFactory;
use Cognesy\Instructor\Deserializers\Symfony\Deserializer;
use Cognesy\Instructor\LLMs\OpenAI\OpenAIFunctionCaller;
use Cognesy\Instructor\Schema\Factories\FunctionCallFactory;
use Cognesy\Instructor\Schema\Factories\SchemaFactory;
use Cognesy\Instructor\Schema\Factories\TypeDetailsFactory;
use Cognesy\Instructor\Schema\PropertyMap;
use Cognesy\Instructor\Schema\SchemaMap;
use Cognesy\Instructor\Schema\Utils\ReferenceQueue;
use Cognesy\Instructor\Schema\Utils\SchemaBuilder;
use Cognesy\Instructor\Utils\Configuration;
use Cognesy\Instructor\Validators\Symfony\Validator;

function autowire(Configuration $config) : Configuration
{
    $config->declare(
        class: Deserializer::class,
        name: CanDeserializeResponse::class
    );
    $config->declare(class: EventDispatcher::class);
    $config->declare(
        class: FunctionCallFactory::class,
        context: [
            'schemaFactory' => $config->reference(SchemaFactory::class),
            'schemaBuilder' => $config->reference(SchemaBuilder::class),
            'referenceQueue' => $config->reference(ReferenceQueue::class),
        ]
    );
    $config->declare(
        class: OpenAIFunctionCaller::class,
        name: CanCallFunction::class,
        context: [
            'eventDispatcher' => $config->reference(EventDispatcher::class),
            'apiKey' => '',
            'baseUri' => 'https://api.openai.com/v1/',
            'organization' => '',
        ]
    );
    $config->declare(class: PropertyMap::class);
    $config->declare(class: ReferenceQueue::class);
    $config->declare(
        class: RequestHandler::class,
        context: [
            'llm' => $config->reference(CanCallFunction::class),
            'responseModelFactory' => $config->reference(ResponseModelFactory::class),
            'eventDispatcher' => $config->reference(EventDispatcher::class),
            'responseHandler' => $config->reference(ResponseHandler::class),
        ]
    );
    $config->declare(
        class: ResponseHandler::class,
        context: [
            'eventDispatcher' => $config->reference(EventDispatcher::class),
            'deserializer' => $config->reference(CanDeserializeResponse::class),
            'validator' => $config->reference(CanValidateResponse::class),
        ]
    );
    $config->declare(
        class: ResponseModelFactory::class,
        context: [
            'functionCallFactory' => $config->reference(FunctionCallFactory::class),
            'responseDeserializer' => $config->reference(CanDeserializeResponse::class),
            'responseValidator' => $config->reference(CanValidateResponse::class),
        ]
    );
    $config->declare(class: SchemaMap::class);
    $config->declare(class: SchemaBuilder::class);
    $config->declare(
        class: SchemaFactory::class,
        context: [
            'schemaMap' => $config->reference(SchemaMap::class),
            'propertyMap' => $config->reference(PropertyMap::class),
            'typeDetailsFactory' => $config->reference(TypeDetailsFactory::class),
            'useObjectReferences' => false,
        ]
    );
    $config->declare(class: TypeDetailsFactory::class);
    $config->declare(
        class: Validator::class,
        name: CanValidateResponse::class,
    );

    return $config;
}
