<?php

declare(strict_types=1);

namespace Dmcz\Option;

use LogicException;

/**
 * @template-covariant T
 */
final class Option
{
    /**
     * Internal constructor; prefer the named constructors.
     *
     * @param T $value
     */
    private function __construct(
        public readonly Tag $tag,
        private readonly mixed $value = null,
    ) {
    }

    /**
     * Create an Option that holds a concrete value.
     *
     * @template S
     * @param S $value
     * @return self<S>
     */
    public static function some(mixed $value): self
    {
        return new self(Tag::Some, $value);
    }

    /**
     * Create an Option that holds no value.
     *
     * @return self<never>
     */
    public static function none(): self
    {
        /** @var self<never> */
        return new self(Tag::None);
    }

    /**
     * Whether the Option currently holds a value.
     */
    public function isSome(): bool
    {
        return $this->tag === Tag::Some;
    }

    /**
     * Whether the Option is empty.
     */
    public function isNone(): bool
    {
        return $this->tag === Tag::None;
    }

    /**
     * Return the contained value or a default.
     *
     * When called on None with no default, a LogicException is thrown.
     * If a callable default is provided, it is invoked lazily only for None.
     *
     * @template D
     * @param null|(callable():D)|D $default
     * @return null|D|T
     */
    public function unwrap(mixed $default = null): mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }

        // Throw an exception if no passed.
        if (func_num_args() === 0) {
            throw new LogicException('Tried to unwrap None');
        }

        // support lazily
        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }
}
