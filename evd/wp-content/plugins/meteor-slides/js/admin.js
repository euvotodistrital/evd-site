//http://euvotodistrital.org.br

jQuery(function() {
  //esconde os boxes de imagem e video

  jQuery('#postimagediv, #postvideodiv').hide();

  if (jQuery('#opt-vid').attr('checked') == true) {
    activateVideo();
  }

  if (jQuery('#opt-img').attr('checked') == true) {
    activateImage();
  }

  jQuery('#slides-options').delegate('input', 'click', function() {
    if (jQuery(this).attr('id') === 'opt-vid') {
      activateVideo();

    } else if (jQuery(this).attr('id') === 'opt-img') {
      activateImage();
    }
  })
});

function activateVideo() {
  //se opt-video estiver selecionado, mostra box de video e desabilita a url
      jQuery('#postimagediv').hide();
      jQuery('#postvideodiv').show();

      jQuery('#slide-url-value').attr('disabled', 'disabled');
}

function activateImage() {
  //se opt-img estiver selecionado, mostra box de imagem e habilita a url
      jQuery('#postvideodiv').hide();
      jQuery('#postimagediv').show();

      jQuery('#slide-url-value').removeAttr('disabled');
}