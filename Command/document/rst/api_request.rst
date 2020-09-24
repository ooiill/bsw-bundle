
◈ 接口请求相关
========================================

HEADER参数
----------------------------------------

.. list-table::
    :widths: 20 15 65
    :class: bsw-doc-table-header

    * - **Name**
      - **Required**
      - **Description**

    * - ``time``
      - **Y**
      - 十位数时间戳

    * - ``sign``
      - **Y**
      - 签名字符串

    * - ``lang``
      - **N**
      - 语言包，支持 ``cn``、``en``，默认 ``cn``

    * - ``token``
      - **N+**
      - 用户登录凭证，将在登录或注册时下发到客户端存储

    * - ``os``
      - **N+**
      - 用户操作系统，支持 ``Android``、``iOS``、``Windows``、``Mac``，默认 ``Unknown``

    * - ``ua``
      - **N+**
      - 用户代理（设备详情），User-Agent

    * - ``device``
      - **N+**
      - 用户设备唯一标识

    * - ``sign-close``
      - **N**
      - [生产] 关闭签名，常用于 ``postman`` 频繁改动参数调试

    * - ``sign-debug``
      - **N**
      - [生产] 签名调试，将返回未哈希的签名字符串

    * - ``sign-dynamic``
      - **N**
      - [线上] 激活 **[生产]** 项配置，用于调试线上环境签名问题

.. note::

    **N+** 标识表示部分接口为必须，必须时验证不通过将会响应相应的错误。

签名算法
----------------------------------------

1. 根据文档参数列表得到参数数组 **$args**，第二步开始为计算签名的顺序。

    .. code-block:: php

        // 假如是登录接口，参数结果如下
        // 这也是最终发送请求时携带的数据
        $args = [
            'user' => 'hello',
            'pass' => '123456,
        ];

#. 获得当前时间戳并添加到 ``$args`` 中 (**key** 为 ``time``)，并且时间参数也要参照上段落文档传入 **HEADER** 中。

    .. code-block:: php

        $args['time'] = time();

#. 将得到的参数数组按 **key** 进行倒序排列。

    .. code-block:: php

        krsort($args);

        // 排序后结果如下
        /*
        $args = [
            'user' => 'hello',
            'time' => 1542851544,
            'pass' => '123456,
        ];
        */

#. 遍历参数数组按 **k1 is v1 and k2 is v2...** 拼接成字符串(注意空格)。

    .. code-block:: php

        $sign = [];
        foreach($args as $key => $value) {
            array_push($sign, "{$key} is {$value}");
        }
        $sign = implode(' and ', $sign);

        // 拼接结果如下
        // $sign = "user is hello and time is 1542851544 and pass is 123456";

#. 在拼接好的字符串尾部追加拼接签名秘钥 **{$sign} & {$salt}**。

    .. code-block:: php

        $sign .= " & {$salt}";

        // 假如秘钥为 abc, 结果如下
        // $sign = "user is hello and time is 1542851544 and pass is 123456 & abc"

#. 对上述得到的字符串使用 **md5** 进行哈希并转小写得到最终的32位签名字符串。

    .. code-block:: php

        $sign = strtolower(md5($sign));
        // sign = "1acdb7b5f817e95ef82bd303b398b7cc";

#. 将得到的签名字符串参照上段落文档传入 **HEADER** 中，并在请求接口时携带 **步骤一** 得到的参数数组。

特别声明
----------------------------------------

以上涉及到 **salt** 等秘钥串请于开发者处获取（或于开放平台获取）。