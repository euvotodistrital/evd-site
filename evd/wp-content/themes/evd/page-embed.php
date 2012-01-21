<!Doctype html>
<html>
  <head>
    <title><?php wp_title('- ', true, 'right'); bloginfo('name'); ?></title>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="author" content="Jogo12" />
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/stylesheets/embed.css" media="all" />
    <link rel="shortcut icon" type="image/png" href="<?php echo home_url('favicon.png'); ?>" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascripts/embed.js" charset="utf-8"></script>
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-22305241-1']);
    _gaq.push(['_trackPageview']);
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
  </head>
  <body>
    <div id="embed">
      <h1><a href="<?php echo home_url(); ?>">#EuVotoDistrital</a></h1>
      <h2>Mais poder ao cidadão</h2>
      <h3>Assine a <span>petição</span> a favor do Voto Distrital para combater a corrupção e mudar a política brasileira!</h3>

      <div class="votes">
        <?php
          $goal = (int) get_option('evd_petition_goal');
          $total = evd_petition_count(false);

          $width = ($goal !== 0) ? ceil($total / $goal * 100) : 0;
        ?>
        <h4><span style="width:<?php echo $width; ?>%"><?php echo number_format($total, 0, '', '.'); ?></span></h4>
      </div>
      <p>já assinaram. Precisamos de <?php echo number_format($goal, 0, '', '.'); ?>! Topa o desafio?</p>

      <form action="<?php the_permalink(); ?>" method="post">
        <?php
          $invalids = array();
          $result = (int) evd_sign_form_control(true);

          if ($result == 0 && isset($_SESSION['evd_sign'])) {
            extract($_SESSION['evd_sign']);
          }

          if ($result === 1) {
          ?>
            <script type="text/javascript">
              alert('Assinatura inserida com sucesso.');
            </script>
          <?php
          }
        ?>
        
        <fieldset class="sign">
          <ol>
            <li class="required <?php if (in_array('name', $invalids)) : ?>invalid<?php endif; ?>">
              <label for="sign-name">Nome:</label>
              <input type="text" class="text" id="sign-name" name="name_" value="<?php if (!empty($sign)) echo $sign->name; ?>" />
            </li>
            <li class="<?php if (in_array('email', $invalids)) : ?>invalid<?php endif; ?>">
              <label for="sign-email">E-mail:</label>
              <input type="text" class="text" id="sign-email" name="email" value="<?php if (!empty($sign)) echo $sign->email; ?>" />
            </li>
            <li class="required <?php if (in_array('identity', $invalids)) : ?>invalid<?php endif; ?>">
              <label for="sign-identity">RG:</label>
              <input type="text" class="text" id="sign-identity" name="identity" value="<?php if (!empty($sign)) echo $sign->identity; ?>" />
            </li>
            <li class="state required <?php if (in_array('state', $invalids)) : ?>invalid<?php endif; ?>">
              <label for="sign-state">Estado:</label>
              <select id="sign-state" name="state">
                <option value="">--</option>
                <?php foreach (evd_states() as $state) : ?>
                  <option value="<?php echo $state; ?>" <?php if (!empty($sign) && $sign->state == $state) : ?>selected="selected"<?php endif; ?>><?php echo $state; ?></option>
                <?php endforeach; ?>
              </select>
            </li>
            <li class="city required <?php if (in_array('city', $invalids)) : ?>invalid<?php endif; ?>">
              <label for="sign-city">Cidade:</label>
              <select id="sign-city" name="city">
                <option value="">--</option>
              </select>
            </li>
            <li class="newsletter <?php if (in_array('newsletter', $invalids)) : ?>invalid<?php endif; ?>">
              <label><input type="checkbox" class="checkbox" name="newsletter" value="1" <?php if (empty($sign) || $sign->newsletter) : ?>checked="checked"<?php endif; ?> /> Quero receber novidades</label>
            </li>
            <li class="volunteer <?php if (in_array('volunteer', $invalids)) : ?>invalid<?php endif; ?>">
              <label><input type="checkbox" class="checkbox" name="volunteer" value="1" <?php if (!empty($sign) && $sign->volunteer) : ?>checked="checked"<?php endif; ?> /> Desejo ser voluntário</label>
            </li>
            <li class="sms <?php if (in_array('sms', $invalids)) : ?>invalid<?php endif; ?>">
              <label><input type="checkbox" class="checkbox" name="sms" value="1" <?php if (!empty($sign) && $sign->sms) : ?>checked="checked"<?php endif; ?> /> Quero receber SMS sobre o movimento</label>
            </li>
            <li class="<?php if (in_array('phone', $invalids)) : ?>invalid<?php endif; ?>">
              <label for="sign-phone">Celular:</label>
              <input type="text" class="text" id="sign-phone" name="phone" value="<?php if (!empty($sign)) echo $sign->phone; ?>" <?php if (empty($sign) || !$sign->sms) : ?>disabled="disabled"<?php endif; ?> />
            </li>
            <li class="actions">
              <?php if (in_array('captcha', $invalids)) : ?>
                <p class="invalid">O desafio das palavras (captcha) não foi preenchido corretamente.</p>
              <?php endif; ?>
              <button id="sign-submit" type="button">Assinar</button>
              <a href="<?php echo home_url('assine'); ?>" class="more">Saiba mais</a>
            </li>
            <li class="privacy">
              <p>Política de <a href="<?php echo home_url('termos-de-privacidade'); ?>">privacidade</a>.</p>
            </li>
          </ol>

          <div id="captcha">
            <div class="inner">
              <?php
                require_once 'recaptchalib.php';
                echo recaptcha_get_html(EVD_RECAPTCHA_PUBLIC);
              ?>
              <ol>
                <li class="actions">
                  <button type="submit">Assinar</button>
                </li>
              </ol>
            </div>
          </div>

        </fieldset>
      </form>
    </div>
    <script type="text/javascript">
      var city_sel = '';
      //seta estado e cidade se selecionados
      <?php if (!empty($sign) && $sign->state && $sign->city) { ?>
        city_sel = '<?php echo $sign->city; ?>';
      <?php } ?>

      //mostra mensagem de erro se post inválido
      <?php if (!empty($invalids)) : ?>
        jQuery(document).ready(function () {
          window.alert('Ops! Você esqueceu de preencher alguns campos no formulário de assinatura.');
        });
      <?php endif; ?>

    </script>
  </body>
</html>