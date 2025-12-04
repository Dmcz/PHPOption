<?php

declare(strict_types=1);

namespace Dmcz\Option;

use Throwable;

/**
 * @template T
 */
abstract class Option
{
    protected function __construct(
        public readonly Tag $tag,
    ) {
    }

    /**
     * 创建一个包含值的 Some。
     *
     * @template S
     * @param S $value
     * @return Option<S>
     */
    public static function some(mixed $value): Option
    {
        return new Some($value);
    }

    /**
     * 创建一个 None。
     *
     * @return Option<never>
     */
    public static function none(): Option
    {
        return None::instance();
    }

    abstract public function isSome(): bool;

    abstract public function isNone(): bool;

    /**
     * 获取内部值或默认值；当 None 且未提供默认值时抛出异常。
     *
     * @template D
     * @param null|(callable():D)|D $default
     * @return null|D|T
     */
    abstract public function unwrap(mixed $default = null): mixed;

    /**
     * @template U
     * @param callable(T):U $mapper
     * @return Option<U>
     */
    abstract public function map(callable $mapper): self;

    /**
     * @template U
     * @param callable(T):Option<U> $mapper
     * @return Option<U>
     */
    abstract public function flatMap(callable $mapper): self;

    /**
     * @param callable(T):bool $predicate
     * @return Option<T>
     */
    abstract public function filter(callable $predicate): self;

    /**
     * @template S
     * @param callable():Option<S>|Option<S> $fallback
     * @return Option<S|T>
     */
    abstract public function orElse(callable|Option $fallback): self;

    /**
     * 获取内部值或返回备用值。
     *
     * @template D
     * @param callable():D|D $default
     * @return D|T
     */
    abstract public function getOrElse(mixed $default): mixed;

    /**
     * 获取内部值；当 None 时抛出异常。
     *
     * @param null|callable():Throwable $exceptionFactory
     * @return T
     */
    abstract public function getOrThrow(?callable $exceptionFactory = null): mixed;

    /**
     * 模式匹配。
     *
     * @template S
     * @template N
     * @param callable(T):S $onSome
     * @param callable():N $onNone
     * @return N|S
     */
    abstract public function match(callable $onSome, callable $onNone): mixed;

    /**
     * 消费内部值（仅 Some 时执行），返回原 Option，便于链式调用。
     *
     * @param callable(T):void $consumer
     * @return Option<T>
     */
    abstract public function tap(callable $consumer): self;
}
