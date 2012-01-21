/**
 * Javascript do embed
 */
var city_sel, form;

jQuery(function() {
  
  form = jQuery('fieldset.sign').parent('form');

  //desabilita telefone
  form.find('input[name=sms]').click(function () {
    if (this.checked) {
      jQuery('#sign-phone').removeAttr('disabled');
    } else {
      jQuery('#sign-phone').attr('disabled', 'disabled');
    }
  });

  //popula cidade
  form.find('select[name=state]').change(function () {
    populateCity(form);
  });

  //popula cidade se já foi selecionada
  if (city_sel != '') {
    populateCity(form);
  }

  //mostra captcha no submit
  form.find('#sign-submit').click(function () {
    if (!validateSign(form)) {
      window.alert('Ops! Você esqueceu de preencher alguns campos no formulário de assinatura.');
      return false;
    }

    jQuery('#captcha').fadeIn();
    return true;
  });

  //submeter form
  form.submit(function () {
    jQuery('#captcha').fadeOut('fast', function () {
      return true;
    });
  })

});

function validateSign(form) {
  //nome, rg, cidade e estado não deve ser brancos
  form.find('.invalid').removeClass('invalid');
  form.find('.required').each(function () {
    var i = jQuery(this);
    if (i.find(':input').val().match(/^\s*$/)) {
      i.addClass('invalid');
    }
  });
  
  return form.find('.invalid').size() == 0;
  
}

function populateCity(form) {
  var city = form.find('select[name=city]');
  var state = form.find('select[name=state]');
  
  city.empty();
  city.append('<option value="">Carregando...</option>');
  jQuery.get('http://www.euvotodistrital.org.br/?ajax&action=get_cities&state=' + state.val(), function (data) {
    city.empty();
    jQuery.each(data, function (key, value) {
      var selected = city_sel && city_sel == value ? 'selected="selected"' : '';
      city.append('<option value="' + value + '" ' + selected + '>' + value + '</option>');
    })
  });
}