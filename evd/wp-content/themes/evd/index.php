<?php get_header(); ?>
    <div class="wrap">
      <section id="petition" class="box">
        <p>O Voto Distrital é um sistema eleitoral que permite fiscalizar de 
            perto o político eleito e assim podemos realizar as mudanças que 
            desejamos ver no Brasil. É o cidadão com mais poder de decisão. 
            Conheça. Assine. Mobilize.</p>
        <div class="media-wrap">
          <!--<iframe title="YouTube video player" width="672" height="408" src="http://www.youtube.com/embed/URWJYMoBOoI?wmode=transparent" frameborder="0" allowfullscreen></iframe>-->
          <?php if(function_exists('meteor_slideshow')) { meteor_slideshow(); } ?>
        </div>
        <form id="sign-form" action="<?php echo home_url('assine/assinar'); ?>" method="post">
          <?php evd_sign_fieldset(); ?>
        </form>
      </section>
     <aside class="boxes">
        <div class="box why">
          <h3><span>Por que #euvotodistrital?</span></h3>
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
        <div class="box mobilize">
          <h3><span>Fotos e Vídeos</span></h3>
          <ul>
            <li class="first">
              <img src="<?php bloginfo('template_directory'); ?>/images/home-mobilize-1.jpg" alt="Mobilize" />
            </li>
            <li>
              <img src="<?php bloginfo('template_directory'); ?>/images/home-mobilize-2.jpg" alt="Mobilize" />
            </li>
            <li>
              <img src="<?php bloginfo('template_directory'); ?>/images/home-mobilize-3.jpg" alt="Mobilize" />
            </li>
          </ul>
          <strong><a href="<?php echo home_url('o-movimento/multimidia'); ?>" title="Veja nossas últimas imagens e vídeos"><span>Veja nossas últimas imagens e vídeos</span></a></strong>
        </div>
        <div class="box blog">
          <h3><span>Blog</span></h3>
          <dl class="blog-highlights">
            <?php $i = 1; foreach (get_posts('numberposts=2&exclude=512') as $post) : setup_postdata($post); ?>
            <dt <?php if ($i == 2) : ?>class="last"<?php endif; ?>><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></dt>
            <dd <?php if ($i == 2) : ?>class="last"<?php endif; ?>>
              <p><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_excerpt(); ?></a></p>
              <span><?php echo get_the_date('d/M'); ?> | <?php foreach (get_the_category() as $category) : ?> <a href="<?php echo get_category_link($category->cat_ID); ?>" title="Mais posts da categoria <?php echo $category->cat_name; ?>">#<?php echo $category->cat_name; ?></a><?php endforeach; ?></span>
            </dd>
            <?php $i++; endforeach; ?>
          </dl>
        </div>
        <div class="box social">
          <h3><span>Social</span></h3>
          <ul>
            <li class="first">
              <a href="#update-all" title="Ver todas as atualizações">Tudo</a>
            </li>
            <li>
              <a href="#update-facebook" title="Ver as atualizações do Facebook">Facebook</a>
            </li>
            <li class="last">
              <a href="#update-twitter" title="Ver as atualizações do Twitter">Twitter</a>
            </li>
          </ul>
          <div class="updates" id="update-all">
            <?php $updates = evd_social_all(); ?>
            <?php if (count($updates) > 0) : ?>
            <ul>
              <?php $i = 1; foreach ($updates as $update) : ?>
              <li class="<?php if ($i == 1) : ?>first<?php elseif ($i == count($updates)) : ?>last<?php endif; ?>">
                <?php if (!empty($update->created_at)) : ?>
                <a href="http://www.twitter.com/<?php echo $update->from_user; ?>" title="<?php echo $update->from_user; ?>"><img src="<?php echo $update->profile_image_url; ?>" alt="<?php echo $update->from_user; ?>" /></a>
                <span>
                  <?php echo $update->text; ?>
                </span>
                <span class="info"><?php echo mysql2date('d/M', $update->created_at); ?> | via Twitter</span>
                <?php else : ?>
                <a href="http://www.facebook.com/profile.php?id=<?php echo $update->from->id; ?>" title="<?php echo $update->from->name; ?>"><img src="http://graph.facebook.com/<?php echo $update->from->id; ?>/picture" alt="<?php echo $update->from->name; ?>" /></a>
                <span>
                  <?php if (!empty($update->message)) : ?>
                  <?php echo $update->message; ?>
                  <?php else : ?>
                  <?php if (!empty($update->name)) echo $update->name ?> <?php echo $update->description; ?>
                  <?php endif; ?>
                </span>
                <span class="info"><?php echo mysql2date('d/M', $update->created_time); ?> | via Facebook</span>
                <?php endif; ?>
              </li>
              <?php $i++; endforeach; ?>
            </ul>
            <?php else : ?>
            <ul>
              <li>Ops! Parece que não conseguimos nos conectar ao <strong>Twitter</strong>...</li>
            </ul>
            <?php endif; ?>
          </div>
          <div class="updates" id="update-facebook">
            <?php $updates = evd_social_facebook(); ?>
            <?php if (is_array($updates) && count($updates) > 0) : ?>
            <ul>
              <?php $i = 1; foreach ($updates as $update) : ?>
              <li class="<?php if ($i == 1) : ?>first<?php elseif ($i == count($updates)) : ?>last<?php endif; ?>">
                <a href="http://www.facebook.com/profile.php?id=<?php echo $update->from->id; ?>" title="<?php echo $update->from->name; ?>"><img src="http://graph.facebook.com/<?php echo $update->from->id; ?>/picture" alt="<?php echo $update->from->name; ?>" /></a>
                <span>
                  <?php if (!empty($update->message)) : ?>
                  <?php echo $update->message; ?>
                  <?php else : ?>
                  <?php if (!empty($update->name)) echo $update->name ?> <?php echo $update->description; ?>
                  <?php endif; ?>
                </span>
                <span class="info"><?php echo mysql2date('d/M', $update->created_time); ?> | via Facebook</span>
              </li>
              <?php $i++; endforeach; ?>
            </ul>
            <?php else : ?>
            <ul>
              <li>Ops! Parece que não conseguimos nos conectar ao <strong>Twitter</strong>...</li>
            </ul>
            <?php endif; ?>
          </div>
          <div class="updates" id="update-twitter">
            <?php $updates = evd_social_twitter(); ?>
            <?php if (is_array($updates) && count($updates) > 0) : ?>
            <ul>
              <?php $i = 1; foreach ($updates as $update) : ?>
              <li class="<?php if ($i == 1) : ?>first<?php elseif ($i == count($updates)) : ?>last<?php endif; ?>">
                <a href="http://www.twitter.com/<?php echo $update->from_user; ?>" title="<?php echo $update->from_user; ?>"><img src="<?php echo $update->profile_image_url; ?>" alt="<?php echo $update->from_user; ?>" /></a>
                <span>
                  <?php echo $update->text; ?>
                </span>
                <span class="info"><?php echo mysql2date('d/M', $update->created_at); ?> | via Twitter</span>
              </li>
              <?php $i++; endforeach; ?>
            </ul>
            <?php else : ?>
            <ul>
              <li>Ops! Parece que não conseguimos nos conectar ao <strong>Twitter</strong>...</li>
            </ul>
            <?php endif; ?>
          </div>
        </div>
      </aside>
    </div>
    <br />&nbsp;
        <center>
            <a href="/o-movimento/na-imprensa/" target="_self"><img src="<?php bloginfo('template_directory'); ?>/images/namidia.png" title="Na imprensa"/></a>
        </center>
<?php get_footer(); ?>
