function DisableApplyButton(formId) {
    var is_disabled = window['new_total_' + formId] == 0 || jQuery('#gf_coupon_code_' + formId).val() == '';

    if (is_disabled) {
        jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_button').prop('disabled', true);
    } else {
        jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_button').prop('disabled', false);
    }
}

function ApplyCouponCode(formId) {
    var couponCode = jQuery('#gf_coupon_code_' + formId).val();
    
    if (couponCode === 'undefined' || couponCode == '') {
        return;
    }

    jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_spinner').show();
    jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_button').prop('disabled', true);
    var data = {
        'action': 'wp_subscription_apply_coupon',
        'couponCode': couponCode,
        'formId': formId,
        'total': jQuery('#gf_total_no_discount_' + formId).val()
    }
    var res = null;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: 'POST',
        success:function(response){
            var couponInfo = jQuery.parseJSON(response);

            jQuery('#gf_coupons_container_' + formId + ' .gf_coupon_invalid').remove();
            jQuery('#gf_coupon_code_' + formId).val('');

            if (!couponInfo['is_valid']) {
                jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_info').prepend("<div class='gf_coupon_invalid'>" + couponInfo['invalid_reason'] + '</div>');
                setTimeout(function(){ jQuery('.gf_coupon_invalid').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);
            } else {

                window['gf_coupons' + formId] = couponInfo['coupons'];

                // setting hidden field with list of coupons
                var coupon,
                    coupon_codes = '',
                    i = 0;

                for (coupon in window['gf_coupons' + formId]) {
                    if (i > 0) {
                        coupon_codes += ',';
                    }
                    coupon_codes += window['gf_coupons' + formId][coupon]['code'];

                    i++;
                }

                jQuery('#gf_coupon_codes_' + formId).val(coupon_codes).change();
                jQuery('#gf_coupons_' + formId).val(jQuery.toJSON(window['gf_coupons' + formId]));

                gformCalculateTotalPrice(formId);

            }

            jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_spinner').hide();
        }
    });
}
if (typeof gform !== "undefined") {
    gform.addFilter('gform_product_total', function (total, formId) {
        // Ignore forms that don't have a coupon field.
        if (jQuery('#gf_coupon_code_' + formId).length == 0) {
            return total;
        }

        jQuery('#gf_total_no_discount_' + formId).val(total);

        var coupon_code = gformIsHidden(jQuery('#gf_coupon_code_' + formId)) ? '' : jQuery('#gf_coupon_codes_' + formId).val(),
            has_coupon = coupon_code != '' || jQuery('#gf_coupons_' + formId).val() != '',
            new_total = total;

        if (has_coupon) {
            var total_discount = PopulateDiscountInfo(total, formId);
            new_total = total - total_discount;
        }

        jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_spinner').hide();
        window['new_total_' + formId] = new_total;
        DisableApplyButton(formId);

        return new_total;
    }, 50);
}

function PopulateDiscountInfo(price, formId) {
    var code,
        coupon,
        couponDiscount,
        couponDetails = '',
        safeCode,
        totalDiscount = 0,
        currency = new Currency(gf_global['gf_currency_config']);

    if (window['gf_coupons' + formId] === undefined) {
        window['gf_coupons' + formId] = jQuery.evalJSON(jQuery('#gf_coupons_' + formId).val());
    }

    for (code in window['gf_coupons' + formId]) {
        coupon = window['gf_coupons' + formId][code];
        couponDiscount = GetDiscount(coupon['type'], coupon['amount'], price, totalDiscount);
        totalDiscount += couponDiscount;
        safeCode = coupon.code.replace(/[^A-Za-z0-9]/g, '');

        couponDetails += '<tr class="gf_coupon_item" id="gf_coupon_' + safeCode + '"><td class="gf_coupon_name_container">' +
        '   <a href="javascript:void(0);" onclick="DeleteCoupon(\'' + coupon['code'] + '\' , \'' + formId + '\');">(x)</a>' +
        '   <span class="gf_coupon_name">' + coupon['name'] + '</span>' +
        '</td><td class="gf_coupon_discount_container">' +
        '   <span class="gf_coupon_discount">-' + currency.toMoney(couponDiscount,true) + '</span>' +
        '</td></tr>';
    }

    jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_info').html('<table>' + couponDetails + '</table>');

    return totalDiscount;
}

function GetDiscount(couponType, couponAmount, price, totalDiscount) {
    var discount;

    price = price - totalDiscount;
    if (couponType == 'm') {
        discount = price - couponAmount;
    } else {
        discount = price * Number((couponAmount / 100));
    }

    return gform.applyFilters('gform_coupons_discount_amount', discount, couponType, couponAmount, price, totalDiscount);
}

function DeleteCoupon(code, formId) {
    // check if coupon code is in the process of being applied
    if (jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_spinner').is(':visible')) {
        return;
    }

    var safeCode = code.replace(/[^A-Za-z0-9]/g, '');

    // removing coupon from UI
    jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_' + safeCode).remove();
    jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_spinner').show();
    jQuery('#gf_coupons_container_' + formId + ' #gf_coupon_button').prop('disabled', true);

    // removing coupon from coupon codes hidden input
    var codes_input = jQuery('#gf_coupon_codes_' + formId),
        coupon_codes = codes_input.val().split(','),
        index = jQuery.inArray(code, coupon_codes);

    if (index == -1) {
        return;
    }

    coupon_codes.splice(index, 1);
    codes_input.val(coupon_codes.join(',')).change();

    // removing coupon from coupon details hidden input
    if (code in window['gf_coupons' + formId]) {
        delete window['gf_coupons' + formId][code];
        jQuery('#gf_coupons_' + formId).val(jQuery.toJSON(window['gf_coupons' + formId]));
    }

    gformCalculateTotalPrice(formId);
}