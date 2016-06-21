function addToCartJsError(e) {
    addToCartJsWait.end();
}

function addToCartJsComplete(transport) {
    addToCartJsWait.end();
    if (transport && transport.responseText){ 
        try {
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
    }

    if (response.status == 'SUCCESS') {
        if (response.sidebar) {
            $('cart:all').update(response.sidebar);
            ICRC.openCart(10);
        }
        if (response.toplink) {
            var a = $$('.header .links');
            a.each(function (i) { i.update(response.toplink); });
        }
        if (response.items) {
            var a = $$('.header .quick-access .cart-count');
            a.each(function (i) { i.update(response.items); });
        }
        if (response.header) {
            ICRC.closeCart();
            $('cart-slide').replace(response.header);
            ICRC.openCart(10);
        }
    }

    if (response.message) {
        if (Object.prototype.toString.call(response.message) === '[object Array]') {
            ICRC.clearStatusMessage();
            response.message.each(function(msg) { ICRC.addIntoStatusMessage(response.status, msg); });
        }
        else {
          ICRC.addStatusMessage(response.status, response.message);
        }
    }
    if (response.error) {
        ICRC.addStatusMessage(response.status, response.error);
    }
}

function addConfigurableToCartJs(url, form, qty, button) {
  var validator = new Validation(form);
  if (validator.validate()) {
    startLoadingAddToCart(button);
    var params = $(form).serialize(true);
    if (qty) params.qty = $(qty).value;
    var p = new Hash(params).merge({isAjax: 1}).toQueryString();
    var request = new Ajax.Request(url,
      {
          method: 'post',
          onComplete: addToCartJsComplete,
          onFailure: addToCartJsError,
          parameters: p
      }
    );
  }
}

function addSimpleToCartJs(url, form, qty, button) {
  var validator = new Validation(form);
  if (validator.validate()) {
    startLoadingAddToCart(button);
    var params = $(form).serialize(true);
    if (qty) params.qty = $(qty).value;
    var p = new Hash(params).merge({isAjax: 1}).toQueryString();
    var request = new Ajax.Request(url,
      {
          method: 'post',
          onComplete: addToCartJsComplete,
          onFailure: addToCartJsError,
          parameters: p
      }
    );
  }
}


function productAddToCartFormAjax(button) {
  var validator = new Validation(productAddToCartForm.form);
  if (validator.validate()) {
    startLoadingAddToCart(button);
    var params = productAddToCartForm.form.serialize(true);
    var p = new Hash(params).merge({isAjax: 1}).toQueryString();
    var url = productAddToCartForm.form.action;
    var request = new Ajax.Request(url,
        {
            method: 'post',
            onComplete: addToCartJsComplete,
            onFailure: addToCartJsError,
            parameters: p
        }
    );
  }
}

var addToCartJsWait = {
  button: null,
  wait: null,
  run: false,
  end: function() {
    if (this.run == false) return;
    this.run = false;
    this.button.show();
    this.wait.hide();
  }
}

function startLoadingAddToCart(button) {
  if (!button || addToCartJsWait.run)
    return;
  //alert(button);
  var wait = button.up().down('.please-wait');
  if (wait) {
    wait.show();
    button.hide();
    addToCartJsWait.button = button;
    addToCartJsWait.wait = wait;
    addToCartJsWait.run = true;
  }
}

function productAddGroupedForm(form, button) {
  startLoadingAddToCart(button);
  var params = $(form).serialize(true);
  var p = new Hash(params).merge({isAjax: 1}).toQueryString();
  var url = $(form).action;
  var request = new Ajax.Request(url,
    {
        method: 'post',
        onComplete: addToCartJsComplete,
        onFailure: addToCartJsError,
        parameters: p
    }
  );
}

function productAddGroupedFormQty(form, button, qtyid) {
  var qty = $(qtyid).value;
  if (isNaN(qty)) {
    // TODO: error
    return;
  }
  if (qty != 1) {
    $$('#' + form + ' .hidden-def-qty').each(function(def) {
      //alert(e);
      var target = $(def.alt);
      target.value = def.value * qty;
    });
  }
  productAddGroupedForm(form, button);
}

function productalert(urlProductAlert, productId) {
    var data = { id: productId};
    jQuery.ajax({
        dataType: "json",
        type: "POST",
        url: urlProductAlert,
        data: data,
        success : jQuery.proxy(function(json){
            jQuery.fancybox(json.message);
        },this)
    });
}

function orderLanguage(urlOrderDemand, productId, lang) {
    var data = { id: productId, lang:lang};
    jQuery.ajax({
        dataType: "json",
        type: "POST",
        url: urlOrderDemand,
        data: data,
        success : jQuery.proxy(function(json){
            jQuery.fancybox(json.message);
            jQuery("#otherlanguageForm").on('submit', function(e){
                e.preventDefault();
                var $this = jQuery(this);

                jQuery.ajax({
                    url: $this.attr("action"),
                    type: $this.attr('method'),
                    data: $this.serialize(),
                    success : jQuery.proxy(function(json){
                        json = jQuery.parseJSON(json);
                        if(json.class == 'error')
                        {
                            jQuery('#messages_product_view_language').html('<ul class="messages"><li class="error-msg"><ul><li><span>'+json.message+'</span></li></ul></li></ul>');
                            $('contact').captcha.refresh();
                        } else {
                            jQuery.fancybox(json.message);
                        }
                    },this)
                });
            });
        },this)
    });
}

document.observe("dom:loaded", function() {
});


