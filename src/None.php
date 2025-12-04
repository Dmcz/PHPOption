<?php

declare(strict_types=1);

namespace Dmcz\Option;

use LogicException;

/**
 * @extends Option<never>
 * @internal Only for type annotation. @Use Option::none() to obtain the singleton; do not instantiate directly.
 */
final class None extends Option
{
    private static ?self $instance = null;

    private function __construct()
    {
        parent::__construct(Tag::None);
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function isSome(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function unwrap(mixed $default = null): mixed
    {
        if (func_num_args() === 0) {
            throw new LogicException('Tried to unwrap None');
        }

        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }

    public function map(callable $mapper): Option
    {
        return $this;
    }

    public function flatMap(callable $mapper): Option
    {
        return $this;
    }

    public function filter(callable $predicate): Option
    {
        return $this;
    }

    public function orElse(callable|Option $fallback): Option
    {
        if (is_callable($fallback)) {
            $fallback = $fallback();

            if (! $fallback instanceof Option) {
                throw new LogicException('flatMap 回调必须返回 Option 实例');
            }
        }

        return $fallback;
    }

    public function getOrElse(mixed $default): mixed
    {
        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }

    public function getOrThrow(?callable $exceptionFactory = null): mixed
    {
        if ($exceptionFactory !== null) {
            $exception = $exceptionFactory();
            throw $exception;
        }

        throw new LogicException('Tried to unwrap None');
    }

    public function match(callable $onSome, callable $onNone): mixed
    {
        return $onNone();
    }

    public function tap(callable $consumer): Option
    {
        return $this;
    }
}
