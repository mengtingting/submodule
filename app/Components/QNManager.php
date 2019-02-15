<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class QNManager
{
    const ACCESS_KEY='YhJdLFOJyWUjBKf19RQS0k_f1L7FwSLRIbsLTIbC';
    const SECRET_KEY='aO2XI4UPpk3FjBMBk6NB_y_mVMgKYkqz9rcaXzVz';
    const BUCKET='knightcomment';
    const URL='http://img.lvluozhibao.com/';

    /*
     * 获取七牛upload token
     *
     * By TerryQi
     *
     */
    public static function uploadToken()
    {
        $accessKey = self::ACCESS_KEY;
        $secretKey = self::SECRET_KEY;
        $auth = new Auth($accessKey, $secretKey);
        $bucket = self::BUCKET;
        $upToken = $auth->uploadToken($bucket);
        return $upToken;
    }


}