<?php
/**
 * File_Name:ApiResponse.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 14:37
 */

namespace App\Http\Controllers;

use App\Components\AES;
use App\Components\Utils;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    //未知错误
    const UNKNOW_ERROR = 999;
    //缺少参数
    const MISSING_PARAM = 901;
    //逻辑错误
    const INNER_ERROR = 902;
    //操作失败
    const FAIL_CODE = 903;
    //没有找到数据
    const NO_DATA = 904;
    //参数错误
    const FAIL_PARAM = 905;
    //成功
    const SUCCESS_CODE = 200;
    //格式错误
    const FORMATTING_ERROR = 99;
    //缺少秘钥
    const SECRET_LOST = 100;
    //用户编码丢失
    const USER_ID_LOST = 103;
    //注册失败
    const REGISTER_FAILED = 104;
    //未找到用户
    const NO_USER = 105;

    //映射错误信息
    public static $errorMassage = [
        //结束
        self::SUCCESS_CODE => array('zh' => '操作成功', 'en' => 'success'),
        self::UNKNOW_ERROR => array('zh' => '未知错误', 'en' => 'unknow error'),
        self::FAIL_CODE => array('zh' => '操作失败', 'en' => 'fail'),
        self::NO_DATA => array('zh' => '没有找到数据', 'en' => 'unfound data'),
        self::FAIL_PARAM => array('zh' => '参数错误', 'en' => 'param incorrect'),
        self::MISSING_PARAM => array('zh' => '缺少参数', 'en' => 'missing param'),
        self::FORMATTING_ERROR => array('zh' => '格式错误', 'en' => 'format incorrect'),
        self::SECRET_LOST => array('zh' => '缺少秘钥', 'en' => 'missing key'),
        self::USER_ID_LOST => array('zh' => '缺少用户编码', 'en' => 'missing user code'),
        self::NO_USER => array('zh' => '未找到用户', 'en' => 'unfound user'),
        self::REGISTER_FAILED => array('zh' => '注册失败', 'en' => 'register failed'),
        self::INNER_ERROR => array('zh' => '内部错误', 'en' => 'inner error'),
    ];

    //格式化返回
    public static function makeResponse($result, $ret, $code, $header = false,$language = 'en',  $mapping_function = null, $params = [])
    {
        if (Utils::isObjNull($language)) {
            $language = env('APP_DEFAULT_LANGUAGE');
        }
        $rsp = [];
        $rsp['code'] = $code;

        if ($result === true) {
            $rsp['result'] = true;
            $rsp['message'] = self::$errorMassage[$code][$language];
            if ($header) {
                $rsp['headers'] = $header;
            }
            if ($ret) {
                $rsp['ret'] = $ret;
//                $rsp['ret'] = AES::encryptData($ret);
            } else {
                $rsp['ret'] = [];
            }
        } else {
            $rsp['result'] = false;
            if ($ret) {
                $rsp['message'] = $ret;
            } else {
                if (array_key_exists($code, self::$errorMassage)) {
                    $rsp['message'] = self::$errorMassage[$code][$language];
                } else {
                    $rsp['message'] = 'undefind error code';
                }
            }
        }
        Utils::backLog(__METHOD__, $rsp);
        return response()->json($rsp);
    }
}