var multiplesign = (function ($) {
  var current = 1,
      holder,
      url = 'http://www.euvotodistrital.org.br/',
      i;
      
  /**
   * Seta o li com o conteudo - label + input
   *
   * @param mixed - elementos que serão apendados no li, na ordem correta
   * @return jQuery li
   */
  function setLi() {
    //check if required
    var li = $('<li></li>');

    //appenda conteudo na ordem do array de parametro
    for (i = 0; i < arguments.length; i++) {
      li.append(arguments[i]);
    }

    return li;
  };

  /**
   * Cria um elemento de label
   *
   * @param string name - nome usado no attributo for do label
   */
  function createLabel(value, name) {
    return $('<label for="' + name + '">' + value + '</label>');
  }

  /**
   * Cria um elemento de input:text
   *
   * @param string name - nome usado no attributo id e name do campo
   */
  function createInputText(name) {
    return $('<input type="text" id="' + name + '" name="' + name + '" />').addClass('text');

    return input;
  }
  
  /**
   * Cria um elemento de select com um campo vazio
   *
   * @param string name - nome usado no attributo id e name do campo
   */
  function createSelect(name) {
    var option = $('<option></option>').val('').html('--');
    
    return $('<select id="' + name + '" name="' + name + '"></select>').append(option);
  }

  /**
   * Popula os estados a partir da var global states, se setada
   * Seleciona o mesmo estado que foi escolhido no primeiro formulário
   *
   * @param jQuery select - elemento a ser populado.
   */
  function populateStates(select) {
    if (states !== null || states !== undefined && typeof states === 'object') {
      //Pega o estado selecionado no primeiro formulário
      selectedState = $('#sign-state').val();
 
      for (i = 0; i < states.length; i++) {
        if (states[i] == selectedState) {
          select.append($('<option></option>').val(states[i]).html(states[i]).attr('selected', 'selected'));
        } else {
          select.append($('<option></option>').val(states[i]).html(states[i]));
        }
      }
    }
  }
  
  /**
   * Cria um elemento de checkbox com valor default 1
   *
   * @param string name - nome usado no attributo id e name do campo
   */
  function createCheckbox(name) {
    return $('<input type="checkbox" id="' + name + '" name="' + name + '" />').val(1);
  }

  /**
   * Adiciona nome no form
   */
  function addName() {
    var name, li;

    //nome usado para ids e names de input
    name = 'sign-name-' + current;

    //get li e seta as classes necessárias
    li = setLi(createLabel('Nome:', name), createInputText(name)).addClass('required').addClass('new-form');

    holder.append(li);
  }

  /**
   * Adiciona email no form
   */
  function addEmail() {
    var name, li;

    //nome usado para ids e names de input
    name = 'sign-email-' + current;

    //get li e seta as classes necessárias
    li = setLi(createLabel('E-mail:', name), createInputText(name).addClass('email'));

    holder.append(li);
  }

  /**
   * Adiciona RG no form
   */
  function addRg() {
    var name, li;
    
    //nome usado para ids e names de input
    name = 'sign-rg-' + current;

    //get li e seta as classes necessárias
    li = setLi(createLabel('R.G:', name), createInputText(name)).addClass('required');

    holder.append(li);
  }

  /**
   * Adiciona estado no form
   *
   * @TODO: Popular os estados, adicionar behaviour para popular cidades no onchange
   */
  function addState() {
    var name, li, select;
    
    name = 'sign-state-' + current;

    select = createSelect(name);
    populateStates(select);

    li = setLi(createLabel('Estado:', name), select).addClass('required').addClass('state');

    holder.append(li);
  }

  /**
   * Adiciona cidade no form
   */
  function addCity() {
    var name, li, select;

    name = 'sign-city-' + current;

    select = createSelect(name);

    //checa se estado está selecionado para popular a cidade
    selectedState = $('sign-state-' + current);

    if (selectedState.val() != '') {
      //popula cidades
      select.html($('#sign-city').html());

      //seleciona cidade
      selectedCity = $('#sign-city').val();
      select.find('option[value="' + selectedCity + '"]').attr('selected', 'selected');
    }

    li = setLi(createLabel('Cidade:', name), select).addClass('required').addClass('city');

    holder.append(li);
  }

  /**
   * Adiciona Voluntário no form
   */
  function addVolunteer() {
    var name, li;

    name = 'sign-volunteer-' + current;

    li = setLi(createCheckbox(name), createLabel('Desejo ser voluntário', name)).addClass('volunteer');

    holder.append(li);
  }

  /**
   * Adiciona Newsletter no form
   */
  function addNewsletter() {
    var name, li, chkbox;

    name = 'sign-newsletter-' + current;

    chkbox = createCheckbox(name).attr('checked', 'checked');

    li = setLi(chkbox, createLabel('Quero receber novidades sobre o movimento', name)).addClass('newsletter');

    holder.append(li);
  }

  /**
   * Adiciona SMS no form
   */
  function addSms() {
    var name, li;

    name = 'sign-sms-' + current;

    li = setLi(createCheckbox(name), createLabel('Quero receber SMS sobre o movimento', name)).addClass('sms');

    holder.append(li);
  }

  /**
   * Adiciona celular no form
   */
  function addPhone() {
    var name, li;

    //nome usado para ids e names de input
    name = 'sign-phone-' + current;

    //get li e seta as classes necessárias
    li = setLi(createLabel('Celular:', name), createInputText(name).attr('disabled', 'disabled'));

    holder.append(li);
  };

  /**
   * Adiciona os campos no formulário
   */
  function addFields() {
    addName();
    addEmail();
    addRg();
    addState();
    addCity();
    addNewsletter();
    addVolunteer();
    addSms();
    addPhone();
  }

  /**
   * valida se os campos não estáo vazios
   *
   * @param mixed value
   * @return boolean
   */
  function validateRequired(value) {
    if ($.trim(value) == "") {
      return false;
    }

    return true;
  }

  /**
   * valida se o email é válido
   *
   * @param string email
   * @return boolean
   */
  function validateEmail(email) {
    pattern =  /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!pattern.test(email)) {
      return false;
    }

    return true;
  }
  
  return {
    setup: function() {
      //clique para adicionar mais assinaturas
      $('#bt-add').click(function(event) {
        multiplesign.add();

        event.preventDefault();
      });

      //behavior para popular a cidade de acordo com o estado selecionado
      $('#multiple-sign').delegate('li.state select', 'change', function() {
        multiplesign.populateCities($(this));
      })

      //adiciona captcha
      $('#sign-submit').click(function () {

        //valida campos antes de mostrar o captcha
        if (multiplesign.validate()) {
          $('#captcha').fadeIn();
        }
      });

      //adiciona validação
      $('#bt-sign').click(function(event) {
        multiplesign.validateCaptcha(event);
      })

      //botão para habilitar celular
      $('#multiple-sign').delegate('.sms input', 'click', function(event) {
        if (this.checked) {
          $(this).parent().next().find('input').removeAttr('disabled');
        } else {
          $(this).parent().next().find('input').attr('disabled', 'disabled').val('');
        }
      });
    },
    add: function() {
      //incrementa o numero do current
      current++;

      //seta holder
      holder = $('.sign').children('ol');

      //adiciona campos
      addFields();

      //atualiza o contador de forms
      $('#total-form').val(current);
    },
    populateCities: function(select) {
      var city = select.parent().next().find('select');
      
      city.empty();
      city.append('<option value="">Carregando...</option>');
      
      $.get(url + '?ajax&action=get_cities&state=' + select.val(), function (data) {
        city.empty();
        $.each(data, function (key, value) {
          city.append('<option value="' + value + '">' + value + '</option>');
        })
      });
    },
    validate: function() {
      var invalids = [],
          flds,
          email;
          
      //validate inputs obrigatórios - nome e rg
      flds = $('li.required').find('input,select');

      flds.each(function(i) {
        if (!validateRequired($(this).val())) {
          invalids.push($(this));
        }
      });

      //validate e-mail válido
      flds = $('input.email');

      flds.each(function(i) {
        email = $(this).val();

        if (email != "") {
          if (!validateEmail(email)) {
            invalids.push($(this));
          }
        }
      })

      if (invalids.length > 0) {
        alert('Ops, alguns campos obrigatórios não foram preenchidos ou são inválidos.');

        return false;
        
      }

      return true;
    },
    validateCaptcha: function(event) {
      var challenge = $('#recaptcha_challenge_field').val(),
          response = $('#recaptcha_response_field').val();
          
      jQuery.get(url + '?ajax&action=validate_captcha&challenge=' + challenge + '&response=' + response, function (data) {
        if (data.valid === 'false') {
          alert('O captcha está incorreto, tente novamente.');
          event.preventDefault();
        } else {
          $('#captcha').hide();
          document.forms["multiple-sign"].submit();
        }
      });
    }
  }
}(jQuery));