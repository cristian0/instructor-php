<?php

namespace Cognesy\Instructor\Extras\Tasks\Signature;

use Cognesy\Instructor\Data\ResponseModel;
use Cognesy\Instructor\Extras\Structure\Contracts\CanHandleDataStructure;
use Cognesy\Instructor\Extras\Tasks\Signature\Contracts\Signature;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class ResponseModelSignature implements Signature
{
    protected string $signatureString;
    protected string $description;
    protected ResponseModel $inputModel;
    protected ResponseModel $outputModel;

    public function description(): string {
        return $this->description;
    }

    public function getInputs(): CanHandleDataStructure {
        return $this->inputModel;
    }

    public function getInputFields(): array {
    }

    public function getInputValues(): array {
        // TODO: Implement asInputArgs() method.
    }

    public function getOutputs(): CanHandleDataStructure
    {
        // TODO: Implement getOutputs() method.
    }

    public function getOutputFields(): array
    {
        // TODO: Implement getOutputFields() method.
    }

    public function getOutputValues(): array
    {
        // TODO: Implement asOutputValues() method.
    }

    public function toString(): string
    {
        // TODO: Implement toString() method.
    }
}