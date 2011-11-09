<?php get_header(); ?>
<?php if (have_posts()) the_post(); ?>
    <div class="wrap">
      <article class="one-column">
        <h1><?php the_title(); ?></h1>
        <?php edit_post_link( __( 'Edit' ), '<p class="edit">[', ']</p>'); ?>
        <?php if (!is_page(array('contato', 'convide'))) : ?>
        <nav class="utils">
          <div class="first">
            <ul>
              <li class="font-increase first">
                <a href="#font-increase" title="Aumentar o tamanho do texto">Aumentar o tamanho do texto</a>
              </li>
              <li class="font-decrease last">
                <a href="#font-decrease" title="Diminuir o tamanho do texto">Diminuir o tamanho do texto</a>
              </li>
            </ul>
          </div>
          <div>
            <ul>
              <!--<li class="print first">
                <a href="#print" title="Imprimir essa página">Imprimir</a>
              </li>-->
              <li class="email first">
                <a href="#email" title="Enviar o link dessa página por e-mail">E-mail</a>
              </li>
            </ul>
          </div>
          <?php
            echo addthis_call_social_widget(15,15);
          ?>
        </nav>
        <?php endif; ?>
        
        <?php if (get_post($id = 0)->post_excerpt) : ?>
        <h4><?php the_excerpt(); ?></h4>
        <?php endif; ?>
        <div class="content">
          <?php the_content(); ?>
        </div>
        
        <style>
        div.social-stream { height:250px; overflow:auto;}
            div.social-stream a { }
            div.social-stream > ul { margin-top: 15px; list-style: none; }
              div.social-stream > ul li { display: inline; margin-right: 10px; font-weight: bold; }
                div.social-stream > ul li.active a { color: #333; }
              div.social-stream  ul { list-style: none; }
                div.social-stream  ul li { display: block; margin: 5px 0; padding: 8px 0 5px; border-top: 1px solid #e6e6e4; }
                div.social-stream  ul li.first { border-top:0; margin-top:0; padding-top:0;}
                  div.social-stream ul li img { float: left;  }
                  div.social-stream  ul li span { display: block; margin-left: 60px; color: #333; }
                  div.social-stream  ul li span.info { margin-top: 10px; color: #999; }
                    div.social-stream  ul li span a { color: #f47421; font-weight: bold; }
                    div.social-stream  ul li span a:hover { text-decoration: underline; }
        </style>
        
        <h3>Nas Redes Sociais</h3>
        
        <div class="social-stream">
        <?php $updates = evd_social_all(15,15); ?>

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
      </article>
      <?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>