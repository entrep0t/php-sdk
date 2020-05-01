<?php

namespace Entrepot\SDK;

class Utils
{
    /**
     * See lodash.get
     * @param mixed $mixed The array/object you want to get something from
     * @param string $path Property or nested property you want to retrieve from $mixed
     * @param mixed $defaultValue (optional) The default value to return in case $path is not found
     * @return mixed Returns the value at $path in $mixed, or $defaultValue
     *
     * @example
     * <code>
     * $arr = ['prop' => ['nestedProp' => 1]];
     * Utils::get($arr, 'prop.nestedProp');
     * </code>
     */
    public static function get($mixed, $path = '', $defaultValue = null)
    {
        $path = explode('.', $path);
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
