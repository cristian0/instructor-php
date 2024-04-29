<?php

namespace Cognesy\Instructor\Events\Instructor;

use Cognesy\Instructor\Events\Event;
use Cognesy\Instructor\Utils\Json;

class InstructorDone extends Event
{
    public function __construct(
        mixed $data
    ) {
        parent::__construct($data);
    }

    public function __toString(): string {
        return Json::encode($this->data);
    }
}