<?php
namespace Cognesy\Instructor\Experimental\Module\Modules;

use Closure;
use Cognesy\Instructor\Experimental\Module\Core\Module;
use Cognesy\Instructor\Experimental\Module\Signature\Signature;
use Cognesy\Instructor\Experimental\Module\Signature\SignatureFactory;

class CallClosure extends Module
{
    protected Closure $callable;
    protected Signature $signature;

    public function __construct(Closure $callable) {
        $this->callable = $callable;
        $this->signature = SignatureFactory::fromCallable($this->callable);
    }

    public function signature(): Signature {
        return $this->signature;
    }

    public function for(mixed ...$args): mixed {
        return ($this)(...$args)->get('result');
    }

    public function forward(mixed ...$args): array {
        return [
            'result' => ($this->callable)(...$args)
        ];
    }
}