<?php get_header(); ?>
<?php if (have_posts()) the_post(); ?>
    <div class="wrap">
      <article>
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
            echo addthis_call_social_widget();
          ?>
        </nav>
        <?php endif; ?>
        <?php if (get_post($id = 0)->post_excerpt) : ?>
        <h4><?php the_excerpt(); ?></h4>
        <?php endif; ?>
        <div class="content">
          <?php the_content(); ?>
        </div>
      </article>
      <?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>