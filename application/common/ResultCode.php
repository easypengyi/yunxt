<?php

namespace app\common;

/**
 * 结果代码
 */
class ResultCode
{
    // 默认成功代码
    const RES_SUCCESS = 200;
    // 默认失败代码
    const RES_PARAMETER_ERR = 199;
    // 未登录
    const RES_LOGIN_ERR = 201;
    // 其他客户端登录
    const RES_OTHER_LOGIN_ERR = 202;
    // 数据不存在
    const RES_NOT_DATA_ERR = 400;
    // 未开通会员
    const RES_MEMBER = 203;
}