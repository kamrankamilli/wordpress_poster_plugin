<?php

/**
* @package Poster
*/

class VKposterDeactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}