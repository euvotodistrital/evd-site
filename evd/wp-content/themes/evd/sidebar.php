<aside class="sidebar">
  <?php if (is_page('o-movimento')) : ?>
  <div class="box-simple">
    <h3><span>Valores</span></h3>
    <p><strong>Rede Distribuída</strong></p>
    <p>Somos um movimento de pessoas e não funcionamos hierarquicamente. Acreditamos que quanto mais distribuída for a rede mais democrático seremos. As redes distribuídas proporcionam conexões ilimitadas entre pessoas – esse principio garante a duração e fortalecimento do movimento, pois dependemos apenas de pessoas interagindo e não de uma estrutura.</p>
    <p><strong>Comunicação Não-Violenta</strong></p>
    <p>Inspirado nos movimentos não-violentos, o movimento #euvotodistrital usa a comunicação não–violenta para expressar suas ideias, propostas, visão, argumentos e opinião. Estamos cansados da política de quem grita mais alto e de discussões que terminam em ataques pessoais. Não xingamos quem é contra nós. Não gritamos. Não julgamos quem pensa diferente.</p>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-movimento', 'imprensa','multimidia'))) : ?>
  <div class="box">
    <h3><span>O que é o&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;Voto Distrital?</span></h3>
    <a class="movement" href="<?php echo home_url('voto-distrital/o-que-e'); ?>">Entenda como ele funciona</a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-movimento', 'multimidia'))) : ?>
  <div class="box" id="box-supporters">
    <h3><span>Apoiadores</span></h3>
    <a class="more-highlight" href="<?php echo home_url('o-movimento/apoiadores'); ?>" title="Apoiadores"><span>Veja quem apoia o Voto Distrital</span></a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('duvidas-comuns', 'faq'))) : ?>
  <?php if (($sent = evd_question_form_control()) !== null) : ?>
  <script type="text/javascript">
    <?php if ($sent) : ?>
    var msg = 'Sua pergunta foi enviada com sucesso. Obrigado!';
    <?php else : ?>
    var msg = 'Ops! Algo estranho aconteceu enquanto enviávamos a sua pergunta. Por favor, verifique os campos e tente novamente.';
    <?php endif; ?>
    window.alert(msg);
  </script>
  <?php endif; ?>
  <div class="box">
    <h3><span>Pergunte</span></h3>
    <form action="<?php the_permalink(); ?>" method="post">
      <fieldset>
        <ol>
          <li>
            <label for="question-name">Nome:</label>
            <input type="text" class="text" id="question-name" name="name_" />
          </li>
          <li>
            <label for="question-email">E-mail:</label>
            <input type="text" class="text" id="question-email" name="email" />
          </li>
          <li>
            <label for="question-question">Pergunta:</label>
            <textarea id="question-question" name="question" cols="30" rows="5"></textarea>
          </li>
          <li>
            <button type="submit">Enviar</button>
          </li>
        </ol>
      </fieldset>
    </form>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-e', 'duvidas-comuns', 'contato', 'biblioteca'))) : ?>
  <div class="box">
    <h3><span>10 Motivos para&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;votar distrital</span></h3>
    <ol class="why-slider">
      <li class="one first">
        <a href="#1" title="Primeiro motivo">#1</a>
        <span><span>Quero um representante que defenda os interesses do meu distrito e que não esteja no Legislativo para defender interesses próprios ou de corporações.</span></span>
      </li>
      <li class="two">
        <a href="#2" title="Segundo motivo">#2</a>
        <span><span>Quero ter o poder de fiscalizar de perto a atuação do meu representante e de cobrar o seu desempenho.</span></span>
      </li>
      <li class="three">
        <a href="#3" title="Terceiro motivo">#3</a>
        <span><span>Quero ajudar a reduzir o gasto de campanhas e a combater a corrupção e o caixa-2.</span></span>
      </li>
      <li class="four">
        <a href="#4" title="Quarto motivo">#4</a>
        <span><span>Quero que meu voto sirva apenas para eleger meu representante. E não candidatos que não conseguem o número de votos necessários para se elegerem pelos seus próprios méritos.</span></span>
      </li>
      <li class="five">
        <a href="#5" title="Quinto motivo">#5</a>
        <span><span>Quero um Legislativo forte e democrático, que recupere a credibilidade para legislar em prol do interesse público e não em causa própria.</span></span>
      </li>
      <li class="six">
        <a href="#6" title="Sexto motivo">#6</a>
        <span><span>Quero votar no candidato de minha própria escolha. Não quero o voto em lista fechada, dando às oligarquias partidárias o direito de escolher quem será eleito.</span></span>
      </li>
      <li class="seven">
        <a href="#7" title="Sétimo motivo">#7</a>
        <span><span>Quero ter orgulho do meu representante pelo trabalho que presta ao meu distrito e ao país. Não quero ser um eleitor que esquece em quem votou em um mês após as eleições.</span></span>
      </li>
      <li class="eight">
        <a href="#8" title="Oitavo motivo">#8</a>
        <span><span>Quero que as lideranças locais tenham capacidade de disputar as eleições, com chances reais de vitória.</span></span>
      </li>
      <li class="nine">
        <a href="#9" title="Nono motivo">#9</a>
        <span><span>Não quero dinheiro público financiando campanha eleitoral. Quero o dinheiro público financiando serviços públicos de qualidade para o povo brasileiro.</span></span>
      </li>
      <li class="ten last">
        <a href="#10" title="Décimo motivo">#10</a>
        <span><span>Quero um sistema eleitoral que ajude a formar novas lideranças.</span></span>
      </li>
    </ol>
    <a class="more" href="<?php echo home_url('voto-distrital/porque-votar-distrital'); ?>" title="Saiba mais">Saiba mais</a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-e', 'porque-votar-distrital', 'faq', 'imprensa'))) : ?>
  <div class="box">
    <h3><span>Biblioteca</span></h3>
    <a class="more-highlight" href="<?php echo home_url('voto-distrital/biblioteca'); ?>" title="Biblioteca"><span>Visite nossa biblioteca sobre o Voto Distrital</span></a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-e', 'porque-votar-distrital', 'biblioteca'))) : ?>
  <div class="box">
    <h3><span>Dúvidas comuns?</span></h3>
    <a class="questions" href="<?php echo home_url('voto-distrital/duvidas-comuns'); ?>">Encontre aqui as respostas</a>
  </div>
  <?php endif; ?>
  <?php if (is_page('assine')) : ?>
  <div class="box">
    <form action="<?php echo home_url('assine/assinar'); ?>" method="post">
      <?php evd_sign_fieldset(); ?>
    </form>
  </div>
  <?php endif; ?>
  <?php if (is_page('assine')) : ?>
  <div class="box">
    <h3><span>Divulgue esta&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;página</span></h3>
    <p><strong>Escolha uma forma de divulgação abaixo e compartilhe na rede:</strong></p>
    <?php echo do_shortcode('[social_share]'); ?>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-posso-fazer-agora', 'caixa-de-ferramentas', 'grupos-de-acao', 'fale-com-os-deputados'))) : ?>
  <div class="box">
    <h3><span>Apresentação</span></h3>
    <p></p>
    <a class="more" href="http://db.tt/TikvsJS" title="Apresentação para download" rel="external">Apresentação para download</a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-posso-fazer-agora', 'grupos-de-acao', 'fale-com-os-deputados'))) : ?>
  <div class="box">
    <h3><span>Caixa de&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;ferramentas</span></h3>
    <p>Aumente seu poder de mobilização!</p>
    <a class="more" href="<?php echo home_url('mobilize/caixa-de-ferramentas'); ?>" title="Saiba mais">Saiba mais</a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-posso-fazer-agora', 'caixa-de-ferramentas', 'fale-com-os-deputados'))) : ?>
  <div class="box">
    <h3><span>Ficha de&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;assinaturas&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;offline</span></h3>
    <p>Para você imprimir e recolher assinaturas!</p>
    <a class="more" href="<?php bloginfo('template_directory'); ?>/inscricao-voto-distrital.pdf" title="Faça o download da ficha de assinaturas offline">Faça o download</a>
  </div>
  <?php endif; ?>
  <?php if (is_page(array('o-que-posso-fazer-agora', 'caixa-de-ferramentas', 'imprensa', 'fale-com-os-deputados'))) : ?>
  <div class="box">
    <h3><span>Grupos de ação</span></h3>
    <p>Entre em nos grupos de ação e troque suas experiências com outras pessoas do movimento:</p>
    <dl>
      <dt>São Paulo</dt>
      <dd><a href="http://www.facebook.com/home.php?sk=group_210644388957873&ap=1" rel="external">Grupo no Facebook EVD SP</a></dd>
      <dt>Rio de Janeiro</dt>
      <dd><a href="http://www.facebook.com/home.php?sk=group_211825555506294&ap=1" rel="external">Grupo no Facebook EVD RJ</a></dd>
      <dt>Minas Gerais</dt>
      <dd><a href="http://www.facebook.com/home.php?sk=group_209544535743285&ap=1" rel="external">Grupo no Facebook EVD MG</a></dd>
    </dl>
    <a class="more" href="<?php echo home_url('mobilize/grupos-de-acao'); ?>" title="Saiba mais">Saiba mais</a>
  </div>
  <?php endif; ?>
  <?php if (is_page('blog') || !(is_page() || is_home())) : ?>
  <div class="box-simple">
    <h3><span>Busca</span></h3>
    <form action="<?php echo home_url(); ?>" method="get">
      <fieldset>
        <ol>
          <li>
            <label class="hide" for="busca-s">Buscar por:</label>
            <input type="text" class="text" id="search-s" name="s" value="<?php echo get_search_query(); ?>" />
          </li>
          <li class="actions">
            <button type="submit">Ir &rarr;</button>
          </li>
        </ol>
      </fieldset>
    </form>
  </div>
  <div class="box-simple">
    <h3><span>Categorias</span></h3>
    <ul class="categories">
      <?php foreach (get_categories() as $category) : ?>
      <li>
        <a href="<?php echo get_category_link($category->cat_ID); ?>" title="<?php echo $category->cat_name; ?>">#<?php echo $category->cat_name; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="box-simple">
    <h3><span>Posts em destaque</span></h3>
    <dl class="blog-highlights">
      <?php
      if (function_exists('featured_posts')) {
        featured_posts('add');
      }

      query_posts('posts_per_page=2');
      $i = 1;

      while ( have_posts() ) : the_post();?>
        <dt <?php if ($i == 2) : ?>class="last"<?php endif; ?>><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></dt>
        <dd <?php if ($i == 2) : ?>class="last"<?php endif; ?>>
          <p><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_excerpt(); ?></a></p>
          <span><?php echo get_the_date('M/y'); ?> | <?php foreach (get_the_category() as $category) : ?> <a href="<?php echo get_category_link($category->cat_ID); ?>" title="Mais posts da categoria <?php echo $category->cat_name; ?>">#<?php echo $category->cat_name; ?></a><?php endforeach; ?></span>
        </dd>
      <?php $i++; endwhile; // end of the loop.

      if (function_exists('featured_posts')) {
        featured_posts('remove');
      }
      ?>
    </dl>
  </div>
  <div class="box-simple">
    <h3><span>Tags</span></h3>
    <nav class="tags">
      <?php wp_tag_cloud(); ?>
    </nav>
  </div>
  <?php endif; ?>
</aside>