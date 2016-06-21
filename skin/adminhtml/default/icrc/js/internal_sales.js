// -*- mode: js -*-
var IcrcAdminOrder = {
  oldsType: 'INIT',
  initDone: false,
  _BillingInfoCollection: [],
  log: function(msg) {
    if (console && console.log)
      console.log(msg);
  },
  changeBillingInfoVisibility: function() {
    IcrcAdminOrder.log('changeBillingInfoVisibility');
    var sel = $('icrc-billing-info-list');
    if (!sel || sel.value == 'new') {
      $$('#payment_form_icrc .icrc-payment-auto').invoke('show');
    }
    else {
      $$('#payment_form_icrc .icrc-payment-auto').invoke('hide');
    }
  },
  changeBillingInfoSelection: function() {
    IcrcAdminOrder.log('changeBillingInfoSelection');
    var sel = $('icrc-billing-info-list');
    if (!sel || sel.value == 'new') {
      $('icrc-int-unit').value = '';
      $('icrc-int-cc').value = '';
      $('icrc-int-oc').value = '';
    }
    else {
      $('icrc-int-unit').value = IcrcAdminOrder._BillingInfoCollection[sel.value].unit;
      $('icrc-int-cc').value = IcrcAdminOrder._BillingInfoCollection[sel.value].cost_center;
      $('icrc-int-oc').value = IcrcAdminOrder._BillingInfoCollection[sel.value].objective_code;
    }
    IcrcAdminOrder.changeBillingInfoVisibility();
  },
  onShippingTypeChange: function() {
    var _type = $('icrc-shipping-type').value;
    if (IcrcAdminOrder.oldsType == _type)
      return;
    IcrcAdminOrder.log('onShippingTypeChange (' + IcrcAdminOrder.oldsType + ' -> ' + _type + ')');
    IcrcAdminOrder.oldsType = _type;
    if (_type == '') {
      $('order-addresses').hide();
      IcrcAdminOrder.removeRequiredEntryFromShipping();
    }
    if (_type == 'unit') {
      $('order-addresses').hide();
      IcrcAdminOrder.removeRequiredEntryFromShipping();
      IcrcAdminOrder.setShippingAsBilling.defer(true);
      //order.setShippingMethod('icrc_internal_hq');
    }
    if (_type == 'delegation') {
      IcrcAdminOrder.restoreRequiredEntryFromShipping();
      $('order-addresses').show();
      IcrcAdminOrder.setShippingAsBilling.defer(false);
    }
    if (order && order.shipping_unit_autocompleter)
      order.shipping_unit_autocompleter.options.defaultParams = 'type=' + _type;
  },
  setShippingAsBilling: function(val) {
    IcrcAdminOrder.log('setShippingAsBilling:' + val);
    if (order.shippingAsBilling != val)
      order.setShippingAsBilling(val);
  },
  icrcPaymentInfoCheckInit: function() {
    IcrcAdminOrder.log('icrcPaymentInfoCheckInit');
    if (order && $('payment_form_icrc')) {
      order.icrcInitAutocomplete();
    }
    if (IcrcAdminOrder.initDone == false && order && $('payment_form_icrc')) {
      IcrcAdminOrder.log('payment_form_icrc exists, do init');
      IcrcAdminOrder.initDone = true;
      order.icrcPaymentInfoInit();
      IcrcAdminOrder.removeRequiredEntryFromBilling();
    }
  },
  removeRequiredEntryFromBilling: function() {
    $$('#order-billing_address .entry-edit fieldset .required-entry').each(function (item, id) {
        item.removeClassName('required-entry');
        if (!item.hasClassName('was-required-entry'))
          item.addClassName('was-required-entry');
      });
  },
  removeRequiredEntryFromShipping: function() {
    $$('#order-shipping_address .entry-edit fieldset .required-entry').each(function (item, id) {
        item.removeClassName('required-entry');
        if (!item.hasClassName('was-required-entry'))
          item.addClassName('was-required-entry');
      });
  },
  restoreRequiredEntryFromShipping: function() {
    $$('#order-shipping_address .entry-edit fieldset .was-required-entry').each(function (item, id) {
        if (!item.hasClassName('required-entry'))
          item.addClassName('required-entry');
      });
  },
  fillInBillingInfo: function() {
    IcrcAdminOrder.checkFieldValue('order-billing_address_firstname', 'ICRC');
    IcrcAdminOrder.checkFieldValue('order-billing_address_lastname', '-');
    IcrcAdminOrder.checkFieldValue('order-billing_address_street0', '19 Avenue de la paix');
    IcrcAdminOrder.checkFieldValue('order-billing_address_city', 'Geneva');
    IcrcAdminOrder.checkFieldValue('order-billing_address_country_id', 'CH');
    IcrcAdminOrder.checkFieldValue('order-billing_address_postcode', '1202');
    IcrcAdminOrder.checkFieldValue('order-billing_address_telephone', '+41227346001');
  },
  checkFieldValue: function(id, value) {
    var field = $(id);
    if (field && field.value == '') {
      field.value = value;
    }
  },
  hideBillingAddress: function() {
    $$('#order-billing_address .entry-edit fieldset').invoke('hide');
    if (!$('order-billing_address-na'))
      $('order-billing_address').insert('<div class="na_panel" id="order-billing_address-na"><span>N/A</span></div>');
    var dim = $('order-shipping_address').down('div').down('fieldset').getDimensions();
    $('order-billing_address-na').setStyle({ width: dim.width + 'px', height: dim.height + 'px', 'background-color': '#e7efef' });
    IcrcAdminOrder.removeRequiredEntryFromBilling();
  },
  afterAutocomplete: function(elem, selectedElement) {
    if(elem && elem.method){
      var data = order.getPaymentData(elem.method);
      if (data) {
         order.loadArea(['card_validation'], true, data);
      } else {
        return;
      }
    }
  }
};

document.observe("dom:loaded", IcrcAdminOrder.icrcPaymentInfoCheckInit);
  
