<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class RuleController extends BaseController
{

    protected function uuid($prefix = '', $len=16, $salt = 'zoomov') {

        $hex = md5($salt. uniqid(mt_rand(), true));

        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);

        $uid = $prefix.preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);

        $len = max(8, min(128, $len));

        while (strlen($uid) < $len)
            $uid .= $this->uuid(22);

        return substr($uid, 0, $len);
    }

    protected function uuid2($prefix='', $salt) {
        return $prefix.md5($salt.'twaoos');
    }
}
