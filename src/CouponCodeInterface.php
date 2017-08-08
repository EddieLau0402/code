<?php

namespace Eddie\CouponCode;


interface CouponCodeInterface
{
    /**
     * 手机号
     *
     * @param $mobile
     * @return mixed
     */
    public function mobile($mobile);

    /**
     * 串码提取.
     *
     * @return array
     */
    public function extract();

    /**
     * 返回结果格式化
     *
     * 应该返回标准格式:
     *     [
     *          'provider' => string,  // 服务供应商
     *          'success'  => boolean, // 是否成功
     *          'code'     => integer, // 返回状态码
     *          'msg'      => string,  // 返回消息
     *          'order_sn' => string,  // 系统生成订单号
     *          'card_no'  => string,  // 卡号
     *          'password' => string   // 密码
     *     ]
     *
     * @return mixed
     */
    public function transform($response);
}