<?php
/**
 * The Template for displaying all single posts.
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
                if( cc2_display_sidebar( 'left' ) )
                    get_sidebar( 'left' ); ?>

                <div id="content" class="<?php echo apply_filters( 'cc2_content_class', $content_class ); ?>">

                    <?php do_action( 'cc_first_inside_main_content_inner'); ?>

                    <?php while ( have_posts() ) : the_post(); ?>


                    <?php
/**
 * @package cc2
 */

$show_title = true;
$display_page_title_props = get_theme_mod('display_page_title', array() );
$author_image_settings = get_theme_mod('show_author_image' );
$author_avatar = false;
$post_class = get_post_class();


//new __debug( array('settings' => $author_image_settings, 'global settings' => get_theme_mod('show_author_image', array() ) )  );

// search
if( isset( $display_page_title_props['posts'] ) && $display_page_title_props['posts'] != 1 ) {
	$show_title = false;
}

if( isset($author_image_settings['single_post']) && $author_image_settings['single_post'] != false ) {
	$author_avatar = cc2_get_author_image();
		
	$post_class[] = 'has-author-avatar';
}


?>
                    
                
<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>
	
	<header class="page-header">
		<h1 class="page-title"><?php the_title(); ?></h1>

		<div class="entry-meta">
			<div class="entry-meta-author pull-left">
                <?php $tulostin = get_user_by( 'id', $post->post_author );
                $tulostin = $tulostin->user_login;
                echo '<a href="http://tulostimet.3d-tarvike.fi/tulostinkartta/tulostinkartan-jasenet/' . $tulostin  . '/">';
                echo bp_core_fetch_avatar ( array( 'item_id' => $post->post_author, 'type' => 'full' ) ); ?></a>
                <p>Käyttäjän <?php echo '<a href="http://tulostimet.3d-tarvike.fi/tulostinkartta/tulostinkartan-jasenet/' . $tulostin  . '/">';
                    echo "@" . $tulostin; ?></a> tulostin</p>
			</div>			
			<?php _tk_posted_on(); ?>
		</div><!-- .entry-meta -->
	
<?php the_post_thumbnail('medium', 'style=max-width:50%;float:none;margin-left:10px;margin-bottom:10px;'); ?>	

</header><!-- .entry-header -->

        
	
	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

<footer class="entry-meta">
    
<div>
<h3>Tulostimet</h3>
<?php
if( have_rows('tulostimet') ):
    echo "<table>";
    echo "<tr><th>Tulostin</th><th>Leveys</th><th>Korkeus</th><th>Pituus</th><th>Hinta</th></tr>";
    while ( have_rows('tulostimet') ) : the_row();
        echo "<tr><td>";
        the_sub_field('tulostimen_malli');
        echo "</td><td>";
        the_sub_field('tulostimesi_leveys');
        echo " mm</td><td>";
        the_sub_field('tulostimesi_korkeus');
        echo " mm</td><td>";
        the_sub_field('tulostimesi_pituus');
	echo " mm</td><td>";
        the_sub_field('tulostimesi_hinta');
        echo " €/cm<sup>3</sup></td></tr>";
    endwhile;
echo "</table>";
endif;
?>
</div>

<div>
<h3>Filamentit</h3>
<?php
if( have_rows('filamentit_lista') ):
    echo "<table>";
    echo "<tr><th>Filamentti</th><th>Hinta</th><th>Väri</th></tr>";
    while ( have_rows('filamentit_lista') ) : the_row();
        echo "<tr><td>";
        the_sub_field('filamentin_nimi');
        echo "</td><td>";
        the_sub_field('filamenttisi_hinta');
        echo " €/cm<sup>3</sup></td>";
	$color = get_sub_field('filamentin_vari');
        echo '<td bgcolor="' . $color . '"></td></tr>';
    endwhile;
echo "</table>";
endif;
?>
</div>
    
<div>
<h3>Tee tulostuspyyntö</h3>
    <p><a href = "<?php echo "/3dprint/?printteri=" . $post->ID; ?>"><button>Tee tulostuspyyntö!</button></a></p></div>


<?php /* echo do_shortcode('[3dprint-lite]'); */ ?>
<div>
<h3>Sijainti</h3>
<?php echo do_shortcode('[gmw_single_location additional_info="0" distance="0" hide_info="1" post_title="0"]'); ?>
</div>    
    </footer><!-- .entry-meta -->
</article><!-- #post-## -->

                    
                        <?php
                            // If comments are open or we have at least one comment, load up the comment template
                            if ( comments_open() || '0' != get_comments_number() )
                                comments_template();
                        ?>

                    <?php endwhile; // end of the loop. ?>

                </div><!-- close #content -->

                <?php if( cc2_display_sidebar( 'right' ) )
                        get_sidebar( 'right' ); ?>

            </div><!-- close .row -->
        </div><!-- close .container -->
    </div><!-- close .main-content -->


<?php get_footer(); ?>
