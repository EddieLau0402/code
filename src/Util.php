<?php

namespace Eddie\CouponCode;


trait Util
{
    /**
     * 发送curl请求
     *
     * @author Eddie
     *
     * @param $url
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return mixed
     */
    public function request($url, $params = [], $method = 'GET', $headers = [])
    {
        /*
         * Open connection, and set options.
         */
        $ch = curl_init();
        if (strtoupper($method) == 'GET') { // >>>>> GET request.
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if ($params) { // has parameters.
                $url .= (strpos($url, '?') ? '&' : '?') . http_build_query($params);
            }
        }
        else { // >>>>> POST request.
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        /*
         * Execute.
         */
        $result = curl_exec($ch);

        /*
         * Close connection.
         */
        curl_close($ch);

        /*
         * Return.
         */
        return $result;
    }
}