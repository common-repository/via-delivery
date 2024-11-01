<table class="form-table">
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="via_shop_id">ID магазина</label>
        </th>

        <td class="forminp">
            <fieldset>
                <input id="via_shop_id" type="text" name="shop_id" value="<?= $form_fields['shop_id'] ?>">
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc">
            <label for="via_secret_token">Секретный ключ</label>
        </th>

        <td class="forminp">
            <fieldset>
                <input id="via_secret_token" type="text" name="secret_token" value="<?= $form_fields['secret_token'] ?>">
            </fieldset>
        </td>
    </tr>
</table>