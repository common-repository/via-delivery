<?php
namespace Ipol\Woo\ViaDelivery;

class Calculator
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var Ipol\Woo\ViaDelivery\Api\Client
     */
    protected $client;

    /**
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * @param Ipol\Woo\ViaDelivery\Shipment $shipment
     * @return array
     */
    public function preview(Shipment $shipment)
    {
        $tariff = $this->getClient()->getService('point-list')->getPreview($shipment);

        return array_merge([
            'count'              => 0,
            'min_delivery_price' => null,
            'max_delivery_price' => null,
            'min_days'           => null,
            'max_daya'           => null,
        ], (array) $tariff);
    }

    /**
     * @param Ipol\Woo\ViaDelivery\Shipment $shipment
     * @return array
     */
    public function calculate(Shipment $shipment, $pointId)
    {
        $ret = $this->getClient()->getService('point-list')->getById($pointId, $shipment);

        return $ret;
    }

    /**
     * @param Shipment $shipment
     * @return string
     */
    public function getMapUrl(Shipment $shipment, array $params = [])
    {
        return $this->getClient()->getService('point-list')->getMapUrl($shipment, $params);
    }

    /**
     * @return Ipol\Woo\ViaDelivery\Api\Client
     */
    protected function getClient()
    {
        return $this->client = $this->client ?: new Api\Client($this->settings);
    }
}