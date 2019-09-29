<?php

namespace Chenlin\JWT;

/** 水平复用trait @author:chenlin @date:2019/8/19 */

trait SingleTon
{
    private static $instance;

    public static function getInstance(...$args)
    {
        if (empty(self::$instance)) {
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }
}