<?php

/**
* @package Poster
*/

class VKposterActivate
{
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
