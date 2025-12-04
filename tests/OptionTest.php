<?php

declare(strict_types=1);

namespace Dmcz\Option\Tests;

use Dmcz\Option\None;
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

    public function testMapAndFlatMap(): void
    {
        $mapped = Option::some(2)->map(fn (int $v): int => $v * 2);
        self::assertTrue($mapped->isSome());
        self::assertSame(4, $mapped->unwrap());

        $flatMapped = Option::some('x')->flatMap(fn (string $v): Option => Option::some($v . '!'));
        self::assertSame('x!', $flatMapped->unwrap());

        $noneMapped = Option::none()->map(fn (): int => 1);
        self::assertInstanceOf(None::class, $noneMapped);
        self::assertTrue($noneMapped->isNone());
    }

    public function testFilter(): void
    {
        $kept = Option::some(3)->filter(fn (int $v): bool => $v > 1);
        self::assertTrue($kept->isSome());
        self::assertSame(3, $kept->unwrap());

        $dropped = Option::some(0)->filter(fn (int $v): bool => $v > 1);
        self::assertTrue($dropped->isNone());
    }

    public function testOrElse(): void
    {
        $fallback = Option::none()->orElse(fn (): Option => Option::some('fallback'));
        self::assertSame('fallback', $fallback->unwrap());

        $unchanged = Option::some('keep')->orElse(fn (): Option => Option::some('other'));
        self::assertSame('keep', $unchanged->unwrap());
    }

    public function testGetOrElse(): void
    {
        self::assertSame('value', Option::some('value')->getOrElse('ignored'));
        self::assertSame('default', Option::none()->getOrElse('default'));

        $lazy = Option::none()->getOrElse(fn (): string => 'lazy');
        self::assertSame('lazy', $lazy);
    }

    public function testGetOrThrow(): void
    {
        self::assertSame(1, Option::some(1)->getOrThrow());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('custom');
        Option::none()->getOrThrow(fn (): LogicException => new LogicException('custom'));
    }

    public function testMatch(): void
    {
        $some = Option::some(5)->match(
            fn (int $v): string => "value:{$v}",
            fn (): string => 'none',
        );
        self::assertSame('value:5', $some);

        $none = Option::none()->match(
            fn (): string => 'some',
            fn (): string => 'none',
        );
        self::assertSame('none', $none);
    }

    public function testTapExecutesOnlyOnSome(): void
    {
        $calls = 0;
        Option::some('x')->tap(function () use (&$calls): void {
            ++$calls;
        });

        Option::none()->tap(function () use (&$calls): void {
            ++$calls;
        });

        self::assertSame(1, $calls);
    }
}
