<?php get_header(); ?>
<?php if (have_posts()) the_post(); ?>
<?php
  //checar se postado
  $result = evd_legislators_form_control();
  
  if ($result === true) { ?>
    <script type="text/javascript">
      alert('E-mail enviado com sucesso.');
    </script>
  <?php } else if ($result === false) { ?>
    <script type="text/javascript">
      alert('Houve um problema no envio do email, verifique se você preencheu os campos corretamente.');
    </script>
  <?php } ?>
<div class="wrap">
  <article>
    <h2>Envie uma mensagem para um parlamentar</h2>
    <?php the_content(); ?>
    <form id="legislators-form" action="<?php echo get_permalink(); ?>" method="post">
      <fieldset class="sign">
        <ol>
          <li class="required">
            <label for="name1">Nome:</label>
            <input type="text" class="text" name="name1" value="" />
          </li>
          <li class="required">
            <label for="email">E-mail:</label>
            <input type="text" class="text" name="email" value="" />
          </li>
          <li>
            <label for="city">Cidade/UF:</label>
            <input type="text" class="text city" name="city" value="" />
            <input type="text" class="text state" name="state" value="" />
          </li>
          <li class="required">
            <label for="legislator-state">Estado:</label>
            <select id="legislator-state" name="legislator-state">
              <option value="">--</option>
              <?php foreach (evd_states() as $state) : ?>
                <option value="<?php echo $state; ?>"><?php echo $state; ?></option>
              <?php endforeach; ?>
            </select>
          </li>
          <li class="required">
            <label for="legislator-name">Parlamentar:</label>
            <select id="legislator-id" name="legislator-id">
              <option value="">--</option>
            </select>
          </li>
          <li id="legislator-message">
            <p><strong>Mensagem enviada:</strong></p>
            <p>Prezado parlamentar,</p>
            <p>O Brasil precisa urgentemente de uma reforma política que contemple o voto distrital. É uma escolha que colocará o País no caminho da ética na política e do combate à corrupção. O voto distrital é um sistema eleitoral que reforça os laços entre o eleitor e o parlamentar, que passa a prestar contas do seu trabalho e responder por demandas específicas à comunidade que pertence.</p>
            <p>Acredito nos benefícios deste sistema eleitoral para a nação, por isso escrevo-lhe esta mensagem pedindo para que batalhe comigo pelo voto distrital, sugerindo, elaborando e aprovando quaisquer iniciativas que contribuam para sua implementação.</p>
            <p>Atenciosamente,</p>
            <p>Seu nome,
            <br />Seu Email</p>
          </li>
        </ol>
      </fieldset>
      <button class="send-message" type="submit">Enviar</button>
    </form>
  </article>
  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
