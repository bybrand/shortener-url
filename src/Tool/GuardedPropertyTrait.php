<?php

namespace Bybrand\ShortenerURL\Tool;

trait GuardedPropertyTrait
{
    /**
     * Mass assign the given options to explicitly defined properties.
     *
     * @param array $options
     * @return void
     */
    protected function fillProperties(array $options = [])
    {
        foreach ($options as $option => $value) {
            if (property_exists($this, $option)) {
                $this->{$option} = $value;
            }
        }
    }
}
