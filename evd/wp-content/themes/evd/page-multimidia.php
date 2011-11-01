<?php get_header(); ?>
    <div class="wrap">
      <article id="multimedia">
        <h2>Videos</h2>
        <?php
          evd_show_youtube_videos();
        ?>
        <a href="http://www.youtube.com/EuVotoDistrital" class="more">+ Veja todos os videos no Youtube</a>

        <h2>Fotos</h2>
        <?php
          evd_show_facebook_photos();
        ?>
        <a href="http://www.facebook.com/euvotodistrital?sk=photos" class="more">+ Veja todas as fotos no Facebook</a>
        </article>
      <?php get_sidebar(); ?>
    </div>
    <div id="multimedia-modal">
      <a href="#" class="bt-close">[Fechar]</a>
      <h2></h2>
      <p></p>
      <div></div>
    </div>
<?php get_footer(); ?>