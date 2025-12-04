# PHP Option

一个简洁的 Option 类型实现，提供 `Some` 和 `None` 两种状态。

## 安装

```bash
composer require dmcz/option
```

## 使用
* Some 和 None 仅用于类型标记，构造函数已封闭，不能直接 new。请始终通过 Option::some($value) 创建 Some，通过 Option::none() 获取唯一的 None 单例。
* 示例

```php
use Dmcz\Option\Option;

$some = Option::some('value');
$none = Option::none();

// 解包
$value = $some->unwrap();                   // 'value'
$default = $none->unwrap('fallback');       // 'fallback'
$lazy = $none->unwrap(fn () => compute());  // 仅在 None 时调用

// 链式操作
$result = Option::some(2)
    ->map(fn (int $v) => $v * 2)
    ->filter(fn (int $v) => $v > 2)
    ->flatMap(fn (int $v) => Option::some((string) $v))
    ->match(
        fn (string $v) => "Some: $v",
        fn () => 'None',
    );
// "Some: 4"

// None 时兜底
$fallback = Option::none()
    ->orElse(fn () => Option::some('backup'))
    ->getOrElse('default'); // 'backup'
```
