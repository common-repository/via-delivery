<?php
namespace Ipol\Woo\ViaDelivery\Hooks;

use Ipol\Woo\ViaDelivery\Shipping as ShippingModel;

class Shipping
{
    /**
     * @return void
     */
    public static function register()
    {
        add_filter('woocommerce_shipping_packages', [static::class, 'clarifyPackageShippingCost']);
    }

    /**
     * @param array $packages
     * @return array
     */
    public static function clarifyPackageShippingCost(array $packages)
    {
        $form = [];
        
        if (isset($_REQUEST['post_data'])) {
            parse_str($_REQUEST['post_data'], $form);
        } else {
            $form = ['via_selected_point' => sanitize_text_field($_POST['via_selected_point'] ?? '')];
        }
        
        $pointId = false;

        if (!empty($form) && !empty($form['via_selected_point'])) {
            $pointId = sanitize_text_field($form['via_selected_point']);
        }

        if (!$pointId) {
            $session = $GLOBALS['viadelivery']->session();
            $pointId = $session->get('via_selected_point');

            // var_dump($pointId);
            // die;
        }

        if (!$pointId) {
            return $packages;
        }

        foreach ($packages as &$package) {
            foreach ($package['rates'] as $rate) {
                if (!ShippingModel::isHeirRate($rate)) {
                    continue;
                }
                
                $shipping = new ShippingModel($rate->instance_id);
                $shipping->calculate_shipping_concrete($package, $pointId);

                $package['rates'] = array_merge($package['rates'], $shipping->rates);
                
                break;
            }

        }

        return $packages;
    }
}