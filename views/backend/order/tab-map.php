<?php if ($order->isCreated()) { ?>
    <p><?= __('Order is created', 'viadelivery') ?></p>

<?php } else { ?>
    <iframe src="<?= $mapUrl ?>" style="width: 100%; height: 550px; border: 0;" frameborder="0"></iframe>
    <input id="via_selected_point" type="hidden" name="order[point]" value="<?= $point['id'] ?>">
<?php } ?>