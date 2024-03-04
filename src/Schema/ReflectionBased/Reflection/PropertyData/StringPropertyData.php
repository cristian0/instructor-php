<?php
namespace Cognesy\Instructor\Schema\ReflectionBased\Reflection\PropertyData;

use Cognesy\Instructor\Schema\ReflectionBased\Data\FCAtom;
use Cognesy\Instructor\Schema\ReflectionBased\Reflection\Enums\JsonType;
use Cognesy\Instructor\Schema\ReflectionBased\Reflection\Enums\PhpType;
use Cognesy\Instructor\Schema\ReflectionBased\Reflection\TypeDefs\TypeDef;

class StringPropertyData extends PropertyData {
    public PhpType $type = PhpType::STRING;

    public function toStruct(): FCAtom {
        $fcAtom = new FCAtom();
        $fcAtom->name = $this->name;
        $fcAtom->type = JsonType::STRING->value;
        $fcAtom->description = $this->description;
        return $fcAtom;
    }

    public static function asArrayItem(TypeDef $typeDef) : StringPropertyData {
        $itemType = new StringPropertyData();
        $itemType->name = 'items';
        $itemType->type = PhpType::OBJECT;
        return $itemType;
    }
}