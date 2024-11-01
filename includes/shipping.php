<?php
namespace Ipol\Woo\ViaDelivery;

use Ipol\Woo\ViaDelivery\Helpers\View;

class Shipping extends \WC_Shipping_Method
{
    const METHOD_ID = 'viadelivery';

    /**
     * @var array
     */
    protected $package;

    /**
     * @param \WC_Shipping_Rate $rate
     * @return boolean
     */
    public static function isHeirRate(\WC_Shipping_Rate $rate)
    {
        return static::isHeir($rate->id);
    }

    /**
     * @param \WC_Shipping_Method $method
     * @return boolean
     */
    public static function isHeirMethod(\WC_Order_Item_Shipping $method)
    {
        return static::isHeir($method->get_method_id());
    }

    /**
     * @param \WC_Order $order
     * @return boolean
     */
    public static function isHeirOrder(\WC_Order $order)
    {
        foreach ($order->get_shipping_methods() as $shippingMethod) {
            if (static::isHeirMethod($shippingMethod)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $methodId
     * @return boolean
     */
    public static function isHeir($methodId)
    {
        return substr($methodId, 0, strlen(static::METHOD_ID)) == static::METHOD_ID;
    }

    /**
     * @inheritDoc
     *
     * @param integer $instance_id
     */
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id                 = static::METHOD_ID;
        $this->method_title       = __('Via.Delivery', 'viadelivery');  
        $this->method_description = __('Via.Delivery description', 'viadelivery'); 
        $this->availability       = 'including';
        $this->supports           = [
            'shipping-zones',
            'settings',
            'instance-settings',
        ];
        
        $this->init();
        
        $this->enabled = 'yes';
        $this->title   = 'Via.Delivery';
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = [
            'shop_id' => [
                'type'              => 'text',
                'default'           => '',
                'title'             => __('ShopID', 'viadelivery'),
                'description'       => '',
                'disabled'          => false,

                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'desc_tip'          => false,
                'custom_attributes' => array(),
            ],

            'secret_token' => [
                'type'        => 'text',
                'title'       => __('Secret token', 'viadelivery'),
                'description' => '',
            ],

            // 'language' => [
            //     'type'        => 'select',
            //     'title'       => __('Language', 'viadelivery'),
            //     'description' => '',
            //     'options'     => [
            //         'en' => 'English',
            //         'ru' => 'Russian',
            //     ]
            // ],

            'dimensions_title' => [
                'type'        => 'title',
                'title'       => __('Dimensions', 'viadelivery'),
                'description' => '',
            ],

            'default_weight' => [
                'type'        => 'decimal',
                'title'       => __('Weight, g', 'viadelivery'),
                'description' => '',
                'default'     => 1000,
            ],

            'default_dimensions_width' => [
                'type'        => 'decimal',
                'title'       => __('Width, mm', 'viadelivery'),
                'description' => '',
                'default'     => 10,
            ],

            'default_dimensions_height' => [
                'type'        => 'decimal',
                'title'       => __('Height, mm', 'viadelivery'),
                'description' => '',
                'default'     => 10,
            ],

            'default_dimensions_length' => [
                'type'        => 'decimal',
                'title'       => __('Length, mm', 'viadelivery'),
                'description' => '',
                'default'     => 10,
            ],

            'select_pickpoint_button_title' => [
                'type'        => 'title',
                'title'       => __('Appearance', 'viadelivery'),
                'description' => '',
            ],

            'select_pickpoint_button_text' => [
                'type'        => 'text',
                'title'       => __('Pickpoint selection button title into checkout form', 'viadelivery'),
                'default'     => __('Select pickpoint', 'viadelivery'),
                'description' => '',
            ],

            'select_pickpoint_button_css' => [
                'type'        => 'text',
                'title'       => __('CSS-class pickpoint button into checkout form', 'viadelivery'),
                'default'     => '',
                'description' => '',
            ],
        ];
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function get_instance_form_fields()
    {
        if (empty($this->instance_form_fields)) {
            parent::get_instance_form_fields();

            $this->instance_form_fields = array_merge($this->instance_form_fields, [
                'method_title' => [
                    'type'        => 'text',
                    'title'       => __('Method title into checkout form', 'viadelivery'),
                    'default'     => 'Via.Delivery',
                    'description' => '',
                ],
            ]);
        }

        return $this->instance_form_fields;
    }

    /**
     * @inheritDoc
     *
     * @param array $package
     * 
     * @return void
     */
    public function calculate_shipping($package = [])
    {
        $shipment = $this->getShipment($package);

        if (!$shipment->isPossibleDelivery()) {
            return false;
        }

        $tariff = (new Calculator($this->settings))->preview($shipment);

        if ($tariff['count'] <= 0) {
            return false;
        }

        $this->add_rate([
            'id'    => $this->id,
            'cost'  => $tariff['min_delivery_price'],
            'label' => ''
                . sprintf(__('%s from ', 'viadelivery'), $this->get_option('method_title'))
            ,
        ]);
    }

    /**
     * @param array $package
     * @return void
     */
    public function calculate_shipping_concrete($package = [], $pointId)
    {
        $shipment = $this->getShipment($package);

        if (!$shipment->isPossibleDelivery()) {
            return false;
        }

        $tariff = (new Calculator($this->settings))->calculate($shipment, $pointId);

        $this->add_rate([
            'id'    => $this->id,
            'cost'  => $tariff['price'],
            'label' => sprintf(
                __('%s (%s, %s, %d-%d days)', 'viadelivery'), 
                $this->get_option('method_title'),
                $tariff['description'],
                trim($tariff['city_pref'] .'. '. $tariff['city'] .', '. $tariff['street'], '.'),
                $tariff['min_days'],
                $tariff['max_days']
            ),
            'meta_data' => ['point' => $tariff],
        ]);
    }

    /**
     * @param array $package
     * @return string
     */
    public function getMapUrl(array $package = [], $pointId = null)
    {
        $shipment   = $this->getShipment($package, false);
        $calculator = new Calculator($this->settings);

        return $calculator->getMapUrl($shipment, array_filter([
            'pointId'  => $pointId,
            // 'point_id' => $pointId,
        ]));
    }

    /**
     * @return Ipol\Woo\ViaDelivery\Api\Client
     */
    public function getApiClient()
    {
        return new Api\Client($this->settings);
    }

    /**
     * @return Shipment
     */
    public function getShipment(array $package = [], $geocode = true)
    {
        $country = strval($package['destination']['country']);
        $state = strval($package['destination']['state']);
        $city = strval($package['destination']['city']);

        $WC_Countries = new \WC_Countries();
        $states = (array) $WC_Countries->get_states( $country );

        if (array_key_exists($state, $states)) {
            $state = $states[$state];
        }
        
        if ($geocode && !empty($country) && !empty($city)) {
            $address = $this->getApiClient()->getService('geocode')->identify(
                $country, $state, $city,
                strtolower(get_locale()) == 'ru_ru' ? 'ru' : 'en'
            );
        } else {
            $address = $package['destination'];
        }

        $shipment = new Shipment($this->settings);
        $shipment->setReceiver(
            $address['country'],
            $address['region'],
            $address['city']
        );

        $shipment->setItems(
            Utils\Package::convertMixed($package['contents']), 
            $package['cart_subtotal'],
            $package['cart_currency'] ?? get_option('woocommerce_currency')
        );

        return $shipment;
    }
}