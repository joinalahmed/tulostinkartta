<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package _tk
 */
$content_class = array('main-content-inner');
?>

<?php get_header(); ?>

    <div class="main-content">
        <div id="container" class="container">
            <div class="row">

                <?php do_action( 'cc_first_inside_main_content'); ?>

                <?php
                // get the left sidebar if it should be displayed
                if( cc2_display_sidebar( 'left' ) ) :
                    get_sidebar( 'left' ); 
                endif; ?>
                    

                <div id="content" class="<?php echo apply_filters('cc2_content_class', $content_class ); ?>">

                    <?php do_action( 'cc_first_inside_main_content_inner'); ?>

<h1>Lähetetyt tulostuspyynnöt</h1>  
  <?php if ( is_user_logged_in() ) {  
$current_user_ID = get_current_user_id();
$args = array('post_type' => 'tulostuspyynto',
	      'author' => $current_user_ID);
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) {
  echo '<ul>';
  while ( $the_query->have_posts() ) {
    $the_query->the_post();
    $tulostin = get_post_meta(get_the_ID(), "tulostin", true );
    echo '<li><a href="' . get_permalink() . '">Tulostuspyyntö tulostimelle ' . get_the_title($tulostin) . '</a> (tila ' . get_post_meta(get_the_ID(), "tila", true )  . ')</li>';
  }
  echo '</ul>';
} else {
  echo "<p>Ei tulostuspyyntöjä!</p>";
}
wp_reset_postdata(); 
?>
<h1>Vastaanotetut tulostuspyynnöt</h1>
<?php  
$current_user_ID = get_current_user_id();
$args = array('post_type' => 'tulostuspyynto',
	      'meta_key'   => 'tulostinomistaja',
	      'meta_value' => $current_user_ID);
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) {
  echo '<ul>';
  while ( $the_query->have_posts() ) {
    $the_query->the_post();
    $tulostin = get_post_meta(get_the_ID(), "tulostin", true );
    echo '<li><a href="' . get_permalink() . '">Tulostuspyyntö tulostimelle ' . get_the_title($tulostin) . '</a> (tila ' . get_post_meta(get_the_ID(), "tila", true )  . ')</li>';
  }
  echo '</ul>';
} else {
  echo "<p>Ei tulostuspyyntöjä!</p>";
}
wp_reset_postdata(); 
}
?>
                    
                </div><!-- close #content -->

                <?php if( cc2_display_sidebar( 'right' ) ) :
                    get_sidebar( 'right' );
				endif; ?>

            </div><!-- close .row -->
        </div><!-- close .container -->
    </div><!-- close .main-content -->


<?php get_footer(); ?>

