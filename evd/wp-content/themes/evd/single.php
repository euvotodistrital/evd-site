<?php get_header(); ?>
    <div class="wrap">
      <section class="posts">
        <?php if (have_posts()) the_post(); ?>
        <article>
          <h2><?php the_title(); ?></h2>
          <?php if (get_post($id = 0)->post_excerpt) : ?>
          <h4><?php the_excerpt(); ?></h4>
          <?php endif; ?>
          <div class="content">
            <?php the_content(); ?>
          </div>
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
          <span class="info"><?php echo get_the_date('d/M'); ?> | <?php foreach (get_the_category() as $category) : ?> <a href="<?php echo get_category_link($category->cat_ID); ?>" title="Mais posts da categoria <?php echo $category->cat_name; ?>">#<?php echo $category->cat_name; ?></a><?php endforeach; ?></span>
          <div id="comments">
            <?php comments_template(); ?>
          </div>
        </article>
      </section>
      <?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>