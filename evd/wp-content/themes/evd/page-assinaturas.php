<?php get_header(); ?>
<?php
  //variável 'states' no javascript para armazenar os estados para os forms futuros
  evd_states_to_javascript();

  //checar se postado
  $result = evd_multiple_form_control();
  
  if ($result === -1) {
  ?>
    <script type="text/javascript">
      alert('O captcha não foi preenchido corretamente. Tente novamente.');
    </script>
  <?php } else if (is_array($result)) { ?>
    <p class="error">Algumas assinaturas não foram enviadas:</p>
    <ul class="error-messages">
    <?php
    foreach($result as $message) {
      echo '<li>' . $message . '</li>';
    }
    ?>
    </ul>
  <?php } else if ($result) { ?>
    <script type="text/javascript">
      alert('Assinaturas enviadas com sucesso.');
    </script>
    <?php } ?>
<div class="wrap">
  <article>
    <h2>Adicionar assinaturas</h2>
    <form id="multiple-sign" action="<?php echo get_permalink(); ?>" method="post">
      <fieldset class="sign">
        <ol>
          <li class="required">
            <label for="sign-name">Nome:</label>
            <input type="text" class="text" id="sign-name" name="sign-name-1" value="" />
          </li>
          <li>
            <label for="sign-email">E-mail:</label>
            <input type="text" class="text email" id="sign-email" name="sign-email-1" value="" />
          </li>
          <li class="required">
            <label for="sign-identity">RG:</label>
            <input type="text" class="text" id="sign-identity" name="sign-rg-1" value="" />
          </li>
          <li class="state required">
            <label for="sign-state">Estado:</label>
            <select id="sign-state" name="sign-state-1">
              <option value="">--</option>
              <?php foreach (evd_states() as $state) : ?>
                <option value="<?php echo $state; ?>"><?php echo $state; ?></option>
              <?php endforeach; ?>
            </select>
          </li>
          <li class="city required">
            <label for="sign-city">Cidade:</label>
            <select id="sign-city" name="sign-city-1">
              <option value="">--</option>
            </select>
          </li>
          <li class="newsletter">
            <input type="checkbox" checked="checked" name="sign-newsletter-1" value="1" /><label>Quero receber novidades sobre o movimento</label>
          </li>
          <li class="volunteer">
            <input type="checkbox" name="sign-volunteer-1" value="1" /><label>Desejo ser voluntário</label>
          </li>
          <li class="sms">
            <input type="checkbox" name="sign-sms-1" value="1" /><label>Quero receber SMS sobre o movimento</label>
          </li>
          <li>
            <label for="sign-phone">Celular:</label>
            <input type="text" class="text" id="sign-phone" disabled="disabled" name="sign-phone-1" value="" />
          </li>
        </ol>
        <input name="total-form" id="total-form" type="hidden" value="1" />
        <a href="#" class="button small" id="bt-add">[+] Adicionar mais uma assinatura</a>
      </fieldset>
      <button id="sign-submit" type="button">Salvar</button>
      <div id="captcha">
        <div class="inner">
          <?php
            require_once 'recaptchalib.php';
            echo recaptcha_get_html(EVD_RECAPTCHA_PUBLIC);
          ?>
          <ol>
            <li class="actions">
              <button type="button" id="bt-sign">Assinar</button>
            </li>
          </ol>
        </div>
      </div>
    </form>
    <script type="text/javascript">
      jQuery(function() {
        multiplesign.setup();
      })
    </script>
  </article>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>