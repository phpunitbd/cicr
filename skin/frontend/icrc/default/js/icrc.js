
var ICRC = {
  cartHidden: true,
  unit_validator_data: 0,
  delegation_validator_data: 0,
  unit_delegation_validator_data: 0,
  oc_validator_data: 0,
  cc_validator_data: 0,
  addStatusMessage: function(status, content) {
    var newmsg = new Element('li', {'class': status == 'SUCCESS' ? 'success-msg' : 'error-msg'}).
        insert(new Element('ul').insert(new Element('li').insert(new Element('span').update(content))));
    var a = $$('.messages');
    if (a.length == 0) {
        var newmessages = new Element('li', {'class': 'messages'}).insert(newmsg);
        var m = $$('.main').first();
        if (m) {
            m.insert({'top': newmessages});
        }
    }
    a.each(function (i) {
        i.update(newmsg); 
    });
  },
  addIntoStatusMessage: function(status, content) {
    var a = $$('.messages li.' + (status == 'SUCCESS' ? 'success-msg' : 'error-msg'));
    if (a.length == 0)
        return ICRC.addStatusMessage(status, content);
    var newmsg = new Element('li').insert(new Element('span').update(content));
    a.each(function (i) {
        i.insert(newmsg); 
    });
  },
  clearStatusMessage: function() {
    var a = $$('.messages');
    if (a.length >= 0)
      a.each(function (i) {
          i.remove();
      });
  },
  openCart: function(delay) {
    if (ICRC.cartHidden == false) return;
    $('cart-slide').show();
    //Effect.BlindDown('cart-slide', { queue: { position: 'end', scope: 'cartq', limit: 2 } });
    ICRC.cartHidden = false;
    var b = $('mini-cart-core');
    var s = $('mini-cart-middle-shadow');
    var h = b.getHeight() - 10;
    h = h + 'px';
    s.setStyle({ height: h });
  },
  closeCart: function() {
    if (ICRC.cartHidden == true) return;
    $('cart-slide').hide();
    //Effect.BlindUp('cart-slide', { queue: { position: 'end', scope: 'cartq', limit: 2 } });
    ICRC.cartHidden = true;
  },
  toggleCart: function() {
    if (ICRC.cartHidden) ICRC.openCart();
    else ICRC.closeCart();
  },
  downloadPdf: function(base, id, button, file, name) {
    var form = $(button).up('form');
    var validator = new Validation(form);
    if (!validator.validate()) return;
    if (id < 0) return;
    try {
      dcsMultiTrack('DCS.dcsuri', base + 'id/' + id, 'WT.ti', file, 'DCSext.download', 'true');
    } catch (e) {
      ICRC.log(e.message);
    }
    try {
      _gaq.push(['_trackEvent', 'pdf', 'Play', name || file]);
    } catch (e) {
      ICRC.log(e.message);
    }
    window.location.href = base + 'id/' + id;
  },  
  getCurrentlySelectedProductIdFromConfig: function(config, option, attr) {
    var opts = config.attributes[attr].options;
    for (var i = 0; i < opts.length; ++i) {
      if (opts[i].id == option)
        return opts[i].products[0];
    }
    return -1;
  },
  testPlaceholder: function() {
    return ('placeholder' in document.createElement('input')); 
  },
  fallbackPlaceholder: function(element) {
    if (this.testPlaceholder()) return;
    new this.Placeholder(element);
  },
  Placeholder: Class.create({
    initialize: function (element) {
        this.element = element;
        this.placeholder = element.readAttribute('placeholder');
        this.blur();
        Event.observe(this.element, 'focus', this.focus.bindAsEventListener(this));
        Event.observe(this.element, 'blur', this.blur.bindAsEventListener(this));
    },
    focus: function () {
        if (this.element.hasClassName('placeholder'))
            this.element.clear().removeClassName('placeholder');
    },
    blur: function () {
        if (this.element.value === '')
            this.element.addClassName('placeholder').value = this.placeholder;
    }
  }),
  log: function(msg) {
    if (typeof console !== 'undefined') {
      try { console.log(msg); } catch (se) {}
    }
  },
  newsletter: {
    register: function(logged) {
      $('newsletter-mini-unsuscribe').value = false;
      if (this.check(logged))
        this.submit();
      return this.check(logged);
    },
    unregister: function(logged) {
      $('newsletter-mini-unsuscribe').value = true;
      if (this.check(logged))
        this.submit();
      return this.check(logged);
    },
    check: function(logged) {
      if (!logged) {
        var captcha = $('captcha-input-box-newsletter');
        if (captcha == false)
          return true;
        var captcha2 = $('captcha-image-box-newsletter');
        var is_visible = captcha.visible();
        captcha.show();
        captcha2.show();
        return is_visible;
      }
      return true;
    },
    show: function() {
      var c = $('captcha-input-box-newsletter');
      if (c == null) return;
      c.show();
      $('captcha-image-box-newsletter').up().show();
    },
    hide: function() {
      var c = $('captcha-input-box-newsletter');
      if (c == null) return;
      c.hide();
      $('captcha-image-box-newsletter').up().hide();
    }, init: function(element) {
        var newsletterFormajax = new VarienForm(element, false);
        this.hide();
        if($('newsletter-mini-email')) {
            $('newsletter-mini-email').observe('click', this.show);
            $('newsletter-mini-email').observe('change', this.show);
            $('newsletter-mini-email').observe('focus', this.show);
        }
        $('newsletterclick').observe('click', this.layer);
      ICRC.fallbackPlaceholder($('newsletter-mini-email'));
    }, layer: function() {
        if($('newsletterclick').hasClassName('open')) {
            $('newsletterclick').removeClassName('open');
            $('newletterlayer').removeClassName('open');
        } else {
            $$('.goto-newsletter .dyn-block').each(function(el) {
                el.style.display = "block";
            });
            $('newsletterclick').addClassName('open');
            $('newletterlayer').addClassName('open');
        }
    }, submit: function() {
        var $this = $("newsletterFormajax");
        new Ajax.Request($this.readAttribute("action"), 
        {
            method:$this.readAttribute('method'),
            parameters: $this.serialize(),
            onSuccess: function(transport){
                var json = transport.responseText.evalJSON();
                if(json.error)
                {
                    $('messages_newsletter_mini').update('<span class="error">'+json.error+'</span>');
                    $('newsletter').captcha.refresh();
                } else {
                    if(json.logged == true) {
                        $$('.goto-newsletter').each(function(el) {
                            el.remove();
                        });
                    } else {
                        $$('.goto-newsletter .dyn-block').each(function(el) {
                            el.style.display = "none";
                        });  
                    }
                    $('messages_newsletter_mini').update('<span class="success">'+json.success+'</span>');
                    $('newsletter').captcha.refresh();
                }
            }
        });
    }
  },
  search: {
    onClick: function(input, event) {
      if (ICRC.search.isOnButton(input, event)) {
        if (input.value == '') {
          // search with no text
          return;
        }
        // search
        input.form.submit();
      }
    },
    isOnButton: function(input, event) {
      if (typeof(event.offsetX) == "undefined")
        event.offsetX = event.layerX; // for gecko
      return event.offsetX >= (input.clientWidth - 16);
    },
    onMouseMove: function(input, event) {
      if (ICRC.search.isOnButton(input, event)) {
        input.setStyle({cursor: "pointer"});
      }
      else {
        input.setStyle({cursor: "text"});
      }
    }
  },
  initSelects: function() {
    var select, selects, _i, _len, _results;
    if (Prototype.Browser.IE && (Prototype.BrowserFeatures['Version'] === 6 || Prototype.BrowserFeatures['Version'] === 7)) {
      return;
    }
    selects = $$(".chzn-select");
    _results = [];
    for (_i = 0, _len = selects.length; _i < _len; _i++) {
      select = selects[_i];
      _results.push(new Chosen(select));
    }
    deselects = $$(".chzn-select-deselect");
    for (_i = 0, _len = deselects.length; _i < _len; _i++) {
      select = deselects[_i];
      _results.push(new Chosen(select,{allow_single_deselect:true}));
    }
    return _results;
  },
  productView: {
    icrcIsStreaming: false,
    alignShare: function() {
      var sb = $('ircr-right-side-bar');
      var sl = $('icrc-share-product-bar');
      var pv = $('product-view-details');
      sbh = sb.getHeight();
      slh = sl.getHeight();
      pvh = pv.getHeight();
      if (pvh - slh < sbh) {
        var d = sbh - (pvh - slh);
        pv.setStyle({ 'height': (sbh - 20) + 'px' });
        sl.setStyle({ 'position': 'absolute', 'bottom' : '0px', 'left' : '0px', 'right' : '0px', 'margin': '10px auto 0' });
      }
    },
    resetShareAlign: function() {
      $('icrc-share-product-bar').setStyle({ 'position': 'static', 'margin': '40px auto 0' });
      $('product-view-details').setStyle({ 'height': 'auto' });
    },
    getStreaming: function(sku) {
      var me = this;
	    if (this.icrcIsStreaming)
	    {
		    this.icrcIsStreaming = false;
		    streamingDiv = document.getElementById("streaming-div");
		    streamingDiv.innerHTML = "";
        me.resetShareAlign();
        me.alignShare();
		    return;
	    }
      var url = '/icrc/video/viewAjax/sku/' + sku;
      var request = new Ajax.Request(
          url,
          {
            method: 'get',
            onComplete: function(transport) {
              me.resetShareAlign();
              Element.insert($('streaming-div'), transport.responseText);
              me.alignShare();
            }
          }
        );
	    this.icrcIsStreaming = true;
    }
  },
  proceedIfSfc: function(url) {
    var validator = new Validation('co-accept-sfc');
    if (validator.validate()) {
      window.location = url;
    }
  },
  getExhibition: function(url) {
    document.location.href = url;
  },
  isLatin1: function(value) {
    var i = value.length;
    while (i--) {
      if (!ICRC.latin1[value[i]])
        return false;
    }
    return true;
  },
  checkChange: function(event) {
    return false;
  },
  checkKeyDown: function(event) {
    var allowed = false;
    switch (event.keyCode) {
      case Event.KEY_BACKSPACE:
      case Event.KEY_TAB:
      case Event.KEY_RETURN:
      case Event.KEY_ESC:
      case Event.KEY_LEFT:
      case Event.KEY_UP:
      case Event.KEY_RIGHT:
      case Event.KEY_DOWN:
      case Event.KEY_DELETE:
      case Event.KEY_HOME:
      case Event.KEY_END:
      case Event.KEY_PAGEUP:
      case Event.KEY_PAGEDOWN:
      case Event.KEY_INSERT:
      return true;
    }
    if (event.char)
      allowed = ICRC.latin1[event.char];
    else {
      if (event.which == null)
        char = String.fromCharCode(event.keyCode);
      else 
        char = String.fromCharCode(event.which);
      allowed = ICRC.latin1[char];
    }
    if (!allowed) {
      Event.stop(event);
      return false;
    }
    return true;
  },
  latin1: {
    " ": 1,
    "!": 1,
    "\"": 1,
    "#": 1,
    "$": 1,
    "%": 1,
    "&": 1,
    "'": 1,
    "(": 1,
    ")": 1,
    "*": 1,
    "+": 1,
    ",": 1,
    "-": 1,
    ".": 1,
    "/": 1,
    "0": 1,
    "1": 1,
    "2": 1,
    "3": 1,
    "4": 1,
    "5": 1,
    "6": 1,
    "7": 1,
    "8": 1,
    "9": 1,
    ":": 1,
    ";": 1,
    "<": 1,
    "=": 1,
    ">": 1,
    "?": 1,
    "@": 1,
    "A": 1,
    "B": 1,
    "C": 1,
    "D": 1,
    "E": 1,
    "F": 1,
    "G": 1,
    "H": 1,
    "I": 1,
    "J": 1,
    "K": 1,
    "L": 1,
    "M": 1,
    "N": 1,
    "O": 1,
    "P": 1,
    "Q": 1,
    "R": 1,
    "S": 1,
    "T": 1,
    "U": 1,
    "V": 1,
    "W": 1,
    "X": 1,
    "Y": 1,
    "Z": 1,
    "[": 1,
    "\\": 1,
    "]": 1,
    "^": 1,
    "_": 1,
    "`": 1,
    "a": 1,
    "b": 1,
    "c": 1,
    "d": 1,
    "e": 1,
    "f": 1,
    "g": 1,
    "h": 1,
    "i": 1,
    "j": 1,
    "k": 1,
    "l": 1,
    "m": 1,
    "n": 1,
    "o": 1,
    "p": 1,
    "q": 1,
    "r": 1,
    "s": 1,
    "t": 1,
    "u": 1,
    "v": 1,
    "w": 1,
    "x": 1,
    "y": 1,
    "z": 1,
    "{": 1,
    "|": 1,
    "}": 1,
    "~": 1,
    "¡": 1,
    "¢": 1,
    "£": 1,
    "¤": 1,
    "¥": 1,
    "¦": 1,
    "§": 1,
    "¨": 1,
    "©": 1,
    "ª": 1,
    "«": 1,
    "¬": 1,
    "-": 1,
    "®": 1,
    "¯": 1,
    "°": 1,
    "±": 1,
    "²": 1,
    "³": 1,
    "´": 1,
    "µ": 1,
    "¶": 1,
    "·": 1,
    "¸": 1,
    "¹": 1,
    "º": 1,
    "»": 1,
    "¼": 1,
    "½": 1,
    "¾": 1,
    "¿": 1,
    "À": 1,
    "Á": 1,
    "Â": 1,
    "Ã": 1,
    "Ä": 1,
    "Å": 1,
    "Æ": 1,
    "Ç": 1,
    "È": 1,
    "É": 1,
    "Ê": 1,
    "Ë": 1,
    "Ì": 1,
    "Í": 1,
    "Î": 1,
    "Ï": 1,
    "Ð": 1,
    "Ñ": 1,
    "Ò": 1,
    "Ó": 1,
    "Ô": 1,
    "Õ": 1,
    "Ö": 1,
    "×": 1,
    "Ø": 1,
    "Ù": 1,
    "Ú": 1,
    "Û": 1,
    "Ü": 1,
    "Ý": 1,
    "Þ": 1,
    "ß": 1,
    "à": 1,
    "á": 1,
    "â": 1,
    "ã": 1,
    "ä": 1,
    "å": 1,
    "æ": 1,
    "ç": 1,
    "è": 1,
    "é": 1,
    "ê": 1,
    "ë": 1,
    "ì": 1,
    "í": 1,
    "î": 1,
    "ï": 1,
    "ð": 1,
    "ñ": 1,
    "ò": 1,
    "ó": 1,
    "ô": 1,
    "õ": 1,
    "ö": 1,
    "÷": 1,
    "ø": 1,
    "ù": 1,
    "ú": 1,
    "û": 1,
    "ü": 1,
    "ý": 1,
    "þ": 1,
    "ÿ": 1,
    " ": 1
  }
};

(function() {
  function isBody(element) {
    return element.nodeName.toUpperCase() === 'BODY';
  }

  function isHtml(element) {
    return element.nodeName.toUpperCase() === 'HTML';
  }

  function isDocument(element) {
    return element.nodeType === Node.DOCUMENT_NODE;
  }

  function isDetached(element) {
    return element !== document.body &&
     !Element.descendantOf(element, document.body);
  }
  
  // Monkey patching for IE8 bug
  Element.getOffsetParent = function(element) {
    element = $(element);

    if (isDocument(element) || isDetached(element) || isBody(element) || isHtml(element))
      return $(document.body);

    var isInline = (Element.getStyle(element, 'display') === 'inline');
    if (!isInline && element.offsetParent && element.offsetParent != document.body) return $(element.offsetParent);

    while ((element = element.parentNode) && element !== document.body) {
      if (Element.getStyle(element, 'position') !== 'static') {
        return isHtml(element) ? $(document.body) : $(element);
      }
    }

    return $(document.body);
  }
})();

document.observe('dom:loaded', ICRC.initSelects);

/* custom validators */
Validation.add('radar-validate-unit', 'The unit does not exist', function(v) {
  return ICRC.unit_validator_data && ICRC.unit_validator_data[v];
});
Validation.add('radar-validate-delegation', 'The delegation does not exist', function(v) {
  return ICRC.delegation_validator_data && ICRC.delegation_validator_data[v];
});
Validation.add('radar-validate-unit-delegation', 'The unit or delegation does not exist', function(v) {
  return ICRC.unit_delegation_validator_data && ICRC.unit_delegation_validator_data[v];
});
Validation.add('radar-validate-objective-code', 'The objective code does not exist', function(v) {
  return ICRC.oc_validator_data && ICRC.oc_validator_data[v];
});
Validation.add('radar-validate-cost-center', 'The cost center does not exist', function(v) {
  return ICRC.cc_validator_data && ICRC.cc_validator_data[v];
});
Validation.add('radar-validate-unit-delegation-id', 'The unit or delegation does not exist', function(v, elm) {
  var reId = /^unit-delegation-selector-id-(.+)$/;
  var result = ICRC.unit_delegation_validator_data && ICRC.unit_delegation_validator_data[v];
  $w(elm.className).each(function(name) {
    var m = reId.exec(name);
    if (m) {
      if ($(m[1])) {
        if ($(m[1]).value == 'unit')
          result = ICRC.unit_validator_data && ICRC.unit_validator_data[v];
        if ($(m[1]).value == 'delegation')
          result = ICRC.delegation_validator_data && ICRC.delegation_validator_data[v];
      }
    }
  });
  return result;
});
Validation.add('validate-latin-1', 'Invalid input: Please use western european characters', ICRC.isLatin1);
Translator.add('Invalid input: Please use western european characters', 'Saisie invalide : veuillez utiliser des caratères d\'europe occidentale');
