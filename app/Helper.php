<?php

namespace app;

class Helper {

    // generate random string
    public static function getRandString($length = 7) {
        $ch = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $ch_len = strlen($ch);
        $out = '';
        for ($i = 0; $i < $length; $i += 1) {
            $out .= $ch[mt_rand(0, $ch_len - 1)];
        }
        return $out;
    }

    // generate a fixed length uid hash based on input data
    public static function getHashString($input, $length = 7) {
        $hash = base64_encode(sha1($input, true));
        $hash = str_replace("+", "", $hash);
        $hash = str_replace("/", "", $hash);
        $hash = str_replace("=", "", $hash);
        $hash = substr($hash, 0, $length);
        return $hash;
    }

    // match an ip address, cisco-style.

    // matches:
    // xxx.xxx.xxx.xxx        (exact)
    // xxx.xxx.xxx.[yyy-zzz]  (range)
    // xxx.xxx.xxx.xxx/nn     (nn = # bits, cisco style -- i.e. /24 = class C)

    // does not match:
    // xxx.xxx.xxx.xx[yyy-zzz]  (range, partial octets not supported)
    public static function matchIpAddr($range, $ip) {
        $result = true;
        if (preg_match(
            "`^(\d{1,3}) \. (\d{1,3}) \. (\d{1,3}) \. (\d{1,3})/(\d{1,2})$`x",
            $range, $regs
        )) {
            // perform a mask match
            $ipl = ip2long($ip);
            $rangel = ip2long($regs[1] . "." . $regs[2] . "." . $regs[3] . "." . $regs[4]);
            $maskl = 0;
            for ($i = 0; $i < 31; $i += 1) {
                if ($i < $regs[5]-1) {
                    $maskl = $maskl + pow(2,(30-$i));
                }
            }
            if (($maskl & $rangel) == ($maskl & $ipl)) $result = true;
            else $result = false;
        } else {
            // range based
            $maskocts = explode(".",$range);
            $ipocts = explode(".",$ip);
            // perform a range match
            for ($i = 0; $i < 4; $i += 1) {
                if (preg_match("`^\[(\d{1,3}) \- (\d{1,3})\]$`x", $maskocts[$i], $regs)) {
                    if (($ipocts[$i] > $regs[2]) || ($ipocts[$i] < $regs[1])) {
                        $result = false;
                    }
                } else {
                    if ($maskocts[$i] != $ipocts[$i]) {
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }

}
