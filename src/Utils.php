<?php

namespace Entrepot\SDK;

class Utils
{
    public static function get($mixed, $path = '', $defaultValue = null)
    {
        $path = explode(".", $path);
        $result = $mixed;

        foreach ($path as $index) {
            if (isset($result[$index])) {
                $result = $result[$index];
            } else {
                $result = $defaultValue;
            }
        }

        return $result;
    }
}
