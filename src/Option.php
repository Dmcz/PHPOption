<?php

declare(strict_types=1);

namespace Dmcz\Option;

use LogicException;

final class Option
{
    private function __construct(
        public readonly Tag $tag,
        private readonly mixed $value = null,
    ) {
    }

    /**
     * Create an Option that holds a concrete value.
     */
    public static function some(mixed $value): self
    {
        return new self(Tag::Some, $value);
    }

    /**
     * Create an Option that holds no value.
     */
    public static function none(): self
    {
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
