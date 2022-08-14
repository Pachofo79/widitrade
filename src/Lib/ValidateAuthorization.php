<?php

namespace App\Lib;

class ValidateAuthorization
{

    /**
     * @param $token
     * @return bool
     */
    public function validateAuthorization($token)
    {
        $token = str_split($token);
        $stack = array();
        foreach ($token as $value) {

            switch ($value) {
                case '(':
                    array_push($stack, 0);
                    break;
                case ')':
                    if (array_pop($stack) !== 0)
                        return false;
                    break;
                case '[':
                    array_push($stack, 1);
                    break;
                case ']':
                    if (array_pop($stack) !== 1)
                        return false;
                    break;
                case '{':
                    array_push($stack, 2);
                    break;
                case '}':
                    if (array_pop($stack) !== 2)
                        return false;
                    break;
                default:
                    return false;
            }
        }
        return (empty($stack));
    }
}