<?php

namespace Bybrand\ShortenerURL\Test;

use PHPUnit\Framework\TestCase;

use Bybrand\ShortenerURL\Provider\Bitly;
use Bybrand\ShortenerURL\Shorten;
use Bybrand\ShortenerURL\Exception\ShortenFailed;

// use Base\Subscribers\Subscribers;
// use Base\Subscribers\Adapter\MailChimp as AdapterMailChimp;
use Mockery as m;

/**
 * @group Bitly
 */
class BitlyTest extends TestCase
{
    protected $provider;

    /**
     * @before
     */
    public function setupBefore()
    {
        // Generator fake contents.
        $this->faker = \Faker\Factory::create();

        $this->provider = new Bitly([
            'group'  => $this->faker->domainWord,
            'domain' => $this->faker->domainName,
            'token'  => $this->faker->md5,
        ]);
    }

    /**
     * @after
     */
    public function downAfter()
    {
        m::close();
    }

    /**
     * @group Bitly
     * @group Bitly.destinationNotEmply
     */
    public function testDestinationNotEmplyException()
    {
        $this->expectException(ShortenFailed::class);
        $this->expectExceptionMessage('The long URL not be empty');

        $shorten = new Shorten($this->provider);
        $shorten->destination('');
        $shorten->create();
    }

    /**
     * @group Bitly
     * @group Bitly.shorten
     */
    public function testShorten()
    {
        $jsonResult = [
            'link'     => $this->faker->domainName . '/' . $this->faker->userName,
            'id'       => $this->faker->md5,
            'long_url' => $this->faker->url,
            'title'    => ''
        ];

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($jsonResult));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $HTTPClientMock = m::mock('GuzzleHttp\ClientInterface');
        $HTTPClientMock->shouldReceive('post')->times(1)->andReturn($response);

        // Set custom HTTP client.
        $this->provider->setHttpClient($HTTPClientMock);

        $shorten = new Shorten($this->provider);
        $shorten->destination($this->faker->url);
        $shorten->create();

        // Get all returnet params.
        $result = $shorten->toArray();

        $this->assertArrayHasKey('link', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('long_url', $result);
    }

    /**
     * @group Bitly
     * @group Bitly.methods
     */
    public function testMethods()
    {
        $jsonResult = [
            'link'     => $this->faker->domainName . '/' . $this->faker->userName,
            'id'       => $this->faker->md5,
            'long_url' => $this->faker->url,
            'title'    => ''
        ];

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($jsonResult));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $HTTPClientMock = m::mock('GuzzleHttp\ClientInterface');
        $HTTPClientMock->shouldReceive('post')->times(1)->andReturn($response);

        // Set custom HTTP client.
        $this->provider->setHttpClient($HTTPClientMock);

        $shorten = new Shorten($this->provider);
        $shorten->destination($this->faker->url);
        $shorten->create();

        $this->assertEquals($jsonResult['link'], $shorten->getLink());
        $this->assertEquals($jsonResult['id'], $shorten->getId());
        $this->assertEquals($jsonResult['long_url'], $shorten->getDestination());
    }
}
