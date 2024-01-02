<?php

namespace App\Helpers;

use Throwable;

class ExceptionHelper
{
    /**
     * Convert exception to readable message for DEBUG only
     * @param Throwable|null $e
     * @param int $index
     * @param int $limit
     * @return string
     */
    public static function makePrettyException(?Throwable $e, int $index=0, int $limit=5): string
    {
        if ($index >= $limit || is_null($e))
            return "";

        $trace = $e->getTrace();
        $codeLine = empty($trace[0]['line'])? '' : $trace[0]['line'];

        $result = '#'.$index.': Line ';
        $result = empty($codeLine)? $result.'-' : $result.$codeLine.'-';
        $result = $index >0? "\n".$result : $result;
        $result .= ' ['.$e->getCode().'] ';
        if(!empty($trace[0]['class'])) {
            $result .= $trace[0]['class'];
            $result .= '->';
        }
        $result .= $trace[0]['function'].'()';
        $result .= ': '.$e->getMessage();

        return $result.ExceptionHelper::makePrettyException($e->getPrevious(), $index+1, $limit);
    }
}
