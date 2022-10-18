<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PayController extends Controller
{

    public function pay()
    {
        $orderNum = time();
        $price = 1;
        $ip = '61.50.125.102';
//        $this->ccbH5($orderNum, $price, $ip);
        $this->ccbMini($orderNum, $price, $ip);

    }

    protected function ccbMini($orderNum, $price, $ip)
    {

        $siteUrl = 'https://ibsbjstar.ccb.com.cn/CCBIS/ccbMain?CCB_IBSVersion=V6&';
        $pk = '30819d300d06092a864886f70d010101050003818b0030818702818100a6901a2b630c2dd9f5ac1688dbb68c963771942e1e6c814dc41b2f947afb6ef0326f948a3cc65614c4b8833c780790b64539cad06fc88cb5d5ba222bcfc0173da777f8abc18a9e3a577c5c2edfbf4723a1c21627fcf9c916e09fcbf5b6b08457ad70e16fdb6e073610879ef5b8c2e6e88c61a4bae800713391a453a753024311020113';

        $md5Params = [
            'MERCHANTID' => '105000059623758',
            'POSID' => '056362203',
            'BRANCHID' => '212000000',
            'ORDERID' => $orderNum,
            'PAYMENT' => $price,
            'CURCODE' => '01',
            'TXCODE' => '530590',//HT0000 530590
            'REMARK1' => '',
            'REMARK2' => '',
            'TYPE' => 1,
            'PUB' => substr($pk, -30),
            'GATEWAY' => 0,
            'CLIENTIP' => $ip,
            'REGINFO' => '',
            'PROINFO' => '',
            'REFERER' => '',
            'TRADE_TYPE' => 'MINIPRO',
            'SUB_APPID' => 'wx4f5015c4e329b728',
            'SUB_OPENID' => 'oD-e65fPyzPeoP9yzbeCeGPs5LEA',
        ];
        $md5Query = http_build_query($md5Params);
        $MAC = md5($md5Query);
        $urlParams = array_merge($md5Params, ['MAC' => $MAC]);

        $url = $siteUrl . http_build_query($urlParams);

        $response = Http::post($url);
        $data = $response->json();
        dump($data);
        if ($data['PAYURL']) {
            $response = Http::post($data['PAYURL']);
            dump($response->json());
        }
    }

    protected function ccbH5($orderNum, $price, $ip)
    {

        $siteUrl = 'https://ch5.dcep.ccb.com/CCBIS/ccbMain_XM?CCB_IBSVersion=V6&';
        $pk = '30819d300d06092a864886f70d010101050003818b0030818702818100a6901a2b630c2dd9f5ac1688dbb68c963771942e1e6c814dc41b2f947afb6ef0326f948a3cc65614c4b8833c780790b64539cad06fc88cb5d5ba222bcfc0173da777f8abc18a9e3a577c5c2edfbf4723a1c21627fcf9c916e09fcbf5b6b08457ad70e16fdb6e073610879ef5b8c2e6e88c61a4bae800713391a453a753024311020113';

        $md5Params = [
            'MERCHANTID' => '105000059623758',
            'POSID' => '056362203',
            'BRANCHID' => '212000000',
            'ORDERID' => $orderNum,
            'PAYMENT' => $price,
            'CURCODE' => '01',
            'TXCODE' => 'HT0000',//HT0000 530590
            'REMARK1' => '',
            'REMARK2' => '',
            'RETURNTYPE' => 1,
            'TIMEOUT' => date('YmdHis', time() + 1000000),
            'PUB' => substr($pk, -30),
        ];
        $md5Query = http_build_query($md5Params);
        $MAC = md5($md5Query);
        $urlParams = array_merge($md5Params, ['MAC' => $MAC, 'TX_FLAG' => 3]);

        $url = $siteUrl . http_build_query($urlParams);
        dump($url);

        $response = Http::post($url);
        dump($response->body());
    }

}
