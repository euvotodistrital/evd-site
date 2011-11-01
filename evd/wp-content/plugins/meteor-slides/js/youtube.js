//http://euvotodistrital.org.br

/**
 * Para receber informações do player de video e pausar o slideshow qdo alguem está assistindo um video
 * Essa função é chamada pela api do youtube qdo ela termina de carregar.
 */
function onYouTubePlayerAPIReady() {
  var player = [], holder;

  var eventsCallbacks = {
    'onStateChange': onPlayerStateChange
  }

  jQuery(function() {
    //popula todos os videos criando um Player
    jQuery('.slide-video').each(function(i) {
      holder = jQuery(this).parent().attr('id');

      player[i] = new YT.Player(holder, {
        width: '672',
        height: '408',
        playerVars: {'wmode': 'transparent'},
        videoId: jQuery(this).attr('rel'),
        events: eventsCallbacks
      });
    });
  })
}

/**
 * Checa as mudanças de estado do player
 */
function onPlayerStateChange(evt) {
  if (evt.data == YT.PlayerState.PLAYING || evt.data == YT.PlayerState.BUFFERING) {
    //var defined in jqeury.cycle.all para indicar que o player está tocando
    playerOn = true;
    
    //player está tocando, pausa o slideshow
    jQuery('.slides').cycle('pause');
  } else if (evt.data == YT.PlayerState.ENDED || evt.data == YT.PlayerState.PAUSED) {
    //player parou, dá um resume no slideshow
		playerOn = false;
    jQuery('.slides').cycle('resume', true);
  }
}