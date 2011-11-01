<?php $result = (int) evd_sign_form_control(); ?>
<?php get_header(); ?>
<?php if (have_posts()) the_post(); ?>
    <div class="wrap">
      <article>
        <h1><?php the_title(); ?></h1>
        <?php edit_post_link( __( 'Edit' ), '<p class="edit">[', ']</p>'); ?>
        <?php if ($result == -1) : ?>
        <h4>De acordo com os nossos registros, você já assinou essa petição pelo voto distrital.</h4>
        <div class="content">
          <p>Se você já assinou, saiba que pode fazer mais coisas pelo movimento, acesse a página <a href="<?php echo home_url('mobilize'); ?>" title="Mobilize">Mobilize.</p>
        </div>
        <?php else : ?>
        <div class="box">
          <div class="votes">
            <?php $goal = (int) get_option('evd_petition_goal'); ?>
            <?php $total = evd_petition_count(false); ?>
            <?php $width = ceil($total / $goal * 100); ?>
            <h4><span style="width:<?php echo $width; ?>%"><?php echo number_format($total, 0, '', '.'); ?></span></h4>
            <p>já assinaram a favor do voto distrital. Precisamos de <strong><?php echo number_format($goal, 0, '', '.'); ?></strong>! Topa o desafio?</p>
          </div>
        </div>
        <?php if (get_post($id = 0)->post_excerpt) : ?>
        <h4><?php the_excerpt(); ?></h4>
        <?php endif; ?>
        <?php the_content(); ?>
        <?php endif; ?>
      </article>
    </div>
<?php get_footer(); ?>