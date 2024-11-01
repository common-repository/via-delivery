<?php
use Ipol\Woo\ViaDelivery\Helpers\View;
?>
<h1><?= __('Via.Delivery', 'viadelivery') ?></h1>

<div class="via-dashboard">
    <div class="via-dashboard__item via-dashboard__item--full">
        <h3 class="via-dashboard__item-title">Доставка по городам</h3>

        <?= View::load('backend/dashboard/dash-cities', $args) ?>

        <!--iframe src="https://widget.viadelivery.pro/via.charts/cities.html?id=<?= $settings['shop_id'] ?>&locale=<?= $locale ?>" frameborder="0" width="100%" height="240px" scrolling="no"></!--iframe-->
    </div>

    <div class="via-dashboard__item via-dashboard__item--half">
        <h3 class="via-dashboard__item-title">Платежи по видам</h3>

        <?= View::load('backend/dashboard/dash-payment', $args) ?>

        <!--iframe src="https://widget.viadelivery.pro/via.charts/payment-types.html?id=<?= $settings['shop_id'] ?>&locale=<?= $locale ?>" frameborder="0" width="100%" height="240px" scrolling="no"></!--iframe-->
    </div>

    <div class="via-dashboard__item via-dashboard__item--half">
        <h3 class="via-dashboard__item-title">Статусы доставки</h3>

        <?= View::load('backend/dashboard/dash-status', $args) ?>

        <!--iframe src="https://widget.viadelivery.pro/via.charts/unclaimed.html?id=<?= $settings['shop_id'] ?>&amp;locale=<?= $locale ?>" frameborder="0" width="100%" height="240px" scrolling="no"></!--iframe-->
    </div>

    <div class="via-dashboard__item via-dashboard__item--full">
        <h3 class="via-dashboard__item-title">Количество отгрузок в сутки</h3>
        
        <?= View::load('backend/dashboard/dash-shipments-per-day', $args) ?>

        <!--iframe src="https://widget.viadelivery.pro/via.charts/shipments-per-day.html?id=<?= $settings['shop_id'] ?>&locale=<?= $locale ?>" frameborder="0" width="100%" height="240px" scrolling="no"></!--iframe-->
    </div>
</div>

<style>
    .via-dashboard {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        grid-gap: 16px;
        gap: 16px;
    }

    .via-dashboard__item {
        box-shadow: rgba(0, 0, 0, 0) 0px 0;
        padding: 24px;
        border-radius: 4px;
        background: #fff;
    }
    
    .via-dashboard__item--full {
        grid-column: span 2/span 2;
    }

    .via-dashboard__item--half {
        grid-column: span 1/span 1;
    }

    .via-dashboard__item-title {
        margin-bottom: 16px;
        font-weight: bold;
    }
</style>