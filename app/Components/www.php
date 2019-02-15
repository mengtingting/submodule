<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/15
 * Time: 14:08
 */

namespace App\Components;


class www
{
    /*
        * 将2017-11-27 00:00:00转换为2017年11月27日
        *
        * By TerryQi
        *
        * 2017-12-04
        *
        */
    public static function getYMDChi($date_str)
    {
        $date_arr = explode(' ', $date_str);
        $date_obj_arr = explode('-', $date_arr[0]);
        return $date_obj_arr[0] . "年" . $date_obj_arr[1] . "月" . $date_obj_arr[2] . "日";
    }
}