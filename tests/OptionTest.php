<?php

declare(strict_types=1);

namespace Dmcz\Option\Tests;

use Dmcz\Option\Option;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class OptionTest extends TestCase
{
    public function testUnwrapReturnsStoredValueWhenSome(): void
    {
        $option = Option::some('value');

        self::assertTrue($option->isSome());
        self::assertFalse($option->isNone());
        self::assertSame('value', $option->unwrap());
    }

    public function testUnwrapThrowsWhenNoneWithoutDefault(): void
    {
        $option = Option::none();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Tried to unwrap None');

        $option->unwrap();
    }

    public function testUnwrapReturnsProvidedDefaultWhenNone(): void
    {
        $option = Option::none();

        self::assertSame('fallback', $option->unwrap('fallback'));
        self::assertSame(0, $option->unwrap(0));
    }

    public function testUnwrapEvaluatesDefaultLazily(): void
    {
        $defaultCalls = 0;
        $default = function () use (&$defaultCalls): string {
            ++$defaultCalls;

            return 'computed';
        };

        self::assertSame('stored', Option::some('stored')->unwrap($default));
        self::assertSame(0, $defaultCalls);

        $none = Option::none();
        self::assertSame('computed', $none->unwrap($default));
        self::assertSame(1, $defaultCalls);
    }
}
