<?php

namespace App\Lib;

class ApiErrors
{

    /**
     * @param $code
     * @param $msg
     * @return mixed
     */
    public function setError($code, $msg)
    {
        return $error = [
            'success' => false,
            'error' => [
                'errCode' => $code,
                'errMsg' => $msg
            ]
        ];
    }
}