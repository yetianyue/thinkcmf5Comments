<?php
/**
 * Author: yetianyue
 * CreateTime: 2017/8/25 8:58
 * Description: write something
 */
if (!function_exists('FormartTime')){
    function FormartTime($t){
        $n = time();
        if($n < $t)
            return '刚刚';

        $s = $n-$t;
        if($s < 60)
            return $s."秒钟前";

        $m = floor($s/60);
        if($m < 60)
            return $m."分钟前";

        $h = floor($m/60);
        if($h < 24)
            return $h."小时前";

        $d = floor($h/24);
        if($d < 7)
            return $d."天前";

        $w = floor($d/7);
        if($w < 4)
            return $w.'周前';

        $m = floor($w/4);
        if($m < 12)
            return $m.'个月前';

        $y = floor($m/12);
        if($y < 5)
            return $y.'年前';
        return date('Y-m-d',$t);
    }
}