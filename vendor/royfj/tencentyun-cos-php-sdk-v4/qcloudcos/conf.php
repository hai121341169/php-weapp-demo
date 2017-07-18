<?php

namespace qcloudcos;

class Conf {
    // Cos php sdk version number.
    const VERSION = 'v4.2.3';
    const API_COSAPI_END_POINT = 'http://region.file.myqcloud.com/files/v2/';

    // Please refer to http://console.qcloud.com/cos to fetch your app_id, secret_id and secret_key.
    const APP_ID = '1253710425';
    const SECRET_ID = 'AKIDvkNo24pkT0OirquCaoz41kNo37FYqHMF';
    const SECRET_KEY = 'oRr57wYyFJDvQjrqf8Xr39GIEy1xkaRL';

    /**
     * Get the User-Agent string to send to COS server.
     */
    public static function getUserAgent() {
        return 'cos-php-sdk-' . self::VERSION;
    }
}
