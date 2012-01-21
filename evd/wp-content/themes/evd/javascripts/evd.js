
var evd = {

  setup : function () {
    evd.css.bindHacks();
    evd.why.bindClick();
    evd.links.bindExternal();
    evd.links.bindPrint();
    evd.font.bindClick();
    evd.content.bindDefList();
    evd.social.bindFeed();
    evd.invite.bindClick();
    evd.menu.bindHover();
    evd.blog.bindSendMail();
    evd.tools.copyCode();
    evd.multimedia.openMedia();
    evd.multimedia.closeMedia();
    evd.legislators.bindLoadNames();
    evd.legislators.validate();
  },

  css : {

    bindHacks : function () {
      if (jQuery.browser.mozilla) {
        jQuery('.box h3 span').css({
          'padding-bottom' : '0px'
        });
        jQuery('.box ol.why-slider li > span span').css({
          'line-height' : '1.33em'
        });
        jQuery('aside.boxes .mobilize a').css({
          'letter-spacing' : '-.05em'
        });
        jQuery('.box a.more-highlight').css({
          'letter-spacing' : '-.05em'
        });
      }
      if (jQuery.browser.msie && jQuery.browser.version < 8) {
        jQuery('.addthis_toolbox').css({
          'height' : '20px'
        });
        jQuery('footer p a.cc').css({
          'display' : 'inline',
          'float' : 'left',
          'margin-right' : '5px'
        });
      }
    }

  },

  why : {

    bindClick : function () {
      var itens = jQuery('aside .box ol.why-slider li');
      var links = itens.children('a');
      var to;
      links.click(function (event) {
        event.preventDefault();
        if (to) {
          window.clearTimeout(to);
        }
        var li = jQuery(this).parent();
        li.siblings().removeClass('active').children('span').fadeOut(function () {
          li.children('span').fadeIn();
        });
        li.addClass('active');
        to = window.setTimeout(function () {
          var next = li.next();
          if (next.size() == 0) {
            next = itens.first();
          }
          next.children('a').click();
        }, 10000);
      });
      links.first().click();
    }

  },

  links : {

    bindExternal : function () {
      jQuery('a[rel=external]').attr('target', '_blank');
    },

    bindPrint : function () {
      jQuery('a[href$="#print"]').click(function (event) {
        event.preventDefault();
        window.print();
      });
    }

  },

  font : {

    minSize : 12,

    maxSize : 16,

    bindClick : function () {
      jQuery('article nav.utils li.font-increase a').click(function (event) {
        event.preventDefault();
        evd.font.increaseSize();
      });
      jQuery('article nav.utils li.font-decrease a').click(function (event) {
        event.preventDefault();
        evd.font.decreaseSize();
      });
    },

    increaseSize : function () {
      var size = jQuery('article .content').css('font-size').replace(/[^0-9]/ig, '');
      size = parseInt(size, 10);
      if (size < evd.font.maxSize) {
        size += 2;
      }
      jQuery('article .content').css('font-size', size + 'px');
    },

    decreaseSize : function () {
      var size = jQuery('article .content').css('font-size').replace(/[^0-9]/ig, '');
      size = parseInt(size, 10);
      if (size > evd.font.minSize) {
        size -= 2;
      }
      jQuery('article .content').css('font-size', size + 'px');
    }

  },

  content : {

    bindDefList : function () {
      jQuery('article .content dl dd').each(function () {
        var dd = jQuery(this);
        dd.css({
          'height'   : '0',
          'overflow' : 'hidden'
        });
        dd.prev('dt').click(function () {
          var dt = jQuery(this);
          dt.parent().find('dd').css('height', '0');
          dd.css('height', 'auto');
        }).css('cursor', 'pointer');
      });

      //checa se alguma âncora é passada e mostra o conteudo de acordo
      var anchor = window.location.hash;
      anchor = anchor.substr(1);

      if (anchor != undefined || anchor != '') {
        jQuery('.content').find('dt').children('a[name=' + anchor + ']').parent().next('dd').css('height', 'auto');
      }
    }

  },

  form : {

    validate : function (form) {
      if (!(form instanceof jQuery)) {
        form = jQuery(form);
      }
      form.find('.invalid').removeClass('invalid');
      form.find('.required').each(function () {
        var i = jQuery(this);
        if (i.find(':input').val().match(/^\s*$/)) {
          i.addClass('invalid');
        }
      });
      return form.find('.invalid').size() == 0;
    }

  },

  social : {

    bindFeed : function () {
      var itens = jQuery('.boxes .social > ul li');
      itens.find('a').click(function (event) {
        event.preventDefault();
        var id = this.href.replace(/^.*#([a-z\-]+)$/i, '$1');
        itens.removeClass('active');
        jQuery(this).parent().addClass('active');
        jQuery('.boxes .social .updates').removeClass('active');
        jQuery('.boxes .social #' + id).addClass('active');
      });
      itens.first().children('a').click();
    }

  },

  invite: {
    bindClick: function() {
      jQuery('.friends').find('.select-all').click(function(event) {
        event.preventDefault();

        jQuery('.friends').find('input:checkbox').attr('checked', 'checked');
      });

      jQuery('.friends').find('.unselect-all').click(function(event) {
        event.preventDefault();

        jQuery('.friends').find('input:checkbox').attr('checked', '');
      });
    },

    validate: function(name, email, friends) {
      var invalid = [], pattern;
      
      if ($.trim(name.val()) == '') {
        invalid.push('nome');
      }

      //checks if email is valid
      pattern =  /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
      if ($.trim(email.val()) == '' || !pattern.test(email.val())) {
        invalid.push('e-mail');
      }

      if (friends.length < 1) {
        invalid.push('amigos');
      }

      if (invalid.length > 0) {
        alert('Ops! Alguns campos estão em branco ou são inválidos.');
        return false;
      }

      return true;
    }
  },

  menu: {
    bindHover: function() {
      jQuery('nav.primary').find('.fstlevel').hover(function() {
        //transparency to all menu items to highlight the hovered
        jQuery('nav.primary').find('.fstlevel').not('.active').fadeTo(0, 0.5);
        jQuery(this).fadeTo(0, 1);

        //hide previous submenu
        jQuery('nav.primary li ul').hide();

        //show submenu of hover
        jQuery(this).find('ul').show();
      })
    }
  },

  blog: {
    bindSendMail: function() {
      var subject;
      
      if (jQuery('#email-modal').length > 0) {
        //click para mostrar modal
        jQuery('li.email').find('a').click(function(event) {
          event.preventDefault();

          //pega o permalink e o titulo do post
          var permalink = $(this).attr('href');
          var title = $(this).attr('rel');
          
          jQuery('#email-modal').css({
            'top': ($(document).scrollTop() + $(window).height()/2 - 150) + 'px',
            'left': '50%',
            'marginLeft': '-100px'
          }).show();

          subject = 'Encontrei um artigo que pode te interessar no #EuVotoDistrital: ' + title;

          //bind url do gmail e hotmail
          jQuery('#post-gmail').attr('href', 'https://mail.google.com/mail/?view=cm&fs=1&to&su=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(permalink) + '&ui=2&tf=1&shva=1');
          jQuery('#post-hotmail').attr('href', 'http://co104w.col104.mail.live.com/default.aspx?rru=compose%3fsubject%3d' + encodeURIComponent(subject) + '%26body%3d' + encodeURIComponent(permalink));

          //bind evento para fechar o modal
          jQuery('#email-modal').find('li.btn-close').find('a').click(function(event) {
            event.preventDefault();

            jQuery('#email-modal').hide();

            //limpa os campos
            jQuery('#email-modal').find('input').val('');
            jQuery('#email-modal').find('textarea').val('');
          })

          //bind evento para mandar email
          jQuery('#email-modal').find('button').click(function(event) {
            var from = jQuery('#email-modal').find('input[name="post-from"]').val();
            var to = jQuery('#email-modal').find('input[name="post-to"]').val();

            //checa se o from está vazio, alerta e restorna
            if (jQuery.trim(from) == '' || jQuery.trim(to) == '') {
              alert('Ops! Alguns campos estão em branco ou são inválidos.');
              return;
            }

            jQuery.ajax({
              url: 'http://www.euvotodistrital.org.br/?ajax&action=send_post_email',
              type: 'POST',
              data: {
                'post-to': to,
                'post-from': from,
                'post-message': $('#email-modal').find('textarea').val(),
                'post-title': title,
                'post-url': permalink
              },
              success: function(data) {
                if (data.success == 1) {
                  alert('E-mails enviados com sucesso.')

                  //esconde modal
                  jQuery('#email-modal').hide();

                  //limpa campos
                  jQuery('#email-modal').find('input').val('');
                  jQuery('#email-modal').find('textarea').val('');
                } else {
                  alert('Ops! Ocorreu um erro no envio de emails, por favor, tente de novo.');
                }
              },
              error: function() {
                alert('Ops! Ocorreu um erro no envio de emails, por favor, tente de novo.');
              }
            });
          })
        })
      }
    }
  },

  tools: {
    copyCode: function() {
      jQuery('#material').find('textarea').focus(function() {
        var input = jQuery(this);

        //setTimeut é usado por causa de um bug no select no webkit
        setTimeout(function() {
            input.select();
        },10);
      })
    }
  },

  multimedia: {
    openMedia: function() {
      jQuery('#multimedia').delegate('.media', 'click', function(event) {
        event.preventDefault();
        
        var modal = jQuery('#multimedia-modal'), wrapper;
        var img = $(this).find('.thumb').find('img');

        //clear title and description
        modal.find('h2').empty();
        modal.find('p').empty();
        modal.find('div').empty();

        modal.find('h2').html(img.attr('title'));
        modal.find('p').html(img.attr('rel'));

        if (img.parent().hasClass('video')) {
          modal.find('div').html('<iframe width="425" height="349" src="http://www.youtube.com/embed/' + img.parent().attr('rel') + '" frameborder="0" allowfullscreen></iframe>');
        } else {
          var photos = img.parent().next('input').val();
          photos = photos.split(',');

          content = modal.find('div');

          for (var i = 0; i < photos.length; i++) {
            wrapper = $('<div />').addClass('facebook-wrapper').css({
              'backgroundImage': 'url(' + photos[i] + ')',
              'backgroundRepeat': 'no-repeat',
              'backgroundPosition': 'center center'
            });

            content.append(wrapper);
          }
        }

        modal.css({
            'top': ($(document).scrollTop() + $(window).height()/2 - 250) + 'px',
            'left': '50%',
            'marginLeft': '-215px'
          }).show();
      });
    },

    closeMedia: function() {
      jQuery('#multimedia-modal').find('.bt-close').click(function(event) {
        event.preventDefault();
        
        jQuery('#multimedia-modal').hide();
      })
    }
  },
  legislators: {
    bindLoadNames: function() {
      jQuery('#legislator-state').change(function(event) {
        var state = $(this).val();
        var legislator = jQuery('#legislator-id');

        legislator.empty();
        legislator.append('<option value="">Carregando...</option>');
        
        jQuery.ajax({
          url: 'http://www.euvotodistrital.org.br/?ajax&action=get_legislators&state=' + state,
          type: 'GET',
          success: function(data) {
            legislator.empty();

            jQuery.each(data, function (key, value) {
              legislator.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
            
          },
          error: function() {
            alert('Ops! Ocorreu um erro ao carregar os nomes dos parlamentares.');
          }
        });
      })
    },
    validate: function() {
      var invalids;
      
      jQuery('#legislators-form').submit(function(event) {
        //empty invalids
        invalids = [];
        
        //check name
        if (jQuery.trim(jQuery('input[name="name1"]').val()) === '') {
          invalids.push('nome')
        };

        //check email
        pattern =  /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (jQuery.trim(jQuery('input[name="email"]').val()) == '' || !pattern.test(jQuery('input[name="email"]').val())) {
          invalids.push('e-mail');
        }

        //check if legislator was selected
        if (jQuery('#legislator-id').val() == '') {
          invalids.push('parlamentar');
        }

        if (invalids.length > 0) {
          event.preventDefault();

          alert('Ops! Alguns campos estão em branco ou são inválidos.');
        }
      })
    }
  }
};

jQuery(document).ready(function () {
  evd.setup();
});
