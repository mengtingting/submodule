<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $admins = AdminManager::getListByStatus(['0', '1']);
        return view('admin.admin.index', ['admin' => $admin, 'datas' => $admins]);
    }


    //删除管理员
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数管理员id$id']);
        }
        $admin = AdminManager::getAdminById($id);
        //如果不存在管理员，则返回成功
        if (!$admin) {
            return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
        }
        //非根管理员
        if ($admin->role == '0') {
            $admin->delete();
            return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, "不允许删除根管理员", ApiResponse::SUCCESS_CODE);
        }
    }

    //设置管理员状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数管理员id$id']);
        }
        $admin = AdminManager::getAdminById($id);
        $admin->status = $data['status'];
        $admin->save();
        return ApiResponse::makeResponse(true, $admin, ApiResponse::SUCCESS_CODE);
    }


    //新建或编辑管理员-get
    public function edit(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin_b = new Admin();
        if (array_key_exists('id', $data)) {
            $admin_b = AdminManager::getAdminById($data['id']);
        }
        $admin = $request->session()->get('admin');
        //只有根管理员有修改权限
        if (!($admin->role == '1')) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，只有根级管理员有修改权限']);
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();

//        dd($admin_b);
        return view('admin.admin.edit', ['admin' => $admin, 'data' => $admin_b, 'upload_token' => $upload_token]);
    }


    //搜索管理员
    public function search(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
//        dd($data);
        $search_word = $data['search_word'];
        if (!array_key_exists('name', $data)) {
            $data['name'] = '';
        }
        $admins = AdminManager::searchAdmin($search_word);
        return view('admin.admin.index', ['admin' => $admin, 'datas' => $admins]);
    }


    //新建或编辑管理员->post
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = new Admin();
//        dd($data);
        //存在id是保存
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $admin = AdminManager::getAdminById($data['id']);
            //保存查看手机号是否重复
            if (array_key_exists('phonenum', $data) && !Utils::isObjNull($data['phonenum'])) {
                $e_admin = AdminManager::getAdminByPhonenum($data['phonenum']);
                if ($e_admin->id != $data['id']) {
                    return ApiResponse::makeResponse(false, "手机号重复", ApiResponse::PHONENUM_DUP);
                }
            }
        } else {
            //新建进行校验，手机号是否重复
            if (array_key_exists('phonenum', $data) && !Utils::isObjNull($data['phonenum'])) {
                $e_admin = AdminManager::getAdminByPhonenum($data['phonenum']);
                if ($e_admin) {
                    return ApiResponse::makeResponse(false, "手机号重复", ApiResponse::PHONENUM_DUP);
                }
            }
        }
        $admin = AdminManager::setAdmin($admin, $data);
        //如果不存在id代表新建，则默认设置密码
        if (!array_key_exists('id', $data) || Utils::isObjNull($data['id'])) {
            $admin->password = 'afdd0b4ad2ec172c586e2150770fbf9e';  //该password为Aa123456的码
        }
        $admin->save();
        return ApiResponse::makeResponse(true, $admin, ApiResponse::SUCCESS_CODE);
    }

    //修改个人资料get
    public function editMySelf(Request $request){
        $admin = $request->session()->get('admin');
        $admins = AdminManager::getAdminById($admin['id']);
        $param=array(
            'data'=>$admins
        );
        return view('admin.admin.editMySelf', $param);
    }

    //修改个人资料post
    public function editMySelfPost(Request $request){
        $data = $request->all();
        $return = null;
        if(empty($data['password'])){
            $admin = AdminManager::getAdminById($data['id']);
            unset($data['password']);
            unset($data['new_password']);
            unset($data['confirm_password']);
            //判断电话号码是否唯一
            if($data['phonenum'] != $admin['phonenum']){
                $result = AdminManager::getAdminByPhonenum($data['phonenum']);
                if($result){
                    $return['result'] = false;
                    $return['msg'] = '个人信息修改失败,此电话号码已被注册';
                    return $return;
                }
            }
            //设置管理员信息
            $admin = AdminManager::setAdmin($admin,$data);
            $result = $admin->save();
            if($result){
                $admins['id'] = $admin['id'];
                $admins['name']= $admin['name'];
                $admins['phonenum'] = $admin['phonenum'];
                $admins['avatar'] = $admin['avatar'];
                $admins['status'] = $admin['status'];
                $admins['role'] = $admin['role'];
                $admins['admin'] = $admin['admin'];
                //存入session
                $request->session()->put('admin',$admins);
                $return['result'] = true;
                $return['msg'] = '个人信息修改成功';
            }else{
                $return['result'] = false;
                $return['msg'] = '个人信息修改失败';
            }
        }else{
            $admin = AdminManager::getAdminById($data['id']);
            unset($data['name']);
            unset($data['phonenum']);
            if($data['password'] != $admin['password']){
                $return['result'] = false;
                $return['msg'] = '修改密码失败,原密码输入不正确';
            }else{
                $data['password'] = $data['new_password'];
                unset($data['new_password']);
                unset($data['confirm_password']);
                $admin = AdminManager::setAdmin($admin,$data);
                $result = $admin -> save();
                if($result){
                    $return['result'] = true;
                    $return['msg'] = '修改密码成功,请重新登陆';
                }else{
                    $return['result'] = false;
                    $return['msg'] = '修改密码失败';
                }
            }
        }
        return $return;
    }







}

















