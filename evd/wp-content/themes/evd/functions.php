<?php


define('EVD_MAIL_API_URL',      'http://216.70.69.79/api');

define('EVD_MAIL',              'contato@euvotodistrital.org.br');

define('EVD_FROM',              'noreply@euvotodistrital.org.br');



define('EVD_RECAPTCHA_PUBLIC',  '6LdCX8MSAAAAAECWuFdq5ITcrNJRDlJVR0WRDnkv');

define('EVD_RECAPTCHA_PRIVATE', '6LdCX8MSAAAAAL93Vgj-KEaeON0yWagF8TTNrqZ5');

//seta nome do cookie que vai ser usado na autenticação com o hotmail
define('EVD_HOTMAIL_COOKIE', 'webauthtoken');


// -- Setup



function evd_setup() {
  if (!session_id()) {

    session_start();

  }



  // Actions

  add_action('init', 'evd_post_type');

  add_action('init', 'evd_petition_setup');

  add_action('admin_menu', 'evd_update_menu');

  add_action('wp', 'evd_check_redirect');

  add_action('wp_dashboard_setup', 'evd_dashboard');

  add_action('init', 'register_custom_menu');

  remove_action('wp_head', 'wp_generator');



  if (is_admin() && !empty($_GET['page']) && $_GET['page'] == 'petition') {

    add_action('init', 'evd_petition_init');

  }

  if (isset($_GET['ajax'])) {
    add_action('init', 'evd_ajax_request');

  }


  // Filters

  add_filter('show_admin_bar', '__return_false');

  add_filter('admin_footer_text', 'evd_footer_admin');

  add_filter('body_class', 'evd_body_class');

  add_filter('pre_site_transient_update_core', create_function('$a', "return null;"));

  add_filter('the_excerpt', 'do_shortcode');



  // Shortcodes

  add_shortcode('social_share', 'evd_social_share');

  add_shortcode('social_share_count', 'evd_social_share_count');

  add_shortcode('contact_form', 'evd_contact_form');

  add_shortcode('hotmail_auth_url', 'evd_hotmail_url');

  add_shortcode('tools_intro', 'evd_tools_intro');



}



function evd_post_type() {

  add_post_type_support('page', 'excerpt');

}



evd_setup();



// -- Core

/**
 * Adiciona o custom menu para o tema
 */
function register_custom_menu() {
  register_nav_menu('custom_menu', __('Custom Menu'));
}

/**
 * Envia emails.
 *
 * @param string $to
 * @param string $from
 * @param string $subject
 * @param string $content
 *
 * @throws Exception quando não consegue mandar o email
 *
 * @return boolean
 */
function evd_mail($to, $subject, $content, $fromEmail = EVD_FROM, $fromName = '#euvotodistrital') {

  /*
   * ========================================
   * Método 1 : API de envio no Media Temple.
   * ========================================
   */

  $from  = $fromEmail;
  $name  = $fromName;
  $debug = 1; // 1 to debug

  $url = EVD_MAIL_API_URL . '/mail/send';
  $post_data = compact('to', 'subject', 'content', 'from', 'name', 'debug');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $result = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);

  $result = json_decode($result);

  return !empty($result) && $result->status == 'OK';

  /*
   * ====================================================================================
   * Método 2 : Chama a wp_mail para usarmos o SMTP do GMail com o plugin SMTP Configure.
   * ====================================================================================
   */

  /*
  try {
    $headers  = 'From: ' . $fromName . ' <' . $fromEmail. '>' . "\n";
    $headers .= "Content-type: text/html; charset=" . get_bloginfo('charset') . "\n";
    $headers .= "Return-Path: " . $fromEmail . '\n';
    return wp_mail($to, $subject, $content, $headers);
  } catch (Exception $e) {
    return false;
  }
  */

  /*
   * =============================
   * Método 3 : Escrito pela Dani.
   * =============================
   */

  /*
  $headers = 'From: ' . $fromName . ' <' . $fromEmail. '>' . "\n";
  $headers .= "Content-type: text/html; charset=" . get_bloginfo('charset') . "\n";

  try {
    //checa se for postfix
    if (!mail($to, $subject, $content, $headers , "-r" . $fromEmail)) {
      //Se "não for Postfix"
      $headers .= "Return-Path: " . $fromEmail . '\n';

      mail($to, $subject, $content, $headers);
    }

    return true;
    
  } catch(Exception $e) {
    return false;
  }
  */

}

function evd_ajax_request() {
  if (empty($_GET['action'])) {
    exit(0);
  }
  
  $json = null;

  switch ($_GET['action']) {
    case 'get_cities':
      $state = !empty($_GET['state']) ? substr(trim($_GET['state']), 0, 2) : null;
      $json = evd_cities($state);
      break;
    case 'get_legislators':
      $state = !empty($_GET['state']) ? substr(trim($_GET['state']), 0, 2) : null;
      $json = evd_legislators($state);
      break;
    case 'validate_captcha':
      $challenge = $_GET['challenge'];
      $response = $_GET['response'];

      if (evd_validate_captcha($challenge, $response)) {
        $json = array('valid'=> 'true');
      } else {
        $json = array('valid'=> 'false');
      }
      break;
    case 'send_post_email':
      $json = sendPostViaEmail();
      break;
  }
  header('Content-type: application/json; charset=utf-8');
  echo json_encode($json);
  exit(0);
}

/**
 * E-mail o post
 */
function sendPostViaEmail() {
  try {
    $to = $_POST['post-to'];
    $from = $_POST['post-from'];
    $message = $_POST['post-message'];
    $title = $_POST['post-title'];
    $permalink = $_POST['post-url'];

    $toArr = explode(',', $to);

    $subject = $from . ' encontrou um artigo que pode te interessar no #EuVotoDistrital: ' . $title;

    $content = "Olá,

{$from} sugeriu um artigo para você e te enviou uma mensagem:


<a href=\"{$permalink}\">{$title}</a>
({$permalink}).";

if (!empty($message)) {

$content .= "

\"{$message}\"";
}

$content .= "



#EuVotoDistrital

Mais poder ao cidadão.

http://EuVotoDistrital.org.br";

    $content = nl2br($content);

    if (is_array($toArr)) {
      foreach ($toArr as $to) {
        if (!evd_mail($to, $subject, $content)) {
          throw Exception;
        }
      }
    }

    $json = array('success' => 1);

  } catch (Exception $e) {
    $json = array('success' => 0);
  }

  return $json;
}

/**
 * Pega os filhos das seções para montar o submenu automaticamente
 */
function getSectionSubmenu($path) {
  //Pega todas as páginas, publicadas com ordem asc
  $wpQuery = new WP_Query();
  $allPages = $wpQuery->query(array('post_type' => 'page', 'nopaging' => true, 'post_status' => 'publish', 'order' => 'ASC'));

  //Pega o objeto da página pai
  $parent = get_page_by_path($path);

  //Pega todos os filhos
  $children = get_page_children($parent->ID, $allPages);

  //não printa nada se não achou filhos
  if (empty($children)) {
    return;
  }

  //Printa o submenu
  foreach ($children as $child) {
      $submenu = '<li';
        
      if (is_page($child->post_name)) {
        $submenu .= ' class="active"';
      }
      
      $submenu .= '><a href="' . home_url($parent->post_name . '/' . $child->post_name) . '" title="' . $child->post_title . '">' . $child->post_title . '</a></li>';

      echo $submenu;
  }
}

/**
 * shortcode para printar o texto de introdução da caixa de ferramentas com
 * meta de assinaturas setada no admin.
 */
function evd_tools_intro() {
  $goal = (int) get_option('evd_petition_goal');
  echo 'Seja qual for a forma que você encontrou para divulgar o movimento, aqui você encontra dicas e ferramentas que vão te ajudar na sua Mobilização. Participe, ajude o movimento conseguir ' . number_format($goal, 0, '', '.') . ' assinaturas';
}



function evd_check_redirect() {

  switch (true) {

    case is_page('voto-distrital'):

      wp_redirect(home_url('voto-distrital/o-que-e'), 301);

      exit(0);

    case is_page('mobilize'):

      wp_redirect(home_url('mobilize/o-que-posso-fazer-agora'), 301);

      exit(0);

  }

}



function evd_is_post() {
  
  return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';

}



function evd_get_url($url) {

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);

  curl_setopt($ch, CURLOPT_HEADER, 0);

  curl_setopt($ch, CURLOPT_HTTPGET, 1);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

  $result = curl_exec($ch);

  curl_close($ch);

  return $result;

}



// -- Admin



function evd_update_menu() {

  global $menu;

  $restricted = array(__('Links'));

  end($menu);

  while (prev($menu)) {

    $key = key($menu);

    $value = explode(' ', $menu[$key][0]);

    if (in_array($value[0] != null ? $value[0] : '', $restricted)) {

      unset($menu[$key]);

    }

  }



  remove_submenu_page('edit-comments.php', 'edit-comments.php');



  add_menu_page('Petição', 'Petição', 'edit_posts', 'petition', 'evd_petition', null, 31);

  add_submenu_page('petition', 'Petição', 'Petição', 'edit_posts', 'petition', 'evd_petition');

  add_submenu_page('petition', 'Petição', 'Adicionar assinaturas', 'edit_posts', 'petition_add', 'evd_petition_add');

  add_submenu_page('petition', 'Petição', 'Meta', 'edit_posts', 'petition_goal', 'evd_petition_goal');

}



function evd_dashboard(){

  global $wp_meta_boxes;

  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);

  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); 

  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);

}



function evd_footer_admin($text) {

  return sprintf('Desenvolvido por <a href="http://www.rrrock.com" target="_blank">RRROCK</a> + <a href="http://www.codekings.com.br" target="_blank">CodeKings</a>. %s', $text);

}



// -- Template



function evd_body_class($classes = '') {

  if (!is_array($classes)) {

    $classes = array($classes);

  }

  switch (true) {

    case is_page(array('assinar', 'confirmar')):

      $classes[] = 'thank-you';

      break;

    case is_page('grupos-de-acao'):

      $classes[] = 'email-groups';

      break;

    case is_page('blog'):

    case !(is_page() || is_home()):

      $classes[] = 'blog';

      break;

  }

  return $classes;

}



function evd_states() {

  global $wpdb;

  $sql = "SELECT DISTINCT `state` FROM `{$wpdb->evd_city}` ORDER BY `state`";

  return $wpdb->get_col($wpdb->prepare($sql));

}



function evd_cities($state) {

  global $wpdb;

  $sql = "SELECT DISTINCT `name` FROM `{$wpdb->evd_city}` WHERE `state` = %s ORDER BY `name`";

  return $wpdb->get_col($wpdb->prepare($sql, $state));

}

function evd_legislators($state) {

  global $wpdb;

  $sql = "SELECT `id`, `name` FROM `" . $wpdb->prefix . 'evd_legislators' . "` WHERE `state` = %s ORDER BY `name`";

  return $wpdb->get_results($wpdb->prepare($sql, $state), 'ARRAY_A');
}



function evd_social_bar() {

  ?>

  <nav class="secondary">

    <div class="first">

      <ul>

        <li class="faq first">

          <a href="<?php echo home_url('faq'); ?>" title="Perguntas frequentes">F.A.Q.</a>

        </li>

        <li class="contact last">

          <a href="<?php echo home_url('contato'); ?>" title="Contato">CONTATO</a>

        </li>

        <li class="imprensa last">

          <a href="<?php echo home_url('imprensa'); ?>" title="Imprensa">IMPRENSA</a>

        </li>

      </ul>

    </div>

    <div>

      <ul>

        <li class="rss first">

          <a href="<?php bloginfo('rss2_url'); ?>" title="Assinar o feed RSS">Feed RSS</a>

        </li>

        <li class="facebook">

          <a href="http://www.facebook.com/euvotodistrital" title="Página do movimento no Facebook" rel="external">Facebook</a>

        </li>

        <li class="twitter">

          <a href="http://www.twitter.com/euvotodistrital" title="Perfil do movimento no Twitter" rel="external">Twitter</a>

        </li>

        <li class="orkut last">

          <a href="http://www.orkut.com.br/Main#Profile?uid=9328289923700919422" title="Perfil do movimento no Orkut" rel="external">Orkut</a>

        </li>

      </ul>

    </div>

    <div class="last">

      <ul>

        <li class="like first">

          <iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode('http://www.facebook.com/euvotodistrital'); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=58&amp;action=like&amp;font&amp;colorscheme=light&amp;height=21&amp;locale=pt_BR" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:58px; height:21px;" allowTransparency="true"></iframe>

        </li>

        <li class="share">

          <a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(home_url()); ?>&t=<?php echo urlencode(get_bloginfo('name')); ?>" rel="external">Compartilhar</a>

        </li>

        <li class="tweet">

          <a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-via="euvotodistrital">Tweet</a>

        </li>

        <li class="promote">

          <a href="http://promote.orkut.com/preview?nt=orkut.com&amp;tt=<?php echo urlencode(get_bloginfo('name')); ?>&amp;du=<?php echo urlencode(home_url()); ?>&amp;cn=<?php echo urlencode(get_bloginfo('description')); ?>" rel="external">Promover</a>

        </li>

        <li class="gplus">

          <g:plusone size="medium" count="false"></g:plusone>

        </li>

        <li class="share-all last">

          <div class="addthis_toolbox addthis_default_style ">

            <a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4d7f69836f30dd05" class="addthis_button_compact">Compartilhar</a>

          </div>

        </li>
        
      </ul>

    </div>

  </nav>

  <?php

}



function evd_social_share() {

  ob_start();

  ?>

  <div class="social-share">

    <ul>

      <li class="share first">

        <a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(home_url()); ?>&t=<?php echo urlencode(get_bloginfo('name')); ?>" rel="external">Compartilhar</a>

      </li>

      <li class="tweet">

        <a href="http://twitter.com/?status=<?php echo rawurlencode('#EuVotoDistrital http://t.co/nkUf5z3 via @euvotodistrital'); ?>" rel="external">Tweet</a>

      </li>

      <li class="promote">

        <a href="http://promote.orkut.com/preview?nt=orkut.com&amp;tt=<?php echo urlencode(get_bloginfo('name')); ?>&amp;du=<?php echo urlencode(home_url()); ?>&amp;cn=<?php echo urlencode(get_bloginfo('description')); ?>" rel="external">Promover</a>

      </li>

      <li class="share-all last">

        <div class="addthis_toolbox addthis_32x32_style">

          <a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4d7f69836f30dd05" class="addthis_button_compact"></a>

        </div>

      </li>

    </ul>

    <fieldset>

      <input type="text" class="text" id="share-url" name="url" value="<?php echo home_url('assine'); ?>" />

      <button type="button">Copiar link</button>

    </fieldset>

  </div>

  <?php

  $code = ob_get_contents();

  ob_end_clean();

  return $code;

}



function evd_contact_form() {

  ob_start();

  ?>

  <?php if (($sent = evd_contact_form_control()) !== null) : ?>

  <script type="text/javascript">

    <?php if ($sent) : ?>

    var msg = 'Sua pergunta foi enviada com sucesso. Obrigado!';

    <?php else : ?>

    var msg = 'Ops! Algo estranho aconteceu enquanto enviávamos a sua pergunta. Por favor, tente novamente.';

    <?php endif; ?>

    window.alert(msg);

  </script>

  <?php endif; ?>

  <form action="<?php the_permalink(); ?>" method="post">

    <fieldset class="contact">

      <ol>

        <li class="message">

          <label for="contact-message">Mensagem:</label>

          <textarea id="contact-message" name="message" cols="50" rows="10"></textarea>

        </li>

        <li>

          <label for="contact-name">Nome:</label>

          <input type="text" class="text" id="contact-name" name="name_" />

        </li>

        <li>

          <label for="contact-email">E-mail:</label>

          <input type="text" class="text" id="contact-email" name="email" />

        </li>

        <li>

          <label for="contact-subject">Assunto:</label>

          <select id="contact-subject" name="subject">

            <option value="Quero ser voluntário">Quero ser voluntário</option>

            <option value="Sou político">Sou político</option>

            <option value="Problemas no site">Problemas no site</option>

            <option value="Dúvidas sobre o voto distrital">Dúvidas sobre o voto distrital</option>

            <option value="Dúvidas sobre o movimento">Dúvidas sobre o movimento</option>

            <option value="Imprensa">Imprensa</option>

            <option value="Outros">Outros</option>

          </select>

        </li>

        <li class="actions">

          <button type="submit">Enviar</button>

        </li>

      </ol>

    </fieldset>

  </form>

  <?php

  $code = ob_get_contents();

  ob_end_clean();

  return $code;

}



function evd_contact_form_control() {

  if (evd_is_post()) {

    $name    = !empty($_POST['name_'])   ? $_POST['name_']   : null;

    $email   = !empty($_POST['email'])   ? $_POST['email']   : null;

    $subject = !empty($_POST['subject']) ? $_POST['subject'] : null;

    $message = !empty($_POST['message']) ? $_POST['message'] : null;

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {

      return false;

    }

    $now = date('d/m/Y H:i:s', current_time('timestamp'));

    $content = "Olá,



Um novo contato foi feita agora ({$now}) através do site do movimento #euvotodistrital. Segue:



Nome: {$name}

E-mail: {$email}

Assunto: {$subject}



{$message}";

    $content = nl2br($content);

    return evd_mail(EVD_MAIL, $subject, $content);
  }

}



function evd_sign_fieldset() {

  $invalids = array();

  $form = null;

  if (!empty($_SESSION['evd_sign'])) {

    $form = $_SESSION['evd_sign'];

    extract($form); // $invalids, $sign

  }

  ?>

  <div class="votes">

    <?php $goal = (int) get_option('evd_petition_goal'); ?>

    <?php $total = evd_petition_count(false); ?>

    <?php 
      $width = ($goal !== 0) ? ceil($total / $goal * 100) : 0;
    ?>

    <h4><span style="width:<?php echo $width; ?>%"><?php echo number_format($total, 0, '', '.'); ?></span></h4>

  </div>

  <fieldset class="sign">

    <h2>apoiam o Movimento a favor do Voto Distrital. Participe! Assine para chegarmos em <strong><?php echo number_format($goal, 0, '', '.'); ?></strong>!</h2>

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

        <label><input type="checkbox" name="newsletter" value="1" <?php if (empty($sign) || $sign->newsletter) : ?>checked="checked"<?php endif; ?> /> Quero receber novidades</label>

      </li>

      <li class="volunteer <?php if (in_array('volunteer', $invalids)) : ?>invalid<?php endif; ?>">

        <label><input type="checkbox" name="volunteer" value="1" <?php if (!empty($sign) && $sign->volunteer) : ?>checked="checked"<?php endif; ?> /> Desejo ser voluntário</label>

      </li>

      <li class="sms <?php if (in_array('sms', $invalids)) : ?>invalid<?php endif; ?>">

        <label><input type="checkbox" name="sms" value="1" <?php if (!empty($sign) && $sign->sms) : ?>checked="checked"<?php endif; ?> /> Quero receber SMS sobre o movimento</label>

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

        <a class="button small" href="<?php echo home_url('assine'); ?>">Manifesto &nbsp; |</a>

      </li>

      <li class="privacy">
        <p>Adicionar <a href="<?php echo home_url('mobilize/assinaturas'); ?>">várias assinaturas</a>.</p>
        <p>Política de <a href="<?php echo home_url('termos-de-privacidade'); ?>">privacidade</a>.</p>
      </li>
    </ol>

    <div id="captcha">

      <div class="inner">

        <?php

        include_once 'recaptchalib.php';

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

  <script type="text/javascript">
    var city_sel;

    var form = jQuery('fieldset.sign').parent('form');

    form.find('input[name=sms]').click(function () {

      if (this.checked) {

        jQuery('#sign-phone').removeAttr('disabled');

      } else {

        jQuery('#sign-phone').attr('disabled', 'disabled');

      }

    });
    form.find('select[name=state]').change(function () {
      var city = form.find('select[name=city]');
      city.empty();
      city.append('<option value="">Carregando...</option>');
      jQuery.get('<?php echo home_url('/'); ?>?ajax&action=get_cities&state=' + jQuery(this).val(), function (data) {
        city.empty();
        jQuery.each(data, function (key, value) {
          var selected = city_sel && city_sel == value ? 'selected="selected"' : '';
          city.append('<option value="' + value + '" ' + selected + '>' + value + '</option>');
        })
      });
    });
    <?php if (!empty($sign) && $sign->state) : ?>
    <?php if ($sign->city) : ?>
    city_sel = '<?php echo $sign->city; ?>';
    <?php endif; ?>
    form.find('select[name=state]').change();
    <?php endif; ?>

    form.find('#sign-submit').click(function () {

      jQuery('#captcha').fadeIn();

    });

    form.submit(function () {

      if (!evd.form.validate(this)) {

        jQuery('#captcha').fadeOut('fast', function () {

          window.alert('Ops! Você esqueceu de preencher alguns campos no formulário de assinatura.');

        });

        return false;

      }

      return true;

    });
    <?php if (!empty($invalids)) : ?>

    jQuery(document).ready(function () {

      window.alert('Ops! Você esqueceu de preencher alguns campos no formulário de assinatura.');

    });

    <?php endif; ?>

  </script>

  <?php

}


/**
 * Controla as assinaturas do formulário
 *
 * @param boolean $isIframe - uma flag para indicar se a função é chamada do iFrame. Se sim, não
 * direcionar para a página de assine qdo finalizar.
 *
 * @return int | boolean
 */
function evd_sign_form_control($isIframe = false) {

  //limpa session sempre no inicio
  if (isset($_SESSION['evd_sign'])) {

    unset($_SESSION['evd_sign']);

  }
  
  if (evd_is_post()) {

    $name      = !empty($_POST['name_'])    ? $_POST['name_']    : null;

    $email     = !empty($_POST['email'])    ? $_POST['email']    : null;

    $identity  = !empty($_POST['identity']) ? $_POST['identity'] : null;

    $city      = !empty($_POST['city'])     ? $_POST['city']     : null;

    $state     = !empty($_POST['state'])    ? $_POST['state']    : null;

    $phone     = !empty($_POST['phone'])    ? $_POST['phone']    : null;

    $volunteer = !empty($_POST['volunteer']);

    $newsletter = !empty($_POST['newsletter']);

    $sms       = !empty($_POST['sms']);

    $invalids  = array ();

    if (empty($name)) {

      $invalids[] = 'name';

    }

    if (empty($identity)) {

      $invalids[] = 'identity';

    }

    if (empty($city)) {

      $invalids[] = 'city';

    }

    if (empty($state)) {

      $invalids[] = 'state';

    }

    require_once 'recaptchalib.php';

    $response = recaptcha_check_answer(EVD_RECAPTCHA_PRIVATE, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

    if (!$response->is_valid) {

      $invalids[] = 'captcha';

    }

    $sign = compact('name', 'email', 'identity', 'city', 'state', 'sms', 'volunteer', 'newsletter', 'phone');

    if (empty($invalids)) {

      if (!empty($email) && evd_petition_email_exists($email)) {

        return -1;

      }

      evd_petition_insert($sign);

      unset($_SESSION['evd_sign']);

      if (!empty($email)) {

        evd_sign_mail($email);

        if ($volunteer == 1) {
          evd_volunteer_mail($email);
        }
      }


      // Notifying EVD

      $content = "Olá!



Uma nova assinatura foi incluída na petição pelo Voto Distrital.



Nome: {$name}

RG: {$identity}";

      $content = nl2br($content);

      $subject = 'Assinatura recebida';

      return evd_mail(EVD_MAIL, $subject, $content);

    } else {

      $_SESSION['evd_sign'] = array (

        'sign'     => (object) $sign,

        'invalids' => $invalids,

      );

      if ($isIframe) {

        return false;
        
      }

      wp_redirect(home_url('assine'));

      exit(0);

    }

  }

}



function evd_sign_mail($email) {

  $token = md5($email . '#evd');

  $content = "Olá,

Obrigado por ter assinado a petição pelo Voto Distrital.

Você se junta nesse momento a outros milhares de pioneiros que acreditam que a mudança na política brasileira é possível. O primeiro passo foi dado. Convidamos você a aumentar ainda o impacto do Movimento #euvotodistrital.

COMPARTILHE - tanto nas redes sociais com no boca-boca, compartilhe com seus amigos, colegas de trabalho e vizinhos. Fale sobre o movimento.

SAIBA MAIS - Obtenha mais informações, se aprofunde sobre o assunto.

MOBILIZE - Saiba diferentes formas que você tem para ajudar o movimento. Seja organizando uma apresentação ou curtindo nossa fan page.

AGORA - você pode enviar um e-mail para seus amigos contando sobre o movimento e pedindo para eles assinarem. Cada apoio é importante para mostrar aos políticos que queremos um País mais próximo de nós.

Esse email é uma sugestão para você mobilizar sua rede de contatos para assinar a peição do Movimento. Acha que precisa melhorar? Fique à vontade para adaptar esse modelo ou fazer o seu mesmo! Assim chamaremos mais e mais gente para vir conosco!

----------

Olá,

Participo de um movimento chamado #EuVotoDistrital. 

Uma mobilização de pessoas pela reforma política no Brasil.

Dentre todas as estratégias do Movimento, a principal é somar MUITAS assinaturas. Temos que ser um movimento de milhões.

Quero pedir a sua ajuda. Você pode nos ajudar a alcançar essa grande meta?

Primeiro passo é você assinar essa petição. Segundo passo é compartilhar com seus contatos.

www.euvotodistrital.org.br/assine

Fazer uma política diferente está em NOSSA capacidade de mobilizar cidadão por cidadão.

Acredite. 

Em menos de um dia conseguimos mais de 1000 assinaturas.

Um abraço de engajamento

#euvotodistrital";

  $content = nl2br($content);

  $subject = 'Obrigado por ter assinado a petição pelo Voto Distrital';

  return evd_mail($email, $subject, $content);

}

/**
 * E-mail para quem selecionar "Quero ser voluntário" quando assinar a petição
 *
 * @param string $email - email da pessoa que assinou
 *
 * @return boolean
 */
function evd_volunteer_mail($email) {

  $content = 'Olá!

Bem Vindo a equipe de voluntários do Movimento #EuVotoDistrital!

A partir de agora, você faz parte de uma rede de milhares de brasileiros que querem pôr a mão na
massa para aprovar o voto distrital no Brasil. Nosso objetivo é espalhar nossa causa para o maior
número de pessoas possíveis e converter em assinaturas para pressionar o Congresso Nacional a votar
nessa mudança. Nós temos muito trabalho pela frente! Além de acompanhar e compar-
tilhar nosso site através das redes sociais e email, existem outras maneiras de mobilizar pessoas
ao seu redor. Veja as nossas sugestões:

1) Entre para os grupos de ação

Escolhemos o Facebook como ferramenta principal de interação e encontro entre os voluntários.
Através dos Grupos de Ação, criamos um ambiente de cooperação para que todos possam unir
forças, planejar ações e trocar experiências. Você pode criar o grupo da sua cidade,
faculdade, bairro e etc...

<a href="http://www.euvotodistrital.org.br/mobilize/grupos-de-acao/">http://www.euvotodistrital.org.br/mobilize/grupos-de-acao/</a>

2) Participe do #MobilizeJá semanal

Se você quer mobilizar mas não tem tempo, você pode participar ativamente do #MobilizeJá
— são ações simples e rápidas (pela Internet) para chamar a atenção para o movimento.

<a href="http://www.euvotodistrital.org.br/mobilizeja/">http://www.euvotodistrital.org.br/mobilizeja/</a>

3) Marque um encontro com seus amigos, colegas e familiares. Explique para eles o movimento - tire
fotos e nos conte como foi, assim você pode inspirar milhares de brasileiros a agirem!

Não deixe de conferir e usar os materiais da nossa Caixa de Ferramentas — através de vídeos e
textos explicativos você terá informações sobre o movimento, mais dicas de como mobilizar as
pessoas e material de mobilização (cartilha, logo, apresentações, banco de idéias p/ mobilização
e etc).

Qualquer dúvida, sugestão ou crítica mande um email para voluntarios@euvotodistrital.org.br

Um abraço,
Equipe de Mobilizadores #EuVotoDistrital.';

  $content = nl2br($content);

  $subject = 'Obrigado por se tornar um voluntário do Movimento #EuVotoDistrital!';

  return evd_mail($email, $subject, $content);
 
}



function evd_confirm_form_control() {

  $token = !empty($_GET['token']) ? $_GET['token'] : null;

  $email = !empty($_GET['email']) ? $_GET['email'] : null;

  if ($token == md5($email . '#evd')) {

    return evd_petition_confirm($email);

  }

  return false;

}



function evd_question_form_control() {

  if (evd_is_post()) {

    $name     = !empty($_POST['name_'])    ? $_POST['name_']    : null;

    $email    = !empty($_POST['email'])    ? $_POST['email']    : null;

    $question = !empty($_POST['question']) ? $_POST['question'] : null;

    if (empty($name) || empty($email) || empty($question)) {

      return false;

    }

    $now = date('d/m/Y H:i:s', current_time('timestamp'));

    $content = "Olá,



Uma nova pergunta foi feita agora ({$now}) no site do movimento #euvotodistrital.



{$name} ({$email}) pergunta:



{$question}";

    $content = nl2br($content);

    $subject = 'Pergunta enviada';

    return evd_mail(EVD_MAIL, $subject, $content);
  }
}



// -- Petition (admin + DB)



define('EVD_PETITION_PER_PAGE', 100);



function evd_petition_setup() {

  global $wpdb;



  $wpdb->evd_petition = $wpdb->prefix . 'evd_petition';

  $wpdb->evd_city     = $wpdb->prefix . 'evd_city';



  if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->evd_petition}'") != $wpdb->evd_petition) {

    $sql = "CREATE TABLE `{$wpdb->evd_petition}` (

      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,

      `name` varchar(255) NOT NULL,

      `email` varchar(255) NOT NULL,

      `identity` VARCHAR(20) NOT NULL,

      `city` VARCHAR(50) NOT NULL,

      `state` VARCHAR(20) NOT NULL,

      `phone` BIGINT(10) NOT NULL,

      `sms` BOOLEAN NOT NULL DEFAULT FALSE,

      `volunteer` BOOLEAN NOT NULL DEFAULT FALSE,

      `newsletter` BOOLEAN NOT NULL DEFAULT FALSE,

      `confirmed` BOOLEAN NOT NULL DEFAULT FALSE,

      PRIMARY KEY (`id`)

      ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);

  }

}



function evd_petition_init() {

  $action = !empty($_GET['action']) ? $_GET['action'] : null;

  switch ($action) {

    case 'export':

      $state = !empty($_GET['state']) ? $_GET['state'] : null;

      $volunteer = !empty($_GET['volunteer']) ? (int) $_GET['volunteer'] : null;

      $newsletter = !empty($_GET['newsletter']) ? (int) $_GET['newsletter'] : null;

      $sms = !empty($_GET['sms']) ? (int) $_GET['sms'] : null;

      $filter = compact('state', 'newsletter', 'volunteer', 'sms');

      evd_petition_export($filter);

      break;

  }

}



function evd_petition_count($only_confirmed = true, $filter = array()) {

  global $wpdb;

  $sql = "SELECT COUNT(*) FROM `{$wpdb->evd_petition}`";

  $where = array();

  if (!empty($filter['state']) && $filter['state'] != -1) {

    $where[] = "`state` = '{$filter['state']}'";

  }

  if (!empty($filter['volunteer']) && $filter['volunteer'] != -1) {

    $where[] = "`volunteer` = {$filter['volunteer']}";

  }

  if (!empty($filter['newsletter']) && $filter['newsletter'] != -1) {

    $where[] = "`newsletter` = {$filter['newsletter']}";

  }

  if (!empty($filter['sms']) && $filter['sms'] != -1) {

    $where[] = "`sms` = {$filter['sms']}";

  }

  if ($only_confirmed === true) {

    $where[] = "`confirmed` = 1";

  }

  if (!empty($where)) {

    $sql .= " WHERE " . implode(" AND ", $where);

  }

  return $wpdb->get_var($wpdb->prepare($sql));

}



function evd_petition_list($paged = 0, $filter = array()) {

  global $wpdb;

  $paged = (int) $paged;

  $offset = $paged * EVD_PETITION_PER_PAGE;

  $sql = "SELECT * FROM `{$wpdb->evd_petition}`";

  $where = array();

  if ($filter['state'] !== null && $filter['state'] != -1) {

    $where[] = "`state` = '{$filter['state']}'";

  }

  if (isset($filter['volunteer']) && $filter['volunteer'] !== null && $filter['volunteer'] != -1) {

    $where[] = "`volunteer` = {$filter['volunteer']}";

  }

  if (isset($filter['newsletter']) && $filter['newsletter'] !== null && $filter['newsletter'] != -1) {

    $where[] = "`newsletter` = {$filter['newsletter']}";

  }

  if (isset($filter['sms']) && $filter['sms'] !== null && $filter['sms'] != -1) {

    $where[] = "`sms` = {$filter['sms']}";

  }

  if (!empty($where)) {

    $sql .= " WHERE " . implode(" AND ", $where);

  }

  $sql .= " LIMIT {$offset}, " . EVD_PETITION_PER_PAGE;

  return $wpdb->get_results($wpdb->prepare($sql), OBJECT_K);

}



function evd_petition_export($filter = array()) {

  global $wpdb;

  $sql = "SELECT DISTINCT `name`, `email` FROM `{$wpdb->evd_petition}` WHERE `email` <> '' AND `email` IS NOT NULL";

  $where = array();

  if (!empty($filter['state']) && $filter['state'] != -1) {

    $where[] = "`state` = '{$filter['state']}'";

  }

  if (!empty($filter['volunteer']) && $filter['volunteer'] != -1) {

    $where[] = "`volunteer` = {$filter['volunteer']}";

  }

  if (!empty($filter['newsletter']) && $filter['newsletter'] != -1) {

    $where[] = "`newsletter` = {$filter['newsletter']}";

  }

  if (!empty($filter['sms']) && $filter['sms'] != -1) {

    $where[] = "`sms` = {$filter['sms']}";

  }

  if (!empty($where)) {

    $sql .= " AND " . implode(" AND ", $where);

  }

  $rows = $wpdb->get_results($wpdb->prepare($sql), OBJECT_K);

  $output = '';

    foreach ($rows as $row) {

    $output .= "{$row->name}\t{$row->email}\r\n";

  }

header("Pragma: public");

  header("Expires: 0");

  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

  header("Cache-Control: private", false);

  header("Content-Type: application/force-download");

  header("Content-Disposition: attachment; filename=\"evd_export.csv\";");

  header("Content-Transfer-Encoding: binary");

  ob_clean();

  echo $output;

  flush();

  exit();

}



function evd_petition_email_exists($email) {

  global $wpdb;

  $sql = "SELECT COUNT(*) FROM `{$wpdb->evd_petition}` WHERE `email` = %s";

  return $wpdb->get_var($wpdb->prepare($sql, $email)) > 0;

}



function evd_petition_insert($sign) {

  global $wpdb;

  if (is_object($sign)) {

    $sign = (array) $sign;

  }

  return $wpdb->insert($wpdb->evd_petition, $sign);

}



function evd_petition_confirm($sign_id) {

  global $wpdb;

  $sign_id = (int) $sign_id;

  return $wpdb->update($wpdb->evd_petition, array('confirmed' => 1), array('id' => $sign_id));

}



function evd_petition_delete($sign_id) {

  global $wpdb;

  $sign_id = (int) $sign_id;

  return $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->evd_petition}` WHERE `id` = %s", $sign_id));

}



function evd_petition() {
  
  $action = !empty($_POST['action']) ? $_POST['action'] : null;

  if ($action == -1) {

    $action = !empty($_POST['action2']) ? $_POST['action2'] : null;

  }

  $sign = !empty($_POST['sign']) ? $_POST['sign'] : null;

  if (!empty($sign) && is_array($sign)) {

    foreach ($sign as $id) {

      switch ($action) {

        case 'confirm':

          evd_petition_confirm($id);

          break;

        case 'delete':

          evd_petition_delete($id);

          break;

      }

    }

  }

  $state = !empty($_POST['state']) ? $_POST['state'] : null;

  if (isset($_POST['volunteer'])) {
    $volunteer = ($_POST['volunteer'] !== '-1') ? (int) $_POST['volunteer'] : null;
  }

  if (isset($_POST['newsletter'])) {
    $newsletter = ($_POST['newsletter'] !== '-1') ? (int) $_POST['newsletter'] : null;
  }

  if (isset($_POST['sms'])) {
    $sms = ($_POST['sms'] !== '-1') ? (int) $_POST['sms'] : null;
  }

  $filter = compact('state', 'volunteer', 'newsletter', 'sms');

  $paged = !empty($_GET['paged']) ? (int) $_GET['paged'] : 0;

  $total = evd_petition_count(false, $filter);

  $pages = floor($total / EVD_PETITION_PER_PAGE);

  if ($paged > $pages) {

    $paged = $pages;

  }

  $start = $paged * EVD_PETITION_PER_PAGE + 1;

  $end = $start + EVD_PETITION_PER_PAGE - 1;

  if ($end > $total) {

    $end = $total;

  }

  $paging = array();

  foreach (range($paged - 2, $paged + 2) as $p) {

    if ($p < 0 || $p > $pages) {

      continue;

    }

    $paging[] = $p;

  }

  ?>

  <div class="wrap">

    <div id="icon-edit" class="icon32"><br></div>

    <h2>Petição <a href="<?php echo admin_url('admin.php?page=petition_add'); ?>" class="button add-new-h2">Adicionar assinaturas</a> <a href="<?php echo admin_url('admin.php?page=petition&action=export&' . http_build_query($filter)); ?>" class="button add-new-h2">Exportar e-mails</a></h2>

    <div class="clear"><br /></div>

    <form id="posts-filter" action="<?php echo admin_url('admin.php?page=petition&paged=' . $paged); ?>" method="post">

      <div class="tablenav">

        <div class="alignleft">

          <strong>Filtrar as assinaturas</strong> 

          <select name="state">

            <option value="-1">Estado</option>

            <?php foreach (evd_states() as $state) : ?>

            <option value="<?php echo $state; ?>"<?php if (!empty($filter['state']) && $filter['state'] == $state) : ?> selected="selected"<?php endif; ?>><?php echo $state; ?></option>

            <?php endforeach; ?>

          </select>

          <select name="volunteer">

            <option value="-1">Voluntário</option>

            <option value="1"<?php if ($filter['volunteer'] !== null && $filter['volunteer'] == 1) : ?> selected="selected"<?php endif; ?>>Sim</option>

            <option value="0"<?php if ($filter['volunteer'] !== null && $filter['volunteer'] == 0) : ?> selected="selected"<?php endif; ?>>Não</option>

          </select>

          <select name="newsletter">

            <option value="-1">Recebe Newsletter</option>

            <option value="1"<?php if ($filter['newsletter'] !== null && $filter['newsletter'] == 1) : ?> selected="selected"<?php endif; ?>>Sim</option>

            <option value="0"<?php if ($filter['newsletter'] !== null && $filter['newsletter'] == 0) : ?> selected="selected"<?php endif; ?>>Não</option>

          </select>

          <select name="sms">

            <option value="-1">Recebe SMS</option>

            <option value="1"<?php if ($filter['sms'] !== null && $filter['sms'] == 1) : ?> selected="selected"<?php endif; ?>>Sim</option>

            <option value="0"<?php if ($filter['sms'] !== null && $filter['sms'] == 0) : ?> selected="selected"<?php endif; ?>>Não</option>

          </select>

          <input type="submit" value="Filtrar" name="filter" id="filter" class="button-secondary" />

        </div>

      </div>

      <div class="tablenav">

        <div class="alignleft actions">

          <select name="action">

            <option value="-1" selected="selected">Ações em massa</option>

            <option value="confirm">Confirmar</option>

            <option value="delete">Excluir</option>

          </select>

          <input type="submit" value="Aplicar" name="doaction" id="doaction" class="button-secondary action" />

        </div>

        <div class="tablenav-pages">

          <span class="displaying-num">Exibindo <?php echo $start; ?>&#8211;<?php echo $end; ?> de <?php echo $total; ?></span>

          <?php if (count($paging) > 1) : ?>

          <?php if ($paged > 0) : ?>

          <a class="prev page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . ($paged - 1)); ?>">&laquo;</a>

          <?php endif; ?>

          <?php if (reset($paging) > 0) : ?>

          <a class="page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=0'); ?>">1</a>

          <span class="page-numbers dots">...</span>

          <?php endif; ?>

          <?php foreach ($paging as $p) : ?>

          <?php if ($p == $paged) : ?>

          <span class="page-numbers current"><?php echo $p + 1; ?></span>

          <?php else : ?>

          <a class="page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . $p); ?>"><?php echo $p + 1; ?></a>

          <?php endif; ?>

          <?php endforeach; ?>

          <?php if (end($paging) < $pages) : ?>

          <span class="page-numbers dots">...</span>

          <a class="page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . $pages); ?>"><?php echo $pages + 1; ?></a>

          <?php endif; ?>

          <?php if ($paged < $pages) : ?>

          <a class="next page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . ($paged + 1)); ?>">&raquo;</a>

          <?php endif; ?>

          <?php endif; ?>

        </div>

        <div class="clear"></div>

      </div>

      <div class="clear"></div>

      <table class="widefat fixed" cellspacing="0">

        <thead>

          <tr>

            <th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox" /></th>

            <th scope="col" id="name" class="manage-column column-name">Nome</th>

            <th scope="col" id="email" class="manage-column column-email">E-mail</th>

            <th scope="col" id="city" class="manage-column column-city">Cidade</th>

            <th scope="col" id="confirmed" class="manage-column column-confirmed" style="width:90px;">Confirmado</th>

            <th scope="col" id="volunteer" class="manage-column column-volunteer" style="width:90px;">Voluntário</th>

            <th scope="col" id="newsletter" class="manage-column column-newsletter" style="width:90px;">Recebe Newsletter</th>

            <th scope="col" id="phone" class="manage-column column-phone" style="width:120px;">Celular</th>

          </tr>

        </thead>

        <tfoot>

          <tr>

            <th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>

            <th scope="col" class="manage-column column-name">Nome</th>

            <th scope="col" class="manage-column column-email">E-mail</th>

            <th scope="col" class="manage-column column-city">Cidade</th>

            <th scope="col" class="manage-column column-confirmed" style="width:90px;">Confirmado</th>

            <th scope="col" class="manage-column column-volunteer" style="width:90px;">Voluntário</th>

            <th scope="col" class="manage-column column-newsletter" style="width:90px;">Recebe Newsletter</th>

            <th scope="col" class="manage-column column-phone" style="width:120px;">Celular</th>

          </tr>

        </tfoot>

        <?php $i = 0; foreach (evd_petition_list($paged, $filter) as $sign) : ?>

        <tbody>

          <tr id="sign-<?php echo $sign->id; ?>" <?php if ($i % 2 == 0) : ?>class="alternate"<?php endif; ?> valign="top">

            <th scope="row" class="check-column"><input type="checkbox" name="sign[]" value="<?php echo $sign->id; ?>" /></th>

            <td class="column-name"><strong><?php echo $sign->name; ?></strong><br />RG <?php echo $sign->identity; ?></td>

            <td class="column-email"><?php echo $sign->email; ?></td>

            <td class="column-city"><?php echo $sign->city; ?>/<?php echo $sign->state; ?></td>

            <td class="column-confirmed" style="width:90px;"><?php echo $sign->confirmed ? 'Sim' : 'Não'; ?></td>

            <td class="column-volunteer" style="width:90px;"><?php if ($sign->volunteer) : ?>Sim<?php else: ?>Não<?php endif; ?></td>

            <td class="column-newsletter" style="width:90px;"><?php if ($sign->newsletter) : ?>Sim<?php else: ?>Não<?php endif; ?></td>

            <td class="column-phone" style="width:120px;"><?php if ($sign->phone) : ?><?php echo $sign->phone; ?><?php if ($sign->sms) : ?> (SMS)<?php endif; ?><?php else : ?>-<?php endif; ?></td>

          </tr>

        </tbody>

        <?php $i++; endforeach; ?>

      </table>

      <div class="tablenav">

        <div class="tablenav-pages">

          <span class="displaying-num">Exibindo <?php echo $start; ?>&#8211;<?php echo $end; ?> de <?php echo $total; ?></span>

          <?php if (count($paging) > 1) : ?>

          <?php if ($paged > 0) : ?>

          <a class="prev page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . ($paged - 1)); ?>">&laquo;</a>

          <?php endif; ?>

          <?php if (reset($paging) > 0) : ?>

          <a class="page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=0'); ?>">1</a>

          <span class="page-numbers dots">...</span>

          <?php endif; ?>

          <?php foreach ($paging as $p) : ?>

          <?php if ($p == $paged) : ?>

          <span class="page-numbers current"><?php echo $p + 1; ?></span>

          <?php else : ?>

          <a class="page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . $p); ?>"><?php echo $p + 1; ?></a>

          <?php endif; ?>

          <?php endforeach; ?>

          <?php if (end($paging) < $pages) : ?>

          <span class="page-numbers dots">...</span>

          <a class="page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . $pages); ?>"><?php echo $pages + 1; ?></a>

          <?php endif; ?>

          <?php if ($paged < $pages) : ?>

          <a class="next page-numbers" href="<?php echo admin_url('admin.php?page=petition&paged=' . ($paged + 1)); ?>">&raquo;</a>

          <?php endif; ?>

          <?php endif; ?>

        </div>

        <div class="alignleft actions">

          <select name="action2">

            <option value="-1" selected="selected">Ações em massa</option>

            <option value="confirm">Confirmar</option>

            <option value="delete">Excluir</option>

          </select>

          <input type="submit" value="Aplicar" name="doaction2" id="doaction2" class="button-secondary action" />

          <br class="clear" />

        </div>

        <br class="clear" />

      </div>

    </form>

  </div>

  <?php

}



function evd_petition_add() {
  ?>

  <div class="wrap">

    <div id="icon-edit" class="icon32"><br></div>

    <h2>Adicionar assinaturas</h2>

    <div class="clear"><br /></div>

    <form id="add-sign" action="<?php echo admin_url('admin.php?page=petition_add'); ?>" method="post">

      <table class="form-table">

        <tbody>

          <tr valign="top">

            <th scope="row">

              <label for="sign-name">Nome:</label>

            </th>

            <td>

              <input type="text" class="text" id="sign-name" name="name_" value="<?php if (!empty($sign)) echo $sign->name; ?>" />

          </tr>

          <tr valign="top">

            <th scope="row">

              <label for="sign-email">E-mail:</label>

            </th>

            <td>

              <input type="text" class="text" id="sign-email" name="email" value="<?php if (!empty($sign)) echo $sign->email; ?>" />
            </td>

          </tr>

          <tr valign="top">

            <th scope="row">

              <label for="sign-identity">RG:</label>

            </th>

            <td>

              <input type="text" class="text" id="sign-identity" name="identity" value="<?php if (!empty($sign)) echo $sign->identity; ?>" />

          </tr>

          <tr valign="top">

            <th scope="row">

              <label for="sign-state">Estado:</label>

            </th>

            <td>

              <select id="sign-state" name="state">
                <option value="">--</option>

                <?php foreach (evd_states() as $state) : ?>

                <option value="<?php echo $state; ?>" <?php if (!empty($sign) && $sign->state == $state) : ?>selected="selected"<?php endif; ?>><?php echo $state; ?></option>

                <?php endforeach; ?>

              </select>
            </td>

          </tr>

          <tr valign="top">

            <th scope="row">

              <label for="sign-city">Cidade:</label>

            </th>

            <td>

              <select id="sign-city" name="city">
                <option value="">--</option>

              </select>
            </td>
          </tr>

          <tr valign="top">

            <th scope="row">

            </th>

            <td>

              <label><input type="checkbox" name="volunteer" value="1" <?php if (!empty($sign) && $sign->volunteer) : ?>checked="checked"<?php endif; ?> /> Desejo ser voluntário</label>
            </td>

          </tr>

          <tr valign="top">

            <th scope="row">

            </th>

            <td>

              <label><input type="checkbox" name="newsletter" value="1" <?php if (!empty($sign) && $sign->newsletter) : ?>checked="checked"<?php endif; ?> /> Quero receber novidades sobre o movimento</label>
            </td>

          </tr>

          <tr valign="top">

            <th scope="row">

            </th>

            <td>

              <label><input type="checkbox" name="sms" value="1" <?php if (!empty($sign) && $sign->sms) : ?>checked="checked"<?php endif; ?> /> Quero receber SMS sobre o movimento</label>
            </td>

          </tr>

          <tr valign="top">

            <th scope="row">

              <label for="sign-phone">Celular:</label>

            </th>

            <td>

              <input type="text" class="text" id="sign-phone" name="phone" value="<?php if (!empty($sign)) echo $sign->phone; ?>" <?php if (empty($sign) || !$sign->sms) : ?>disabled="disabled"<?php endif; ?> />
            </td>

          </tr>

        </tbody>

      </table>

      <p class="submit"><input type="submit" id="submit" name="submit" class="button-primary" value="Adicionar"></p>

    </form>

    <script type="text/javascript">
      var form = jQuery('form#add-sign');

      form.find('select[name=state]').change(function () {
        var city = form.find('select[name=city]');
        city.empty();
        city.append('<option value="">Carregando...</option>');
        jQuery.get('<?php echo home_url('/'); ?>?ajax&action=get_cities&state=' + jQuery(this).val(), function (data) {
          city.empty();
          jQuery.each(data, function (key, value) {
            city.append('<option value="' + value + '">' + value + '</option>');
          })
        });
      });
    </script>
  </div>

  <?php

}



function evd_petition_goal() {

  add_option('evd_petition_goal', 1000000);

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_POST['evd_petition_goal'])) {

      update_option('evd_petition_goal', (int) $_POST['evd_petition_goal']);

    }

  }

  ?>

  <div class="wrap">

    <div id="icon-edit" class="icon32"><br></div>

    <h2>Meta da petição</h2>

    <div class="clear"><br /></div>

    <form action="<?php echo admin_url('admin.php?page=petition_goal'); ?>" method="post">

      <table class="form-table">

        <tbody>

          <tr valign="top">

            <th scope="row">

              <label for="evd_petition_goal">Número total de assinaturas</label>

            </th>

            <td>

              <input type="text" id="evd_petition_goal" name="evd_petition_goal" value="<?php form_option('evd_petition_goal'); ?>" class="regular-text">

              <span class="description">Esse valor será usado para calcular a porcentagem da meta e na exibição no site.</span>

            </td>

          </tr>

        </tbody>

      </table>

      <p class="submit"><input type="submit" id="submit" name="submit" class="button-primary" value="Salvar Alterações"></p>

    </form>

  </div>

  <?php

}



// -- Social feeds



function evd_social_twitter($limit=3) {

  static $updates;

  if (empty($updates)) {

    $url = 'http://search.twitter.com/search.json?q=%23euvotodistrital&rpp='.$limit;

    $result = evd_get_url($url);

    $updates = array();

    if (!empty($result)) {

      $updates = json_decode($result);

      if (!empty($updates->results)) {

        $updates = $updates->results;

      }

    }

  }

  return $updates;

}



function evd_social_facebook($limit=3) {

  static $updates;

  if (empty($updates)) {

    $url = 'http://graph.facebook.com/search?q=euvotodistrital&limit='.$limit;

    $result = evd_get_url($url);

    $updates = array();

    if (!empty($result)) {

      $updates = json_decode($result);

      if (!empty($updates->data)) {

        $updates = $updates->data;

      }

    }

  }

  return $updates;

}



function evd_social_all($twitter_limit=3, $facebook_limit=3) {

  $updates = array();

  $twitter = evd_social_twitter($twitter_limit);

  $facebook = evd_social_facebook($facebook_limit);

  if (is_array($twitter)) {

    $updates = array_merge($updates, $twitter);

  }

  if (is_array($facebook)) {

    $updates = array_merge($updates, $facebook);

  }

  for ($i = 0, $s = count($updates); $i < $s; $i++) {

    for ($j = $i; $j < $s; $j++) {

      if (empty($updates[$i]->timestamp)) {

        $updates[$i]->timestamp = strtotime(!empty($updates[$i]->created_at) ? $updates[$i]->created_at : $updates[$i]->created_time);

      }

      if (empty($updates[$j]->timestamp)) {

        $updates[$j]->timestamp = strtotime(!empty($updates[$j]->created_at) ? $updates[$j]->created_at : $updates[$j]->created_time);

      }

      if ($updates[$j]->timestamp > $updates[$i]->timestamp) {

        $temp = $updates[$i];

        $updates[$i] = $updates[$j];

        $updates[$j] = $temp;

      }

    }

  }

  return $updates;

}

/**
 * Faz uma chamada para a API de contatos do Google
 *
 * @param string $url
 * @param string $token
 * @return string
 */
function evd_gmail_call($url, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $curlheader[0] = sprintf("Authorization: AuthSub token=\"%s\"/n", $token);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheader);

    $output = curl_exec($ch);

    curl_close($ch);

    return $output;
}

/**
 * Envia emails de convite
 *
 * @throws Exception quando não consegue mandar algum email.
 *
 * @return boolean
 */
function evd_invite_form_control() {
  if (evd_is_post()) {
    $name = !empty($_POST['invite-name']) ? $_POST['invite-name'] : null;
    $email = !empty($_POST['invite-email']) ? $_POST['invite-email'] : null;
    $friends = !empty($_POST['invite-friends']) ? $_POST['invite-friends'] : array();

    if (empty($name) || empty($email) || empty($friends)) {
      return false;
    }

    $subject = $name . ' te recomendou #EuVotoDistrital — é o cidadão com mais poder de decisão.';

    $content = 'Amigo(a),

#EuVotoDistrital é um movimento de cidadãos brasileiros, ou seja, de pessoas: trabalhadores, estudantes, empresários, usuários, sociedade, você, todo brasileiro que sonha por um País melhor e mais justo.

Nosso objetivo é aprovar pelo Congresso a emenda constitucional que torna o voto distrital o sistema eleitoral para eleição de deputados federais, estaduais e vereadores.

Chegou a HORA de mudar a historia da política brasileira!

Conheça. Assine. Mobilize.

http://www.EuVotoDistrital.org.br';
    $content = nl2br($content);

    try {
      foreach ($friends as $friend) {
        if (!evd_mail($friend, $subject, $content)) {
          //throw uma exceção se não conseguiu mandar algum email
          throw new Exception;
        }
      }

      return true;
      
    } catch (Exception $e) {
      return false;
    }
  }
}

// Métodos para o invite do hotmail

/**
 * Usado para gerar a url de contatos do Hotmail
 */
function fixed_base_convert($numstring, $frombase, $tobase) {
  $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
	$tostring = substr($chars, 0, $tobase);
	$length = strlen($numstring);
	$result = '';

  for ($i = 0; $i < $length; $i++) {
    $number[$i] = strpos($chars, $numstring{$i});
  }

  do {
    $divide = 0;
		$newlen = 0;

    for ($i = 0; $i < $length; $i++) {
      $divide = $divide * $frombase + $number[$i];

		  if ($divide >= $tobase) {
        $number[$newlen++] = (int)($divide / $tobase);
        $divide = $divide % $tobase;
		  } elseif ($newlen > 0) {
        $number[$newlen++] = 0;
		  }
		}

    $length = $newlen;
		$result = $tostring{$divide} . $result;
  } while ($newlen != 0);

	return $result;
}

/**
 * Usado para gerar a url de contatos do hotmail
 */
function hexa_to_64_signed_decimal($hexa) {
  $bin = fixed_base_convert($hexa, 16, 2);

  if (64 === strlen($bin) and 1 == $bin[0]) {
    $inv_bin = strtr($bin, '01', '10');
		$i = 63;

    while (0 !== $i) {

      if (0 == $inv_bin[$i]) {
        $inv_bin[$i] = 1;
        $i = 0;
		  } else {
        $inv_bin[$i] = 0;
        $i--;
		  }
		}

		return '-' . fixed_base_convert($inv_bin, 2, 10);
	} else {
    return fixed_base_convert($hexa, 16, 10);
  }
}

/**
 * Retorna o objeto do Windows Live Login para autenticação com o hotmail
 *
 * @return WindowsLiveLogin
 */
function evd_get_wll() {
  $keyFile = get_template_directory() . '/../../../libraries/hotmail/Application-Key.xml';

  return WindowsLiveLogin::initFromXml($keyFile);
}

/**
 * Seta cookie com token de autenticação do hotmail
 *
 * @param string $value
 */
function evd_hotmail_set_cookie($value = '') {
  $cookieTtl = time() + (10 * 365 * 24 * 60 * 60);

  setcookie(EVD_HOTMAIL_COOKIE, $value, $cookieTtl);
}

/**
 * Retorna valor do cookie de autenticação do hotmail
 *
 * @return string
 */
function evd_hotmail_get_cookie() {
  return $_COOKIE[EVD_HOTMAIL_COOKIE];
}

/**
 * Retorna url de autenticação no Gmail
 *
 * @return string
 *
 */
function evd_gmail_auth_url() {
  $baseUrl = 'https://www.google.com/accounts/AuthSubRequest';
  $scope = urlencode('http://www.google.com/m8/feeds/contacts/');

  return $baseUrl . '?session=0&next='. get_permalink() . '&scope=' . $scope . '&secure=0&hd=default';
}

/**
 * Retorna url de autenticação no Live
 *
 * @return string
 */
function evd_hotmail_auth_url($wll) {
  $scope = 'Contacts.Invite';
  
  return $wll->getConsentUrl($scope, null, get_permalink(), $market=null);
}

/**
 * Retorna url do hotmail mas adiciona a biblioteca. Para ser usado como shortcode em um post.
 *
 * @TODO: Rever isso
 */
function evd_hotmail_url() {
  require_once get_template_directory() . '/../../../libraries/hotmail/windowslivelogin.php';

  $wll = evd_get_wll();

  $scope = 'Contacts.Invite';
  $returnUrl = home_url('convide/hotmail');

  $url = $wll->getConsentUrl($scope, null, $returnUrl, $market=null);

  return '<li class="hotmail"><a href="' . $url . '" title="Importar contatos do Hotmail">Importar contatos do Hotmail</a></li>';
}

/**
 * Printa html dos botões
 *
 * @param $wll
 */
function evd_show_auth_links($wll) {
  echo '<h1>Convide seus amigos</h1>
        <ul class="invite-services">
          <li class="gmail"><a href="' . evd_gmail_auth_url() . '" title="Importar contatos do Gmail">Importar contatos do Gmail</a></li>
          <li class="hotmail"><a href="' . evd_hotmail_auth_url($wll) . '" title="Importar contatos do Hotmail">Importar contatos do Hotmail</a></li>
        </ul>';
}

/**
 * Retorna os contatos do hotmail
 *
 * @return string - xml dos contatos
 */
function evd_get_hotmail_contacts($token) {
  $expiry = $token->getExpiry();
	$expiry = date(DATE_RFC2822, $expiry);

	$delegationToken = $token->getDelegationToken();
	$cid = $token->getLocationID();

	// convert the cid to a signed 64-bit integer
	$lid = hexa_to_64_signed_decimal($cid, 16, 10);

  $uri = 'https://livecontacts.services.live.com/users/@C@' . $lid . '/rest/invitationsbyemail';
  
	// Adiciona token no header
	$headers = array("Authorization: DelegatedToken dt=\"$delegationToken\"");

  $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $uri);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	$xml = curl_exec($curl);

  return $xml;
}

/**
 * Parse xml de contatos do hotmail
 *
 * @param string $xml
 * @return array - com os emails dos contatos
 */
function evd_parse_hotmail_xml($xml) {
  require_once get_template_directory() . '/../../../libraries/minixml/minixml.inc.php';

  $parsedXml = new MiniXMLDoc();
  $parsedXml->fromString($xml);
  $arrayXml = $parsedXml->toArray();
  $contacts = $arrayXml['LiveContacts']['Contacts']['Contact'];

  $friends = array();

  foreach($contacts as $contact) {
    //checa se é email válido
    if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $contact['PreferredEmail'])) {
    //if (!empty($contact['PreferredEmail'])) {
      $friends[] = $contact['PreferredEmail'];
    }
  }

  return $friends;
}

/**
 * filter search results - exclude pages
 */
function evd_search_filter($query) {
  if ($query->is_search) {
      $query->set('post_type', 'post');
  }

  return $query;
}

add_filter('pre_get_posts','evd_search_filter');

/**
 * Printa os estados em uma variável javascript - states. Usado para os novos forms
 * de assinaturas múltiplas
 */
function evd_states_to_javascript() {
  echo '<script type="text/javascript">';
  echo 'var states = [';

  $states = array();
  foreach (evd_states() as $state) {
    $states[] = '"' . $state . '"';
  }

  echo implode(',', $states);

  echo '];</script>';
}

/**
 * Salva os campos de assinatura multiplo.
 */
function evd_multiple_form_control() {
  if (evd_is_post()) {
    $invalids  = array ();
    $signatures = '';

    unset($_SESSION['evd_sign']);

    $total = $_POST['total-form'];

    for ($i = 1; $i <= $total; $i++) {
      $name = !empty($_POST['sign-name-' . $i]) ? $_POST['sign-name-' . $i] : null;
      $email = !empty($_POST['sign-email-' . $i]) ? $_POST['sign-email-' . $i] : null;
      $identity = !empty($_POST['sign-rg-' . $i]) ? $_POST['sign-rg-' . $i] : null;
      $city = !empty($_POST['sign-city-' . $i]) ? $_POST['sign-city-' . $i] : null;
      $state = !empty($_POST['sign-state-' . $i]) ? $_POST['sign-state-' . $i] : null;
      $phone = !empty($_POST['sign-phone-' . $i]) ? $_POST['sign-phone-' . $i] : null;
      $volunteer = !empty($_POST['sign-volunteer-' . $i]);
      $newsletter = !empty($_POST['sign-newsletter-' . $i]);
      $sms = !empty($_POST['sign-sms-' . $i]);

      //checa campos em branco
      if (empty($name) || empty($identity) || empty($city) || empty($state)) {
        $identifier = (!empty($name)) ? $name : $identity;

        if (empty($identifier)) {
          $identifier = 'Não identificado';
        }

        $invalids[] = 'Campos obrigatórios em branco na assinatura de: ' . $identifier;
      }

      //checa email inválido
      if (!empty($email) && evd_petition_email_exists($email)) {
        $invalids[] = 'Email já está cadastrado: ' . $email;
      }

      $sign = compact('name', 'email', 'identity', 'city', 'state', 'sms', 'volunteer', 'newsletter', 'phone');

      if (empty($invalids)) {
        evd_petition_insert($sign);
        
        if (!empty($email)) {
          evd_sign_mail($email);
          if ($volunteer == 1) {
            evd_volunteer_mail($email);
          }
        }

        $signatures .= "

Nome: {$name}

RG: {$identity}";
      }
    }

    if (!empty($signatures)) {
      // Notifying EVD

      $content = "Olá!



Novas assinaturas foram incluídas na petição pelo Voto Distrital.



{$signatures}";

      $content = nl2br($content);
      $subject = 'Assinaturas recebidas';

      return evd_mail(EVD_MAIL, $subject, $content);
    }

    return $invalids;
  }
}

/**
 * Valida Captcha
 *
 * @param string $challenge - valor do captcha
 * @param string $response - valor entrado pelo usuário
 */
function evd_validate_captcha($challenge, $response) {
  require_once 'recaptchalib.php';

  $result = recaptcha_check_answer(EVD_RECAPTCHA_PRIVATE, $_SERVER['REMOTE_ADDR'], $challenge, $response);

  if (!$result->is_valid) {
    return false;
  }

  return true;
}

/**
 * Salva os campos de assinatura multiplo.
 */
function evd_legislators_form_control() {
  global $wpdb;
  
  if (evd_is_post()) {
    $name = !empty($_POST['name1']) ? $_POST['name1'] : null;
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $city = !empty($_POST['city']) ? $_POST['city'] : '';
    $state = !empty($_POST['state']) ? $_POST['state'] : '';
      
    $legislatorId = !empty($_POST['legislator-id']) ? $_POST['legislator-id'] : null;

    if ($legislatorId === null || $name === null || $email === null) {
      return false;
    }

    //validate email
    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
      return false;
    }

    //get email from db    
    $sql = "SELECT `name`,`email` FROM `" . $wpdb->prefix . 'evd_legislators' . "` WHERE `id`='" . $legislatorId . "'";
    $result = $wpdb->get_results($sql, 'ARRAY_A');

    $legislatorName = $result[0]['name'];
    $legislatorEmail = $result[0]['email'];

    $subject = 'Voto Distrital, apoie esta ideia';

    $content = "Prezado parlamentar,

O Brasil precisa urgentemente de uma reforma política que contemple o voto distrital. É uma escolha que colocará o País no caminho da ética na política e do combate à corrupção. O voto distrital é um sistema eleitoral que reforça os laços entre o eleitor e o parlamentar, que passa a prestar contas do seu trabalho e responder por demandas específicas à comunidade que pertence.

Acredito nos benefícios deste sistema eleitoral para a nação, por isso escrevo-lhe esta mensagem pedindo para que batalhe comigo pelo voto distrital, sugerindo, elaborando e aprovando quaisquer iniciativas que contribuam para sua implementação.

Atenciosamente,
{$name}
{$email}

* Esta mensagem foi enviada individualmente através do site do movimento #EuVotoDistrital
  ";

   $content = nl2br($content);

   return evd_mail($legislatorEmail, $subject, $content, EVD_FROM, $name);
  } else {
    return -1;
  }
}

/**
 * Mostra videos do Youtube na página de multimidia
 *
 * @param int $total - numero de videos para mostrar, default 5
 */
function evd_show_youtube_videos($total = 5, $videosUrl='http://gdata.youtube.com/feeds/users/uploads') {
  if (defined('WP_ZEND_GDATA_INTERFACES') && constant('WP_ZEND_GDATA_INTERFACES')) {
    Zend_Loader::loadClass('Zend_Gdata_YouTube');
    
    $yt = new Zend_Gdata_YouTube();

    //define a query para os videos
    $ytQuery = $yt->newVideoQuery($videosUrl);
    $ytQuery->setMaxResults($total);
    $ytQuery->setOrderBy('published');
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);

    $videos = $yt->getVideoFeed($ytQuery);

    foreach ($videos as $video) {
      $id = $video->getVideoId();
      $title = $video->getVideoTitle();
      $description = $video->getVideoDescription();
      $thumbnails = $video->getVideoThumbnails();

      $html = '<div class="media">';
      $html .= '<a href="#" rel="' . $id . '" class="thumb video"><img src="' . $thumbnails[2]['url'] . '" alt="'. $title . '" title="' . $title . '" rel="' . $description . '" /></a>';
      $html .= '<div class="info">';
      $html .= '<h3>' . $title . '</h3>';
      $html .= '<p>' . $description . '</p>';
      $html .= '<p class="link"><a href="">Assista o vídeo</a></p></div></div>';

      echo $html;
    }
  }
}

/**
 * Mostra fotos do Facebook na página de multimidia
 *
 * @param int $total - numero de albuns para mostrar, default 5
 */
function evd_show_facebook_photos($total = 5) {
  include_once get_template_directory() . '/../../../libraries/facebook/facebook.php';

  $facebookClient = new Facebook(array(
    'appId'    => '244005038965775',
    'secret'   => '7abb06c81cdc5bc11a77ab05d354dbbd'
  ));

  $albums = $facebookClient->api('/euvotodistrital/albums?limit=' . $total, 'GET', array());

  $albums = $albums['data'];

  if (is_array($albums) && !empty($albums)) {
    foreach($albums as $album) {
      if ($album['id'] !== '173322979387960' && $album['id'] !== '133805943339664') {
        //pega a foto da capa
        $photos = $facebookClient->api('/' . $album['id'] . '/photos', 'GET', array());

        $photos = $photos['data'];

        $urls = array();

        if (is_array($photos) && !empty($photos)) {
          foreach ($photos as $photo) {
            if ($photo['id'] == $album['cover_photo']) {
              $cover = $photo['images'][2]['source'];
            }

            $urls[] = $photo['images'][2]['source'];
          }

          $urls = implode(',', $urls);
        }
        
        $html = '<div class="media">';
        $html .= '<a href="' . $album['link'] . '" class="thumb img"><img src="' . $cover . '" title="' . $album['name'] . '" alt="' . $album['name'] . '" rel="' . $album['description'] . '" width="120" height="90" /></a>';
        $html .= '<input type="hidden" class="all-photos" value="' . $urls . '" />';
        $html .= '<div class="info">';
        $html .= '<h3>' . $album['name'] . '</h3>';
        $html .= '<p>' . $album['description'] . '</p>';
        $html .= '<p class="link"><a href="' . $album['link'] . '">Veja as fotos</a></p></div></div>';

        echo $html;
      }
    }
  }
}

/*
 *
 */

function curl_request($url, $params = null, $headers = null)
  {
    if (!is_array($params)) {
      $params = array();
    }
    if (!is_array($headers)) {
      $headers = array();
    }
    $options = array(
      CURLOPT_HEADER => 0,
      CURLOPT_URL => $url . '?' . http_build_query($params),
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_FRESH_CONNECT => 1,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_FORBID_REUSE => 1,
      CURLOPT_TIMEOUT => 15,
      CURLOPT_FOLLOWLOCATION => 1,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    return $result;
  }

/*
 *
 */
function evd_show_twitter_followers() {
  $url_twitter = 'https://api.twitter.com/1/';
  
  $url_followers = $url_twitter . 'followers/ids.json';
  $url_lookup = $url_twitter . 'users/lookup.json';
  
  //https://api.twitter.com/1/followers/ids.json?cursor=-1&screen_name=euvotodistrital&stringify_ids=true
  $curl_followers = curl_request($url_followers, array('cursor'=>'-1', 'screen_name'=>'euvotodistrital', 'stringfy_ids'=>false));
  $json_followers = json_decode($curl_followers);
  
  $json_followers->ids;
  //https://api.twitter.com/1/users/lookup.json?user_id=49127807&include_entities=true
  
}