<?php

namespace Bybrand\ShortenerURL\Provider;

use Bybrand\ShortenerURL\ShortenInterface;
use Bybrand\ShortenerURL\Tool\GuardedPropertyTrait;
use Bybrand\ShortenerURL\Tool\ArrayAccessorTrait;
use Bybrand\ShortenerURL\Exception;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class Bitly implements ShortenInterface
{
    use GuardedPropertyTrait;
    use ArrayAccessorTrait;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    private $longUrl;
    private $shortUrl;
    private $response = [];

    /**
     * The target URL endpoint to short link.
     * @var string
     */
    protected $targetEndpoint = 'https://api-ssl.bitly.com/v4/shorten';

    /**
     * The base domain to url shortned.
     * @var string
     */
    protected $domain = 'bit.ly';

    /**
     * A GUID for a Bitly group, get in the OAuth2 request.
     * @var string
     */
    protected $group;

    /**
     * The Bearer Token specification, get in the OAuth2 request.
     * @var string
     */
    protected $token;

    public function __construct(array $options = [])
    {
        // We'll let the GuardedPropertyTrait handle mass assignment of incoming
        // options.
        $this->fillProperties($options);

        $this->setHttpClient(new HttpClient);
    }

    /**
     * Sets the HTTP client instance.
     *
     * @param  HttpClientInterface $client
     * @return self
     */
    public function setHttpClient(HttpClientInterface $client)
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Returns the HTTP client instance.
     *
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function destination($url)
    {
        $this->longUrl = $url;
    }

    public function post()
    {
        if (!$this->longUrl) {
            throw new Exception\ShortenFailed('The long URL not be empty.');
        }

        try {
            // The Guzzle Client.
            $response = $this->httpClient->post($this->targetEndpoint, [
                'json' => [
                    'group_guid' => $this->group,
                    'domain'     => $this->domain,
                    'long_url'   => $this->longUrl
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept'        => 'application/json',
                ]
            ]);

            // Return success.
            $body = (string) $response->getBody();
            // Save all params returned in array.
            $this->response = json_decode($body, true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                throw new Exception\ShortenFailed(Psr7\str($e->getResponse()));
            }
            throw new Exception\ShortenFailed('The URL destination does not shortened.');
        }
    }

    public function getLink()
    {
        return $this->getValueByKey($this->response, 'link');
    }

    public function getId()
    {
        return $this->getValueByKey($this->response, 'id');
    }

    public function getDestination()
    {
        return $this->getValueByKey($this->response, 'long_url');
    }

    /**
     * Return all of the params details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
