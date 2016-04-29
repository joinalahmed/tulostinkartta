<?php
/**
 * The Template for displaying all single posts.
 *
 * @package _tk
 */
$content_class = array('main-content-inner');
?>

<?php 
acf_form_head();
get_header(); ?>


    <div class="main-content">
        <div id="container" class="container">
            <div class="row">

                <?php do_action( 'cc_first_inside_main_content'); ?>

                <?php
                // get the left sidebar if it should be displayed
                if( cc2_display_sidebar( 'left' ) )
                    get_sidebar( 'left' ); ?>

                <div id="content" class="<?php echo apply_filters( 'cc2_content_class', $content_class ); ?>">
                    <?php while ( have_posts() ) : the_post(); ?>


                    <?php printjob_page(); ?>

                    			  
  		    <?php endwhile; ?>                  
                </div><!-- close #content -->

                <?php if( cc2_display_sidebar( 'right' ) )
                        get_sidebar( 'right' ); ?>

            </div><!-- close .row -->
        </div><!-- close .container -->
    </div><!-- close .main-content -->
<?php get_footer(); ?>