<?php

namespace Zoomov\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $planner = 20;
    protected $writer = 9;

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
