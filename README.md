# PHP Option

一个简洁的 Option 类型实现，提供 `Some` 和 `None` 两种状态。

## 安装

```bash
composer require dmcz/option
```

## 使用

```php
use Dmcz\Option\Option;

$some = Option::some('value');
$none = Option::none();

$value = $some->unwrap();                   // 'value'
$default = $none->unwrap('fallback');       // 'fallback'
$lazy = $none->unwrap(fn () => compute());  // 仅在 None 时调用
```