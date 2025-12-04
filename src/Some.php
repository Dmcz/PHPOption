<?php

declare(strict_types=1);

namespace Dmcz\Option;

use LogicException;

/**
 * @template T
 * @extends Option<T>
 * @internal Only for type annotation. @Use Option::none() to obtain the singleton; do not instantiate directly.
 */
final class Some extends Option
{
    /**
     * @param T $value
     */
    protected function __construct(private readonly mixed $value)
    {
        parent::__construct(Tag::Some);
    }

    public function isSome(): bool
    {
        return true;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function unwrap(mixed $default = null): mixed
    {
        return $this->value;
    }

    public function map(callable $mapper): Option
    {
        return Option::some($mapper($this->value));
    }

    public function flatMap(callable $mapper): Option
    {
        $result = $mapper($this->value);

        /* @phpstan-ignore-next-line */
        if (! $result instanceof Option) {
            throw new LogicException('flatMap 回调必须返回 Option 实例');
        }

        return $result;
    }

    public function filter(callable $predicate): Option
    {
        if ($predicate($this->value)) {
            return $this;
        }

        return Option::none();
    }

    public function orElse(callable|Option $fallback): Option
    {
        return $this;
    }

    public function getOrElse(mixed $default): mixed
    {
        return $this->value;
    }

    public function getOrThrow(?callable $exceptionFactory = null): mixed
    {
        return $this->value;
    }

    public function match(callable $onSome, callable $onNone): mixed
    {
        return $onSome($this->value);
    }

    public function tap(callable $consumer): Option
    {
        $consumer($this->value);

        return $this;
    }
}
