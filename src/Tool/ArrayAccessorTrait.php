<?php

namespace Bybrand\ShortenerURL\Tool;

/**
 * Provides generic array navigation tools.
 */
trait ArrayAccessorTrait
{
    /**
     * Returns a value by key using dot notation.
     *
     * @param  array      $data
     * @param  string     $key
     * @return mixed
     */
    private function getValueByKey(array $data, $key, $default = null)
    {
        if (!is_string($key) || empty($key) || !count($data)) {
            return $default;
        }

        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                if (!is_array($data) or !array_key_exists($innerKey, $data)) {
                    return $default;
                }
                $data = $data[$innerKey];
            }

            return $data;
        }

        return array_key_exists($key, $data) ? $data[$key] : $default;
    }
}
