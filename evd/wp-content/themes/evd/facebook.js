var facebook = (function ($) {
  var apiKey = '169761096417898',
      message = '#MobilizeJá 3 amigos para o #EuVotoDistrital http://www.EuVotoDistrital.org.br',
      link = 'http://euvotodistrital.org.br';


  //initialize facebook.
  function init() {
    FB.init({'apiKey': apiKey});
  }

  //open facebook's dialog box.
  function post() {
      init();

      FB.ui(
       {
         'method': 'feed',
         'message': message,
         'link': link
       },
       function(response) {
         if (response && response.post_id) {
           alert('Obrigado por compartilhar!');
         } else {
           alert('Não foi possível publicar, tente novamente.');
         }
       }
     );
     return false;
    }
  
  return {
    attachEvent: function(elm) {
      $('body').delegate(elm, 'click', post);
    }
  }
}(jQuery));