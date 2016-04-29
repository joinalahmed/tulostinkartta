<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package cc2
 */
?>

<?php $content_class = array('main-content-inner'); ?>
<?php get_header(); ?>

    <div class="main-content">
        <div id="container" class="container">
            <div class="row">
				

                <?php do_action( 'cc_first_inside_main_content'); ?>

                <?php
                // get the left sidebar if it should be displayed
                if( cc2_display_sidebar( 'left' ) )
                    get_sidebar( 'left' ); ?>

                <div id="content" class="<?php echo apply_filters( 'cc2_content_class', $content_class ); ?>">

                    <?php do_action( 'cc_first_inside_main_content_inner'); ?>
		    <?php tulostinkartta_bittikukkaro(); ?>


		    

                    
                </div><!-- close #content -->

                <?php if( cc2_display_sidebar( 'right' ) )
                    get_sidebar( 'right' ); ?>


            </div><!-- close .row -->
        </div><!-- close .container -->
    </div><!-- close .main-content -->

<?php get_footer(); ?>






