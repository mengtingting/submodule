<?php

namespace App\Components;
class AES
{
//    var $key = 'zO1BlKyViUmJkTIsvIHJXRr1FluWKmTZ';
    var $key = '5zoPHK0nhu5o6UeK';
    const iv = 'FQFBCcQh59HNFr2M';

    public static function pkcs7_pad($str)
    {
        $len = mb_strlen($str, '8bit');
        $c = 16 - ($len % 16);
        $str .= str_repeat(chr($c), $c);
        return $str;
    }

    //    PHP7.1以上使用   加密
    public static function encryptData($data,$key)
    {
        $str_padded = AES::pkcs7_pad($data);
//        echo $str_padded . "\n";
        $encrypted = openssl_encrypt($str_padded, 'aes-128-cbc', $key, OPENSSL_NO_PADDING, self::iv);
//        dd($encrypted);
        $encrypted =base64_encode($encrypted);
        return  str_replace("+", "%2B",$encrypted);
    }

    //    PHP7.1以上使用    解密
    public static function decryptData($data,$key)
    {
//        dd($data);
        $charlist = " \t\n\r\0\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F";
        $decrypted = openssl_decrypt(base64_decode($data), 'aes-128-cbc', $key, OPENSSL_NO_PADDING, self::iv);

        $decrypted = rtrim( $decrypted,$charlist);
        return $decrypted;
//        $padding = ord($decrypted[strlen($decrypted) - 1]);
//        return substr($decrypted, 0, -$padding);
    }

    //    PHP5 以上使用  加密
    public static function encryptToken($data,$key)
    {
        $padding = 16 - (strlen($data) % 16);
        $data .= str_repeat(chr($padding), $padding);
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, self::iv);
    }

    //    PHP5 以上使用  解密
    public static function decryptToken($data,$key)
    {
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($data), MCRYPT_MODE_CBC, self::iv);
        $padding = ord($data[strlen($data) - 1]);
        return substr($data, 0, -$padding);
    }
}

//if (php_sapi_name() === 'cli') {
//    $m_data = '{"code":200,"result":true,"message":"success","ret":{"id":7,"start_time":"2018-10-10 10:00:00","end_time":"2018-10-18 00:00:00","end_kntt_time":"2018-10-15 00:00:00","status":2,"title":"Million Challenge","content":"<p>Million Challenge test</p>","num":1,"token":"MICH","amount":"1000000.00000000","participants_num":249,"first_gradient":3,"second_gradient":3,"third_gradient":4,"count":10,"language":"en","created_at":"2018-10-10 10:36:30","updated_at":"2018-10-18 00:00:03","deleted_at":null}}';
////    $m_data = '{"app":"0123456789ABCDEF"}';
//
//    $aes = new AES();
//    // PHP7
//    $me_data = $aes->encryptData($m_data);
//    echo ('PHP openssl_encrypt: ' . $me_data) . "\n";
//    $med_data = $aes->decryptData($me_data);
//    echo ('PHP openssl_decrypt: ' . $med_data) . "\n";
//
//    //PHP5
//    $en_data = base64_encode($aes->encryptToken($m_data));
//    echo ('PHP encrypt: ' . $en_data) . "\n";
//    echo ('PHP decrypt: ' . $aes->decryptToken($me_data)) . "\n";
//
//
//}