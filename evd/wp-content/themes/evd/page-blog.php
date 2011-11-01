<?php get_header(); ?>
<?php
  //get current page and show 5 posts from it
  global $paged;
  query_posts('post_type=post&posts_per_page=5&paged=' . $paged); ?>
    <div class="wrap">
      <section class="posts">
        <?php while (have_posts()) : the_post(); ?>
        <article>
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php edit_post_link( __( 'Edit' ), '<p class="edit">[', ']</p>'); ?>
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
                  <a href="<?php the_permalink(); ?>" rel="<?php the_title(); ?>" title="Enviar o link dessa página por e-mail">E-mail</a>
                </li>
              </ul>
            </div>
            <?php
              echo addthis_call_social_widget();
            ?>
          </nav>
          <?php
                        //Pega o numero de comentários pela tag do disqus <span...>XX</span>
            $disqusOutput = get_comments_number();
            preg_match('/^<.*>(\d*)<.*>$/', $disqusOutput, $matches);
            $totalComments = $matches[1];

            $commentText = 'Comentar';

            if ($totalComments == 1) {
              $commentText = '1 Comentário';
            }

            if ($totalComments > 1) {
              $commentText = $totalComments . ' Comentários';
            }
          ?>
          <a class="comment" href="<?php comments_link() ?>"><?php echo $commentText; ?></a>
          <span class="info"><?php echo get_the_date('d/M'); ?> | <?php foreach (get_the_category() as $category) : ?> <a href="<?php echo get_category_link($category->cat_ID); ?>" title="Mais posts da categoria <?php echo $category->cat_name; ?>">#<?php echo $category->cat_name; ?></a><?php endforeach; ?></span>
        </article>
        <?php endwhile; ?>
        <div class="paging">
          <span class="prev">
            <?php echo next_posts_link('Posts anteriores'); ?>
          </span>
          <span class="next">
            <?php echo previous_posts_link('Posts mais recentes'); ?>
          </span>
        </div>
        <?php wp_reset_query(); ?>
      </section>
      <div id="email-modal">
        <form action="#" method="post">
          <ol>
            <li class="btn-close">
              <a href="" title="Fechar">[Fechar]</a>
            </li>
            <li>
              <label>De (Nome):</label>
              <input type="text" name="post-from" />
            </li>
            <li>
              <label>Para (E-mails separados por vírgula):</label>
              <input type="text" name="post-to" />
            </li>
            <li>
              <label>Mensagem (Opcional):</label>
              <textarea name="post-message"></textarea>
            </li>
            <li>
              <button type="button">Enviar</button>
            </li>
            <li>
              <p>Envie direto do seu e-mail:</p>
              <a href="#" target="_blank" id="post-gmail">Gmail</a>
              <a href="#" target="_blank" id="post-hotmail">Hotmail</a>
            </li>
          </ol>
        </form>
      </div>
      <?php get_sidebar(); ?>
    </div>
<?php get_footer(); ?>