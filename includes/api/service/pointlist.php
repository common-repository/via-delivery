<?php
namespace Ipol\Woo\ViaDelivery\Api\Service;

use Ipol\Woo\ViaDelivery\Api\Client;
use Ipol\Woo\ViaDelivery\Api\Provider\Map;
use Ipol\Woo\ViaDelivery\Shipment;

class PointList implements ServiceInterface
{
    /**
     * @var Ipol\Woo\ViaDelivery\Api\Provider\Map
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
     * @param array $params
     * @return array
     */
    public function getPreview(Shipment $shipment, array $params = [])
    {
        $params = array_merge($this->prepare($shipment), $params);

        $ret = $this->provider->execMethod('point-list/preview', $params);

        file_put_contents(__DIR__ .'/getPreview.log', print_r($params, true) . print_r($ret, true) . PHP_EOL . '------' . PHP_EOL, FILE_APPEND);

        return $ret;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getList(Shipment $shipment, array $params = [])
    {
        $params = array_merge($this->prepare($shipment), $params);

        return $this->provider->execMethod('point-list', $params);
    }

    public function getById($pointId, Shipment $shipment = null)
    {
        $params = ['point_id' => $pointId];

        if ($shipment) {
            $params = array_merge($params, $this->prepare($shipment));
        }

        $ret = $this->provider->execMethod('point-list', $params);

        file_put_contents(__DIR__ .'/getById.log', print_r($params, true) . print_r($ret, true) . PHP_EOL . '------' . PHP_EOL, FILE_APPEND);

        return $ret;

    }

    /**
     * @param array $params
     * @return string
     */
    public function getMapUrl(Shipment $shipment, array $params = [])
    {
        $parms = $this->prepare($shipment);
        
        $ret = $this->provider->getMapUrl($parms = array_merge([
            'address'     => $parms['city'] .', '. $parms['region'] .', '. $parms['country'],
            'orderCost'   => $parms['order_price'],
            'orderWeight' => $parms['weight'],
            'orderWidth'  => $parms['width'],
            'orderHeight' => $parms['height'],
            'orderLength' => $parms['length'],
            'currency'    => $parms['currency'],
            'lang'        => $parms['lang'],
        ], $params));

        file_put_contents(__DIR__ .'/getMapUrl.log', print_r($parms, true) . print_r($ret, true) . PHP_EOL . '------' . PHP_EOL, FILE_APPEND);

        return $ret;
    }
    
    /**
     * @param Ipol\Woo\ViaDelivery\Shipment $shipment
     * @return array
     */
    protected function prepare(Shipment $shipment)
    {
        return [
            'weight'         => $shipment->getWeight(),
            'width'          => $shipment->getWidth(),
            'height'         => $shipment->getHeight(),
            'length'         => $shipment->getLength(),
            'order_price'    => $shipment->getPrice(),
            'country'        => $shipment->getReceiver()['country'],
            // 'region'         => $shipment->getReceiver()['region'],
            'city'           => $shipment->getReceiver()['city'],
            'payment_method' => $shipment->getPaymentMethod(),
            'currency'       => $shipment->getCurrency(),
            'lang'           => strtolower(determine_locale()) == 'ru_ru' ? 'ru' : 'en',
        ];
    }
}