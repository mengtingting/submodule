<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/7
 * Time: 14:33
 */

namespace App\Http\Middleware;

use App\Components\AES;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use Closure;

class DecryptData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //判断是否要加密
        $request_status = env('APP_ENCRYPTED_STATUS');
        if ($request_status) {
            $parameter = $request->all();
//            dd($request);
            $header = Utils::getHeaders($request);
            if (!array_key_exists('clientSession', $header)) {
                return ApiResponse::makeResponse(false, false, ApiResponse::SECRET_LOST);
            } else if (!array_key_exists('body', $parameter)) {
                return ApiResponse::makeResponse(false, false, ApiResponse::FORMATTING_ERROR);
            } else {
//                $parameter['body']=base64_decode($parameter['body']);
                $key = $header['clientSession'];
//                dd($parameter['body']);
                $data = AES::decryptData($parameter['body'], $key);
                $data = json_decode($data,true);
//                dd($data);
            }
            if (array_key_exists('page', $data)) {
                $request->offsetSet('page', $data['page']);
            }
            $request->offsetSet('data', $data);
        }
        return $next($request);
    }
}