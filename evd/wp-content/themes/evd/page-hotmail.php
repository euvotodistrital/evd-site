<?php
//adicionando biblioteca do hotmail
require_once get_template_directory() . '/../../../libraries/hotmail/windowslivelogin.php';

$wll = evd_get_wll();

//checar se a ação é de autenticação e salvar o token em um cookie
$action = $_REQUEST['action'];

if ($action == 'delauth') {
  $consent = $wll->processConsent($_REQUEST);

/*
 *  Seta o cookie['webauthtoken'] com o token retornado da autenticação com o live. Isso tem que ser
 * a primeira coisa feita na página, por isso o get_header() vem depois.
 */
  if ($consent) {
    evd_hotmail_set_cookie($consent->getToken());
  } else {
    evd_hotmail_set_cookie();
  }
}
?>

<?php get_header(); ?>
<div class="wrap">
  <article>

    <?php
    $authCookie = evd_hotmail_get_cookie();

    //O cookie vem com valor null quando acaba e ser setado
    if (!$authCookie && $consent) {
      $authCookie = $consent->getToken();
    }

    if (!$authCookie) {
      evd_show_auth_links($wll);
    } else {
      //checa se emails foram enviados
      if ($action != 'delauth' && ($sent = evd_invite_form_control()) !== null) { ?>
        <script type="text/javascript">
          <?php if ($sent) { ?>
            var msg = 'Seus convites foram enviados com sucesso. Obrigado!';
          <?php } else { ?>
            var msg = 'Ops! Algo estranho aconteceu enquanto enviávamos seus emails. Por favor, tente novamente.';
          <?php } ?>
          window.alert(msg);

          //redireciona para a página de convide
        window.location = 'http://euvotodistrital.org.br/assine/assinar';
        </script>
      <?php
      }

      //check token
      $token = $wll->processConsentToken($authCookie);

      if ($token && !$token->isValid()) {
        evd_show_auth_links($wll);

        $token = null;
      }

      if ($token && $token != null) {
        $xml = evd_get_hotmail_contacts($token);

        $friends = evd_parse_hotmail_xml($xml);
        ?>
        <h1>Convide seus amigos do Hotmail</h1>
        <div class="content">
          <form action="<?php the_permalink(); ?>" method="post">
            <fieldset class="invite">
              <ol>
                <li class="friends">
                  <p>Selecione seus amigos:</p>
                  <div>
                    <?php $i = 0; foreach ($friends as $friend) { ?>
                    <p>
                      <input type="checkbox" name="invite-friends[<?php echo $i; ?>]" value="<?php echo $friend; ?>" />
                      <label for="invite-friends[<?php echo $i; ?>]">&lt;<?php echo $friend; ?>&gt;</label>
                    </p>
                    <?php $i++; } ?>
                  </div>
                  <a href="" class="select-all">selecionar todos</a> |
                  <a href="" class="unselect-all">desselecionar todos</a>
                </li>
                <li>
                  <label for="invite-name">Seu Nome:</label>
                  <input type="text" class="text" id="invite-name" name="invite-name" />
                </li>
                <li>
                  <label for="invite-email">Seu E-mail:</label>
                  <input type="text" class="text" id="invite-email" name="invite-email" />
                </li>
                <li class="actions">
                  <button type="submit" id="bt-invite">Convide</button>
                </li>
              </ol>
            </fieldset>
          </form>
        </div>
      <?php
      }
    }
  ?>
  </article>
</div>
<script type="text/javascript">
  jQuery(function() {
    jQuery('#bt-invite').click(function(event) {
      if (!evd.invite.validate(jQuery('#invite-name'), jQuery('#invite-email'), jQuery('.friends input:checked'))) {
        event.preventDefault();
      }
    })
  })
</script>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
