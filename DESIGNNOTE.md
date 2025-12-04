# DESIGN NOTE
## 泛型字母
* 每个方法里描述的“输入类型”和“输出类型”是独立的，所以用不同字母避免混淆.
    * T: 表示 Option 当前承载的值
    * U: 表示 map 的产出
    * S: 表示行为分派时Some的返回
    * N: 表示行为分派时None的返回

## 将Optional分化成Some和None
* 优势
  * Option 和 Some 既是静态检查收窄的载体，也是运行时行为分派（Some::map 映射值，None::map 原样返回）的载体
  * 避免了多层 if else 包裹，且单元测试更容易书写
  * Some 和 None 作为类的情况下 instanceof 有了明确的语义
* 弊端
  * 脱离 Option 单独声明或实例化 Some/None 都没有实际意义。（当前设计已经封闭这两个类，这两个类无法直接实例化，这里仅讨论他们独立时语义上的价值）。
    * 若定义 a 为 Some 类型，那 a 本质就是通过范型指定了类型的变量，此时Some中的方法对与这个 a 来说没有任何意义，也是就 a 约等于 mixed。例如:
    ```php
    // 直接定义一个 Some 没有任何意义
    /* @param Some<string> $a */
    function test1(Some $a){}
    test1(new Some("abc")); // 这里只是为了阐述这样用没有意义，some无法直接构造

    // Some 必须和 Option 一起用才有意义 
    /* @param Option<string> $a */
    function test2(Option $a){} 
    test1(new Some("abc")); // 这里只是为了阐述这样用没有意义，some无法直接构造
    
    ```
    * None 在某些情况可以用来区别 null，但这偏离了这个类设计的本质。
  * Some 和 None 为了不对外暴露实现（None也节省了实例化开支）而封闭了 __construct，使用者需要知道这点并通过工厂方法获取实例。
* 结论
  * 将 Option 在内部分化成 Some 和 None。 内部用 Some/None 分派行为和提供类型精度。
  * 用户不应该直接操作 Some 和 None，不应该依赖他们，所以的操作、类型定义都紧紧依赖于Option