<?php

namespace Bybrand\ShortenerURL;

use Bybrand\ShortenerURL\ShortenInterface;

class Shorten
{
    protected $provider;

    public function __construct(ShortenInterface $provider)
    {
        $this->provider = $provider;
    }

    public function destination($string)
    {
        $this->provider->destination($string);
        return $this;
    }

    /**
     * @return void
     * @return object Exception\ShortenFailed
     */
    public function create()
    {
        $this->provider->post();
        return $this;
    }

    public function getLink()
    {
        return $this->provider->getLink();
    }

    public function getDestination()
    {
        return $this->provider->getDestination();
    }

    public function getId()
    {
        return $this->provider->getId();
    }

    public function toArray()
    {
        return $this->provider->toArray();
    }
}
