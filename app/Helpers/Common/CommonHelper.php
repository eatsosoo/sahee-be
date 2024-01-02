<?php

namespace App\Helpers\Common;

use Carbon\Carbon;

class CommonHelper
{
    /**
     * handle search string when has character: "%", "_", ...
     *
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * handle search string when has character: "%", "_", ...
     *
     * @param string $str
     * @return string
     */
    public static function escapeLikeQueryParameter(string $str): string
    {
        $str = str_replace('%', '\%', $str);
        $str = str_replace('_', '\_', $str);
        $str = str_replace('--', '\--', $str);
        $str = str_replace("'", "\'", $str);
        return str_replace('"', '\"', $str);
    }
}
