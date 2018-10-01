
/*
jQuery Credit Card Validator 1.0
Copyright 2012-2015 Pawel Decowski
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software
is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
IN THE SOFTWARE.
 */

(function() {
  var $,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  $ = jQuery;

  $.fn.validateCreditCard = function(callback, options) {
    var bind, card, card_type, card_types, get_card_type, is_valid_length, is_valid_luhn, normalize, validate, validate_number, _i, _len, _ref;
    card_types = [
      {
        name: 'amex',
        pattern: /^3[47]/,
        valid_length: [15]
      }, {
        name: 'diners_club_carte_blanche',
        pattern: /^30[0-5]/,
        valid_length: [14]
      }, {
        name: 'diners_club_international',
        pattern: /^36/,
        valid_length: [14]
      }, {
        name: 'jcb',
        pattern: /^35(2[89]|[3-8][0-9])/,
        valid_length: [16]
      }, {
        name: 'laser',
        pattern: /^(6304|670[69]|6771)/,
        valid_length: [16, 17, 18, 19]
      }, {
        name: 'visa_electron',
        pattern: /^(4026|417500|4508|4844|491(3|7))/,
        valid_length: [16]
      }, {
        name: 'visa',
        pattern: /^4/,
        valid_length: [16]
      }, {
        name: 'mastercard',
        pattern: /^5[1-5]/,
        valid_length: [16]
      }, {
        name: 'maestro',
        pattern: /^(5018|5020|5038|6304|6759|676[1-3])/,
        valid_length: [12, 13, 14, 15, 16, 17, 18, 19]
      }, {
        name: 'discover',
        pattern: /^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,
        valid_length: [16]
      }
    ];
    bind = false;
    if (callback) {
      if (typeof callback === 'object') {
        options = callback;
        bind = false;
        callback = null;
      } else if (typeof callback === 'function') {
        bind = true;
      }
    }
    if (options == null) {
      options = {};
    }
    if (options.accept == null) {
      options.accept = (function() {
        var _i, _len, _results;
        _results = [];
        for (_i = 0, _len = card_types.length; _i < _len; _i++) {
          card = card_types[_i];
          _results.push(card.name);
        }
        return _results;
      })();
    }
    _ref = options.accept;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      card_type = _ref[_i];
      if (__indexOf.call((function() {
        var _j, _len1, _results;
        _results = [];
        for (_j = 0, _len1 = card_types.length; _j < _len1; _j++) {
          card = card_types[_j];
          _results.push(card.name);
        }
        return _results;
      })(), card_type) < 0) {
        throw "Credit card type '" + card_type + "' is not supported";
      }
    }
    get_card_type = function(number) {
      var _j, _len1, _ref1;
      _ref1 = (function() {
        var _k, _len1, _ref1, _results;
        _results = [];
        for (_k = 0, _len1 = card_types.length; _k < _len1; _k++) {
          card = card_types[_k];
          if (_ref1 = card.name, __indexOf.call(options.accept, _ref1) >= 0) {
            _results.push(card);
          }
        }
        return _results;
      })();
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        card_type = _ref1[_j];
        if (number.match(card_type.pattern)) {
          return card_type;
        }
      }
      return null;
    };
    is_valid_luhn = function(number) {
      var digit, n, sum, _j, _len1, _ref1;
      sum = 0;
      _ref1 = number.split('').reverse();
      for (n = _j = 0, _len1 = _ref1.length; _j < _len1; n = ++_j) {
        digit = _ref1[n];
        digit = +digit;
        if (n % 2) {
          digit *= 2;
          if (digit < 10) {
            sum += digit;
          } else {
            sum += digit - 9;
          }
        } else {
          sum += digit;
        }
      }
      return sum % 10 === 0;
    };
    is_valid_length = function(number, card_type) {
      var _ref1;
      return _ref1 = number.length, __indexOf.call(card_type.valid_length, _ref1) >= 0;
    };
    validate_number = (function(_this) {
      return function(number) {
        var length_valid, luhn_valid;
        card_type = get_card_type(number);
        luhn_valid = false;
        length_valid = false;
        if (card_type != null) {
          luhn_valid = is_valid_luhn(number);
          length_valid = is_valid_length(number, card_type);
        }
        return {
          card_type: card_type,
          valid: luhn_valid && length_valid,
          luhn_valid: luhn_valid,
          length_valid: length_valid
        };
      };
    })(this);
    validate = (function(_this) {
      return function() {
        var number;
        number = normalize($(_this).val());
        return validate_number(number);
      };
    })(this);
    normalize = function(number) {
      return number.replace(/[ -]/g, '');
    };
    if (!bind) {
      return validate();
    }
    this.on('input.jccv', (function(_this) {
      return function() {
        $(_this).off('keyup.jccv');
        return callback.call(_this, validate());
      };
    })(this));
    this.on('keyup.jccv', (function(_this) {
      return function() {
        return callback.call(_this, validate());
      };
    })(this));
    callback.call(this, validate());
    return this;
  };

}).call(this);

var payfortFort = (function () {
  return {
    validateCreditCard: function(element) {
      var isValid = false;
      var eleVal = $(element).val();
      eleVal = this.trimString(element.val());
      eleVal = eleVal.replace(/\s+/g, '');
      $(element).val(eleVal);
      // $(element).validateCreditCard(function(result) {
      //   isValid = result.valid;
      // });
        isValid = true;
      return isValid;
    },
    validateCardHolderName: function(element) {
      $(element).val(this.trimString(element.val()));
      var cardHolderName = $(element).val();
      if(cardHolderName.length > 50) {
        return false;
      }
      return true;
    },
    validateCvc: function(element) {
      $(element).val(this.trimString(element.val()));
      var cvc = $(element).val();
      if(cvc.length > 4 || cvc.length == 0) {
        return false;
      }
      if(!this.isPosInteger(cvc)) {
        return false;
      }
      return true;
    },
    isDefined: function(variable) {
      if (typeof (variable) === 'undefined' || typeof (variable) === null) {
        return false;
      }
      return true;
    },
    isPosInteger: function(data) {
      var objRegExp  = /(^\d*$)/;
      return objRegExp.test( data );
    },
    trimString: function(str){
      return str.trim();
    }
  };
})();

var payfortFortMerchant = (function () {
  return {
    process: function () {
      this.hideError();
      var isValid = payfortFort.validateCardHolderName($('#card_holder'));
      if(!isValid) {
          this.showError('Invalid Card Holder Name');
          return false;
      }
      isValid = payfortFort.validateCreditCard($('#card_number'));
      if(!isValid) {
          this.showError('Invalid Credit Card Number');
          return false;
      }
      isValid = payfortFort.validateCvc($('#cvv'));
      if(!isValid) {
          this.showError('Invalid Card CVV');
          return false;
      }
      // now here we write the process
      generatePaymentPage.process();
    },
    showError: function(msg) {
      alert(msg);
      //console.log(msg);
    },
    hideError: function() {
      return;
    }
  };
})();

var generatePaymentPage = (function() {
  return {
    process: function () {
      var $form = $('#payfort_fort_form');
      var form_elements = {};
      form_elements = $form.find('input').serialize();
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: $form.attr('action'),
        data : form_elements,
        success : function(response) {
          if(response.form) {
            $('body').append(response.form);
            monthValue = "0"+$('#exp_month').val();
            var expDate = $('#exp_year').val().substring(2)+''+monthValue.slice(-2);
            var mp2_params = {};
            mp2_params.card_holder_name = $('#card_holder').val();
            mp2_params.card_number = $('#card_number').val();
            mp2_params.expiry_date = expDate;
            mp2_params.card_security_code = $('#cvv').val();
            $.each(mp2_params, function(k, v){
                $('<input>').attr({
                    type: 'text',
                    id: k,
                    name: k,
                    value: v
                }).appendTo('#payfort_final_payment_form');
            });
            $('#payfort_final_payment_form input[type=submit]').click();

          } else {
            payfortFortMerchant.showError('Unable to contact server for payment processing');
          }
        },
        error : function(jqXHR, textStatus, errorThrown) {
          payfortFortMerchant.showError(errorThrown);
        }
      });
    }
  };
})();
