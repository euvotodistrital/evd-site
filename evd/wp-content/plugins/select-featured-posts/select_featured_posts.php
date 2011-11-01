<?php
/*
Plugin Name: Select Featured Posts
Description: A plugin which allows you to select/unselect posts using Ajax and make it as a featured post.
Author: Kapil Chugh
Author URI: http://kapilchugh.wordpress.com/
Plugin URI: http://mediologysoftware.com/wordpress-plugins/
Version: 0.2
*/
  global $wpdb;
  $wpdb->featured_posts = $wpdb->prefix . 'featured_posts';
  register_activation_hook(__FILE__, 'install_featured_post_table');

  function install_featured_post_table () {
    global $wpdb;
    $charset_collate = '';

    if ( ! empty($wpdb->charset) ) {
      $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    }
    if ( ! empty($wpdb->collate) ) {
      $charset_collate .= " COLLATE $wpdb->collate";
    }
    if ( is_admin() ) {
      $wpdb->query( "CREATE TABLE IF NOT EXISTS `{$wpdb->featured_posts}` (
        `featured_post_id` bigint(20) NOT NULL auto_increment,
        `post_id` bigint(20) NOT NULL,
        `created` INT( 11 ) NOT NULL,
        PRIMARY KEY  (`featured_post_id`),
        UNIQUE (`post_id`)
      )$charset_collate;" );
    }
  }

  add_filter('manage_posts_columns', 'add_more_coloum_to_post', 10, 1);

  function add_more_coloum_to_post ($posts_columns) {
    $posts_columns[ 'featured' ] = __( 'Post em destaque' );
    return $posts_columns;
  }

  add_action('manage_posts_custom_column', 'custom_posts_column', 10, 2);
  function custom_posts_column ($column_name, $post_id) {
    if ($column_name == 'featured') {
      $nonce = wp_create_nonce( 'featured_post' ); ?>
      <script  type='text/javascript'>
      <!--
      function add_featured_post(post_id) {
        if (!isNaN(post_id)) {
          if (!jQuery('#' + post_id).is(':checked')) {
          	var delete_stat  = 1;
          }
          jQuery.ajax({
            type: "post",url: "admin-ajax.php",data: { action: 'add_featured_post_id', post_id: post_id, action_delete: delete_stat, _ajax_nonce: '<?php echo $nonce; ?>' },
            beforeSend: function() {jQuery('#' + <?php echo $post_id; ?>).fadeOut('fast');}, //fadeIn loading just when link is clicked
            success: function(html){ //so, if data is retrieved, store it in html
              alert(html);
            }
          }); //close jQuery.ajax
        } else {
          alert('Selecione um post válido.');
        }
      }
      -->
      </script> <?php
      global $wpdb, $post;
      $checked = '';
      if ($post->post_status == 'publish' && $post->post_password == '' && $post->post_type == 'post') {
        $res = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->featured_posts WHERE post_id = $post_id"));
        if ($res) {
          $checked = 'checked="true"';
        } else {
          $checked = '';
        } ?>
        <input type="checkbox" <?php echo $checked; ?> name="featured_post" onclick="add_featured_post(this.value)" id="<?php echo $post_id; ?>" value="<?php echo $post_id; ?>" /> <?php
      }
    }
  }

  add_action( 'wp_ajax_add_featured_post_id', 'add_featured_post_id' );
  function add_featured_post_id () {
    global $wpdb,$post;
    check_ajax_referer( "featured_post" );
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $action_delete = isset($_POST['action_delete']) ? $_POST['action_delete'] : '';
    if (!(int)$post_id) {
      return false;
    }
    if ($action_delete == 1) {
      $success = $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->featured_posts WHERE post_id = $post_id "));
    } else {
      $success = $wpdb->insert($wpdb->featured_posts, array( 'post_id' => $post_id, 'created' => time() ), array( '%d', '%d' ) );
    }
    if ($success) {
      echo 'Post atualizado.';
    } else {
      echo 'Ocorreu um erro, tente de novo por favor.';
    }
    exit;
  }

  /**
   * Use this function in theme
   *
   * value of $value variable will be add or remove
   *
   * To use this function in theme use this before query_posts
   * if (function_exists('featured_posts')) {
   *    featured_posts('add');
   * }
   *
   * After query_posts add this
   * if (function_exists('featured_posts')) {
   *   featured_posts('remove');
   * }
   **/
  function featured_posts ($value) {
    if ($value == 'add') {
      add_filter('posts_join', 'featured_posts_join' );
      add_filter('posts_orderby', 'featured_posts_orderby');
    } elseif ($value == 'remove') {
      remove_filter('posts_join', 'featured_posts_join' );
      remove_filter('posts_orderby', 'featured_posts_orderby');
    }
  }

  function featured_posts_join( $join ) {
    global $wpdb;
    $join .= " INNER JOIN $wpdb->featured_posts ON $wpdb->posts.ID  = $wpdb->featured_posts.post_id ";
    return $join;
  }

  function featured_posts_orderby( $orderby ) {
    global $wpdb;
    return "$wpdb->featured_posts.created DESC";
  }

  function get_featured_posts($limit = 10) {
    if (!(int)$limit) {
      return false;
    }
    global $wpdb;
    return $wpdb->get_results($wpdb->prepare("SELECT post_id, created FROM $wpdb->featured_posts ORDER BY created DESC LIMIT $limit"));
  }

  add_action( 'widgets_init', 'featured_load_widgets' );

  function featured_load_widgets() {
    register_widget( 'WP_Widget_Featured_Posts' );
  }

  class WP_Widget_Featured_Posts extends WP_Widget {

    function WP_Widget_Featured_Posts() {
      $widget_ops = array('classname' => 'widget_featured_entries', 'description' => __( "It displays featured posts on your site") );
      $this->WP_Widget('featured-posts', __('Select Featured Posts'), $widget_ops);
      $this->alt_option_name = 'widget_featured_entries';


    }

    function widget($args, $instance) {
      extract($args);

      $title = apply_filters('widget_title', empty($instance['title']) ? __('Select Featured Posts') : $instance['title'], $instance, $this->id_base);
      if ( !$number = (int) $instance['number'] )
        $number = 10;
      else if ( $number < 1 )
        $number = 1;
      else if ( $number > 15 )
        $number = 15;

      if (function_exists('featured_posts')) {
        featured_posts('add');
      }
      if (isset($instance['cat'])) {
        $r = new WP_Query(array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1, 'cat' => $instance["cat"]));
      } else {
        $r = new WP_Query(array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
      }
      if ($r->have_posts()) {
      echo $before_widget;
      if ( $title ) echo $before_title . $title . $after_title; ?>
        <ul>  <?php
          while ($r->have_posts()) : $r->the_post(); ?>
            <li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li> <?php
          endwhile; ?>
        </ul> <?php
        echo $after_widget;
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();
      } else {
        echo 'Nenhum post encontrado';
      }

      if (function_exists('featured_posts')) {
        featured_posts('remove');
      }
    }

    function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['number'] = (int) $new_instance['number'];
      $instance['cat'] = (int) $new_instance['cat'];

      return $instance;
    }

    function form( $instance ) {
      $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
      $cat = isset($instance['cat']) ? $instance['cat'] : '';
      if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
        $number = 5; ?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

      <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
      <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
      <p>
        <label>
          <?php _e( 'Category' ); ?>:
          <?php wp_dropdown_categories( array( 'show_option_all' => 'Select a Category', 'name' => $this->get_field_name("cat"), 'selected' => $cat ) ); ?>
        </label>
      </p> <?php
    }
  }
?>
