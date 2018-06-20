<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Http\Request;
use Omnipay;

class AlipayController extends Controller
{
    public function pay(Request $request){

        $gateway = Omnipay::gateway();

        $options = [
            'out_trade_no' => date('YmdHis') . mt_rand(1000,9999),
            'subject' => $request['film_entry_id'],
            'total_fee' => $request['fee'],
        ];

        $response = $gateway->purchase($options)->send();
        $response->redirect();
    }

    public function result(){

        $gateway = Omnipay::gateway();

        $options = [
            'request_params'=> $_REQUEST,
        ];

        $response = $gateway->completePurchase($options)->send();

        if ($response->isSuccessful() && $response->isTradeStatusOk()) {
            //支付成功后操作
            exit('支付成功');
        } else {
            //支付失败通知.
            exit('支付失败');
        }

    }
}
