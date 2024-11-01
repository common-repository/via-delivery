<?php
namespace Ipol\Woo\ViaDelivery\Api\Service;

use Ipol\Woo\ViaDelivery\Api\Client;
use Ipol\Woo\ViaDelivery\Order as OrderModel;

class GeoCode implements ServiceInterface
{
    /**
     * @var Ipol\Woo\ViaDelivery\Api\Provider\Address
     */
    protected $provider;

    /**
     * @param Client
     */
    public function __construct(Client $client)
    {
        $this->provider = $client->getProvider('map');
    }

    /**
     * @param string $country
     * @param string $region
     * @param string $city
     * @param string $lang
     * 
     * @return array
     */
    public function identify($country, $region, $city, $lang = 'ru')
    {
        $ret = $this->provider->execMethod('geo-decode', array_filter([
            'address' => implode(',', [$city, $region, $country]),
            'lang'    => $lang
        ]));

        return $ret;
    }
}