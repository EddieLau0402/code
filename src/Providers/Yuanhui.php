<?php

namespace Eddie\CouponCode\Providers;

use Eddie\CouponCode\Util;
use Eddie\CouponCode\CouponCodeInterface;

class Yuanhui implements CouponCodeInterface
{
    use Util;


    /*
     * API uri
     */
    //// http://xxxxxxxx/API/CodeGet.ashx
    const API_COUPON_CODE_GET = 'CodeGet.ashx'; // 串码提取


    /**
     * 服务地址
     *
     * @var
     */
    protected $server;

    /**
     * 获取 API 校验
     *
     * @var
     */
    protected $appkey;

    /**
     * 客户/账号
     *
     * @var
     */
    protected $cid;

    /**
     * 资源
     *
     * @var
     */
    protected $resource;

    /**
     * 调试开关
     *
     * @var bool
     */
    protected $debug_mode = false;

    /**
     * 订单流水号
     *
     * @var
     */
    protected $order_id;

    /**
     * 手机号
     *
     * @var
     */
    protected $mobile;

    /**
     * 串码奖品
     *
     * @var
     */
    protected $coupon;



    /**
     * Yuanhui constructor.
     *
     * @author Eddie
     *
     * @param $config
     */
    public function __construct($config)
    {
        if (!is_array($config))
            throw new \Exception('请设置好参数并且配置参数必须是数组', 500);

        if (!$config['cid'])
            throw new \Exception('缺少cid参数', 500);

        if (!$config['appkey'])
            throw new \Exception('缺少appkey参数', 500);

        if (!$config['url'])
            throw new \Exception('缺少url参数', 500);


        $this->server = $config['url'];
        $this->appkey = $config['appkey'];
        $this->cid = $config['cid'];
        $this->resource = $config['resource'];
    }


    /**
     * Open debug mode.
     *
     * @author Eddie
     *
     * @return $this
     */
    public function debug()
    {
        $this->debug_mode = true;
        return $this;
    }

    /**
     * Setter - set mobile.
     *
     * @author Eddie
     *
     * @param $mobile
     * @return $this
     */
    public function mobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Setter - set order_id.
     *
     * @param $order_id
     * @return $this
     */
    public function orderId($order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }

    /**
     * Setter - set coupon.
     *
     * @author Eddie
     *
     * @param $coupon
     * @return $this
     */
    public function coupon($coupon)
    {
        $this->coupon = $coupon;
        return $this;
    }


    /**
     * 串码提取
     *
     * @author Eddie
     *
     * @param null $coupon
     * @return array
     * @throws \Exception
     */
    public function extract($coupon = null)
    {
        if (!$this->order_id) {
            throw new \Exception('订单号不能为空', 422);
        }
        if (!$this->mobile) {
            throw new \Exception('手机号不能为空', 422);
        }
        if (!$this->coupon) {
            if (!$coupon) {
                throw new \Exception('奖品不能为空', 422);
            }
            $this->coupon($coupon);
        }

        $params = [
            'cid' => $this->cid,
            'productid' => $this->getProductId(),
            'orderid' => $this->order_id,
            'timestamps' => $this->getMsec(), // 精确到毫秒
            'mob' => $this->mobile
        ];

        /*
         * 签名
         */
        $params['sign'] = $this->signature($params);

        $url = $this->server . self::API_COUPON_CODE_GET;

        $response = $this->request($url, $params, 'POST');

        if ($this->debug_mode) {
            \Log::info('-------------------------------------------------');
            \Log::info('Request API: '.$url);
            \Log::info('Request params: '. print_r($params, true));
            \Log::info('Response: '. print_r(json_decode($response, true), true));
            \Log::info('-------------------------------------------------');
        }

        return $this->transform($response);
    }

    /**
     * Response formater.
     *
     * @author Eddie
     *
     * @param $response
     * @return array $return
     */
    public function transform($response)
    {
        $result = json_decode($response);

        if (!$result) return $result;

        $return = [
            'provider' => 'Yuanhui',
            'success'  => $result->Success,
            'msg'      => $result->Msg,
            'code'     => $result->Code,
        ];
        if ($result->Code == '1001') {
            $return['order_sn'] = $result->OutOrderId;
            $return['coupon_code'] = $result->CodeData;
        }

        return $return;
    }


    /**
     * Return signature string.
     *
     * 签名机制 :
     *     请求参数列表中，除sign外其他必填参数均需要参加验签;
     *     请求列表中的所有必填参数的参数值与APPKEY经过按值的字符串格式从小到大排序(字符串格式排序)后, 直接首尾相接连接为一个字符串,
     *     然后用md5指定的加密方式进行加密。
     *
     *
     * @author Eddie
     *
     * @param $params
     * @return string
     */
    private function signature($params)
    {
        /*
         * 去除 非必选参数
         */
        //unset($params['recallurl']);

        /*
         * Generate signature.
         */
        $signArr = array_values($params);
        $signArr[] = $this->appkey;
        sort($signArr, SORT_STRING);

        return strtoupper(md5(implode($signArr)));
    }

    /**
     * Get micro-seconds.
     *
     * @author Eddie
     *
     * @return bool|string
     */
    private function getMsec()
    {
        list($msec, $sec) = explode(' ', microtime());

        return date('YmdHis' . (sprintf('%03d', $msec*1000)), $sec);
    }

    /**
     * Return productid.
     *
     * @author Eddie
     *
     * @return $productid
     */
    private function getProductId()
    {
        if ($this->coupon) {
            $arr = array_flip($this->resource);
            if (array_key_exists($this->coupon, $arr)) {
                return $arr[$this->coupon];
            }
            else {
                throw new \Exception('没有对应的资源!', 500);
            }
        }
    }

}
