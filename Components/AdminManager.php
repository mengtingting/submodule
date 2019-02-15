<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use App\Models\Admin;

class AdminManager
{


    /*
     * 获取admin的list
     *
     * By TerryQi
     *
     * 2018-01-06
     */
    public static function getListByStatus($status_arr)
    {
        $admins = Admin::wherein('status', $status_arr)->orderby('id', 'desc')->get();
        return $admins;
    }

    /*
     * 根据id获取管理员
     *
     * By TerryQi
     *
     * 2018-01-06
     */
    public static function getAdminById($id)
    {
        $admin = Admin::find($id);
        return $admin;
    }

    /*
     * 根据手机号获取用户信息
     *
     * By TerryQi
     *
     * 2018-01-07
     */
    public static function getAdminByPhonenum($phonenum)
    {
        $admin = Admin::where('phonenum', '=', $phonenum)->first();
        return $admin;
    }

    /*
     * 管理员登录
     *
     * By TerryQi
     *
     */
    public static function login($phonenum, $password)
    {
        $admin = Admin::where('phonenum', '=', $phonenum)->where('password', '=', $password)->first();
        return $admin;
    }

    /*
    * 搜索管理员信息
         *
         * By TerryQi
         *
         * 2017-12-19
         *
         */
    public static function searchAdmin($search_word)
    {
        $admins = Admin::where('name', 'like', '%' . $search_word . '%')
            ->orwhere('phonenum', 'like', '%' . $search_word . '%')->orderby('id', 'desc')->get();
        return $admins;
    }


    /*
     * 设置管理员信息，用于编辑
     *
     * By TerryQi
     *
     */
    public static function setAdmin($admin, $data)
    {
        if (array_key_exists('name', $data)) {
            $admin->name = array_get($data, 'name');
        }
        if (array_key_exists('avatar', $data)) {
            $admin->avatar = array_get($data, 'avatar');
        }
        if (array_key_exists('phonenum', $data)) {
            $admin->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('password', $data)) {
            $admin->password = array_get($data, 'password');
        }
        if (array_key_exists('role', $data)) {
            $admin->role = array_get($data, 'role');
        }
        return $admin;
    }

    /*
     * 根据条件获取信息
     *
     * By mtt
     *
     * 2018-7-12
     */
    public static function getListByCon($con_arr,$is_paginate){
        $lists = Admin::wherein('status',['0','1']);
        //相关搜索条件
        if (array_key_exists('role', $con_arr) && !Utils::isObjNull($con_arr['role'])) {
            $lists = $lists->where('role', $con_arr['role']);
        }
        if (array_key_exists('phonenum', $con_arr) && !Utils::isObjNull($con_arr['phonenum'])) {
            $lists = $lists->where('phonenum', $con_arr['phonenum']);
        }
        $lists = $lists->orderby('id', 'asc');
        if ($is_paginate) {
            $lists = $lists->paginate(Utils::PAGE_SIZE);
        } else {
            $lists = $lists->get();
        }
        return $lists;
    }

}