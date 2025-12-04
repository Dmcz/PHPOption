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

    /**
     * @return false
     */
    public function isSome(): bool
    {
        return false;
    }

    /**
     * @return true
     */
    public function isNone(): bool
    {
        return true;
    }

    /**
     * @template D
     * @param null|(callable():D)|D $default
     * @return D|null
     *
     * @throws LogicException
     */
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

    /**
     * @return None
     */
    public function map(callable $mapper): static
    {
        return $this;
    }

    /**
     * @return None
     */
    public function flatMap(callable $mapper): Option
    {
        return $this;
    }

    /**
     * @return Option<never>
     */
    public function filter(callable $predicate): Option
    {
        return $this;
    }

    /**
     * @template S
     * @param callable():Option<S>|Option<S> $fallback
     * @return Option<S>
     *
     * @throws LogicException
     */
    public function orElse(callable|Option $fallback): Option
    {
        if (is_callable($fallback)) {
            $fallback = $fallback();

            if (! $fallback instanceof Option) {
                throw new LogicException('Must return Option');
            }
        }

        return $fallback;
    }

    /**
     * @template D
     * @param callable():D|D $default
     * @return D
     */
    public function getOrElse(mixed $default): mixed
    {
        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }

    /**
     * @param null|callable():\Throwable $exceptionFactory
     * @return never
     *
     * @throws \Throwable
     */
    public function getOrThrow(?callable $exceptionFactory = null): mixed
    {
        if ($exceptionFactory !== null) {
            $exception = $exceptionFactory();
            throw $exception;
        }

        throw new LogicException('Tried to unwrap None');
    }

    /**
     * @template S
     * @template N
     * @param callable(never):S $onSome
     * @param callable():N $onNone
     * @return N
     */
    public function match(callable $onSome, callable $onNone): mixed
    {
        return $onNone();
    }

    /**
     * @param callable(never):void $consumer
     * @return None
     */
    public function tap(callable $consumer): static
    {
        return $this;
    }
}
