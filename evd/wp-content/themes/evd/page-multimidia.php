<?php get_header(); ?>
    <div class="wrap">
      <article id="multimedia">
        <?php
          $user = 'EuVotoDistrital';
          $userVideosUrl = 'http://gdata.youtube.com/feeds/users/' . $user . '/uploads';
          $favoritesVideosUrl = 'http://gdata.youtube.com/feeds/api/users/' . $user . '/favorites';
        ?>
        <h2>Videos (Uploads)</h2>
        <?php
          evd_show_youtube_videos('5', $userVideosUrl);
        ?>
        <h2>Videos (Favoritos)</h2>
        <?php
          evd_show_youtube_videos('5', $favoritesVideosUrl);
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