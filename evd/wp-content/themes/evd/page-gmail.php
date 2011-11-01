<?php get_header(); ?>
<div class="wrap">
  <article>
    <?php if (($sent = evd_invite_form_control()) !== null) : ?>
      <script type="text/javascript">
        <?php if ($sent) : ?>
          var msg = 'Seus convites foram enviados com sucesso. Obrigado!';
        <?php else : ?>
          var msg = 'Ops! Algo estranho aconteceu enquanto enviávamos seus emails. Por favor, tente novamente.';
        <?php endif; ?>
        window.alert(msg);

        //redireciona para a página de convide
        window.location = 'http://euvotodistrital.org.br/assine/assinar';
      </script>
    <?php endif; ?>

    <?php
    if (!isset($_GET['token'])) {
      //adicionando biblioteca do hotmail
      require_once get_template_directory() . '/../../../libraries/hotmail/windowslivelogin.php';

      $wll = evd_get_wll();

      evd_show_auth_links($wll);
      
    } else {
      $sessiontoken = urldecode($_GET['token']);
      $result = evd_gmail_call("http://www.google.com/m8/feeds/contacts/default/base?alt=json&max-results=500", $sessiontoken);

      $contacts = json_decode($result, true);
      $contacts = $contacts['feed']['entry'];

      $friends = array();

      if (is_array($contacts)) {
      foreach ($contacts as $contact) {
	if (!empty($contact['gd$email'][0]['address'])) {
          $friends[] = $contact['gd$email'][0]['address'];
	}
      }}
      ?>
      <h1>Convide seus amigos do Gmail</h1>
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
    <?php } ?>
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
