<?php
namespace Cognesy\Instructor\Extras\Structure\Traits\Field;

use Cognesy\Instructor\Extras\Structure\Structure;
use Cognesy\Instructor\Features\Schema\Data\Schema\Schema;
use Cognesy\Instructor\Features\Schema\Data\TypeDetails;

trait HandlesFieldSchema
{
    private Schema $schema;

    public function name() : string {
        return $this->schema->name ?? '';
    }

    public function withName(string $name) : self {
        $this->schema->name = $name;
        // TODO: revise this
        if ($this->isStructure()) {
            $this->get()?->withName($name);
        }
        return $this;
    }

    public function description() : string {
        return $this->schema->description ?? '';
    }

    public function withDescription(string $description) : self {
        $this->schema->description = $description;
        // TODO: revise this
        if ($this->isStructure()) {
            $this->get()->withDescription($description);
        }
        return $this;
    }

    public function schema() : Schema {
        return match(true) {
            $this->isStructure() => $this->value->schema(),
            default => $this->schema,
        };
    }

    public function isStructure() : bool {
        return $this->schema->typeDetails->class === Structure::class;
    }

    public function typeDetails() : TypeDetails {
        return $this->schema->typeDetails;
    }

    public function nestedType() : TypeDetails {
        return $this->schema->typeDetails->nestedType;
    }
}