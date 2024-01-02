<?php

namespace App\Helpers\Common;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MetaInfo
{
    public string $name;

    public Carbon $time;

    public string $signature;

    public function __construct()
    {
        $this->name = '';
        $this->time = Carbon::now();
        $this->signature = '';
    }

    public static function doneBy(string $name, string $signature = ''): MetaInfo
    {
        $ret = new MetaInfo();
        $ret->name = $name;
        $ret->signature = $signature;
        return $ret;
    }

    public static function parseWebRequest(string $signature): MetaInfo
    {
        $user = Auth::user();
        $author = $user ? $user->getAuthIdentifierName() : 'web';
        return MetaInfo::doneBy($author, $signature);
    }

    public function refreshTime(): void
    {
        $this->time = Carbon::now();
    }
}
