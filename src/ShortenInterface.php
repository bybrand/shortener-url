<?php

namespace Bybrand\ShortenerURL;

interface ShortenInterface
{
    /**
     * Set a destination link to short.
     *
     * @param  string
     * @return void
     */
    public function destination($string);

    /**
     * Set a custom branded link.
     *
     * @param  string
     * @return void
     */
    public function brandedLink($string);

    /**
     * Send a link to shorten via the provider.
     *
     * @return array
     */
    public function post();

    /**
     * Return the shorten link.
     *
     * @return string
     */
    public function getlink();

    public function getDestination();
    public function getId();
    public function toArray();
}
