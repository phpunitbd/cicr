function setDefVal(elm, val) {
	if ($(elm) && $(elm).value == '') $(elm).value = val;
}

// allsteps
var AllSteps = Class.create();
AllSteps.prototype = {
  initialize: function(form, forms, saveUrl, refreshShippingUrl) {
    this.form = form;
		this.forms = forms;
    if ($(this.form)) {
      $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
    }
    this.saveUrl = saveUrl;
    this.refreshShippingUrl = refreshShippingUrl;
    this.onSave = this.nextStep.bindAsEventListener(this);
    this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    this.onGetShippingPrice = this.getShippingPrice.bindAsEventListener(this);
  },

  save: function(){
    if (checkout.loadWaiting!=false) return;

	  var isValid = true;
	  this.forms.each(function(name) {
		  if (isValid) {
			  var validator = new Validation(name);
			  isValid = validator.validate();
		  }
	  });

    if (isValid) {

	    var allParams = new Hash();
	    this.forms.each(function(name) {
		    var curParams = Form.serialize(name, true);
		    this.update(curParams);
	    }, allParams);

	    //alert(Object.toJSON(allParams)); return false;

      checkout.setLoadWaiting('allsteps');

      var request = new Ajax.Request(
        this.saveUrl,
        {
          method: 'post',
          //onComplete: this.onComplete, // do not stow waiting, as we must wait more
          onSuccess: this.onSave,
          onFailure: this.onError, //checkout.ajaxFailure.bind(checkout),
          parameters: allParams.toQueryString()
        }
      );
    }
  },

	onError: function(e) {
		ICRC.addStatusMessage('ERROR', 'error: ' + e.status);
	},

  resetLoadWaiting: function(transport){
    checkout.setLoadWaiting(false);
  },

  /**
      This method recieves the AJAX response on success.
      There are 3 options: error, redirect or html with shipping options.
  */
  nextStep: function(transport){
    if (transport && transport.responseText){
      try{
      response = eval('(' + transport.responseText + ')');
      }
      catch (e) {
       response = {};
      }
    }

    if (response.update_steps) {
      $('checkout_steps').replace(response.update_steps);
    }
    if (response.payment_changed) {
      if($(payment.form).down('fieldset') != undefined)  $(payment.form).down('fieldset').replace(); // We forced payment to another one, so we must not re-submit it
    }

    if (response.error) {
			var msg = '';
      if ((typeof response.error) == 'string') {
        msg = response.error;
      } else if ((typeof response.error) == 'Array') {
				try {
      	  msg = response.error.join("\n");
				} catch (e) {}
      }
			if (response.message) {
        if ((typeof response.message) == 'string') {
          msg += response.message;
        } else if ((typeof response.message) == 'Array') {
					try {
		        msg += response.message.join("\n");
					} catch (e) {}
        }
      }
      this.resetLoadWaiting();
			ICRC.addStatusMessage('ERROR', msg);
      return false;
    }

    //checkout.setStepResponse(response);
    ICRC.clearStatusMessage();
    this.refreshReview(true);
  },

  get_shipping_price: function() {
    if (checkout.loadWaiting!=false) return;
    var validator = new Validation('co-shipping-form');
    if (validator.validate()) {
      checkout.setLoadWaiting('shipping-eval');
      var params = new Hash().update(Form.serialize('co-shipping-form', true));

      $$('#co-shipping-method-form .real-form input').each(function (input) {
        if (input.checked) {
          this.update(Form.serialize('co-shipping-method-form', true));
        }
      }, params);

      var request = new Ajax.Request(
        this.refreshShippingUrl,
        {
          method: 'post',
          onComplete: this.onComplete,
          onSuccess: this.onGetShippingPrice,
          onFailure: this.onError,
          parameters: params
        }
      );
    }
  },

  getShippingPrice: function(transport) {
    if (transport && transport.responseText) {
      try {
        response = eval('(' + transport.responseText + ')');
      }
      catch (e) {
        response = {};
      }
      if (response.html) {
        // stop waiting before replacing html as button will be replaced
        checkout.setLoadWaiting(false);
        $('checkout-shipping-method-load').update(response.html);
        is_eco_selected = is_prio_selected = is_rr_selected = false;
      }
      //if (response.payment_html) {
      //  $('checkout-payment-method-load').update(response.payment_html);
      //}
    }
  },

  refreshReview: function(setStepResponse) {
      var request = new Ajax.Request(
        '/checkout/onepage/refreshReview',
        {
          method: 'post',
          onComplete: this.onComplete,
          onSuccess: function(transport) {
            if (transport && transport.responseText){
              try{
              response = eval('(' + transport.responseText + ')');
              }
              catch (e) {
               response = {};
              }
            }

            if (response.update_steps) {
              $('checkout_steps').replace(response.update_steps);
            }
            if (setStepResponse)
              checkout.setStepResponse(response);
          },
          onFailure: this.onError
        }
      );
  }
}

