<?php
/* Tulostuspyyntösivulle pääsy vain lähettäjälle ja vastaanottajalle */

function printjob_page() {
                global $post;
                global $wpdb;
                global $bp;
                $postid = get_the_ID();

                $current_user_ID = get_current_user_id(); 

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID != ($kirjoittaja || $vastaanottaja)){
			         die ( 'You do not have sufficient permissions to access this page!' );
	            }
    
            $updated = $_GET['updated'];
            if ($updated == "true") {
                printjob_updated();
                }
            printjob_status();
}

/* Tulostuspyynnön päivitysfunktio */

/* Tulostuspyynnön tilat:
    - tarjous
    - tilaus
    - maksu
    - arvostelu
    - valmis
    - peruutettu
    */

function printjob_updated() {
	    global $wpdb,$bp;
        global $post;
        global $user_ID;

        $postid = get_the_ID();

        $current_user_ID = get_current_user_id();
    
        $updated = $_GET['updated'];

	    $today = date("d.m.Y H:i:s");
    
        $kirjoittaja = get_post($post->ID);      
        $kirjoittaja = $kirjoittaja->post_author;
    
        $vastaanottaja = get_post_meta($post->ID, "tulostin",true);
        $contact_kirjoittaja = get_post($vastaanottaja);
        $vastaanottaja = $contact_kirjoittaja->post_author;  

	    $tila = get_post_meta( get_the_ID(), 'tila', true );    
    
      if (($updated == "true") && ($tila == "tarjous") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nUusi tulostuspyyntö: " . $post->guid . "\n";     
        $otsikko = "Uusi tulostuspyyntö";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
	    update_post_meta($post->ID, "tilaaja",$kirjoittaja);
  	    update_post_meta($post->ID, "tulostaja",$vastaanottaja);
	    update_post_meta($post->ID, "aika",$today);
      } 
      if (($updated == "true") && ($tila == "tilaus") && ($current_user_ID == $vastaanottaja)) {
        $message = "\nUusi tarjous: " . $post->guid . "\n";
        $otsikko = "Uusi tarjous";
        $recipient = $kirjoittaja;
        $sender = $vastaanottaja;
        update_post_meta($post->ID, "tarjousaika",$today);
	    $kurssi = get_option("btc_kurssi");
	    $hinta = get_post_meta($post->ID, "tarjous",true);	
	    $hinta_bitcoineissa = $hinta / $kurssi;
	    update_post_meta($post->ID, "bitcoin_hinta",$hinta_bitcoineissa);
      }
      if (($updated == "true") && ($tila == "maksu") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nUusi tilaus: " . $post->guid . "\n";
        $otsikko = "Uusi tilaus";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
        update_post_meta($post->ID, "tilausaika",$today);
        require_once('/var/www/dev/wp-content/plugins/tulostinkartta/block_io.php'); 
        $apiKey = get_option( 'blockio_api_key_0' );  
    	$version = 2; 
        $pin = get_option( 'blockio_pin_1' );    
    	$block_io = new BlockIo($apiKey, $pin, $version);    	
   	    $amounts = get_post_meta($post->ID, "bitcoin_hinta",true);
	    $amounts = floatval($amounts);
        $amounts = round($amounts, 4);
	    $amounts = ($amounts-0.0001);
	    $tulos = $block_io->withdraw_from_labels(array('amounts' => $amounts, 'from_labels' => $kirjoittaja, 'to_labels' => '1'));
	    tt_blockio_balances();
	    update_post_meta($post->ID, "btc_escrow",$amounts);
      }
      if (($updated == "true") && ($tila == "arvostelu") && ($current_user_ID == $vastaanottaja)) {
        $message = "\nTilaus merkitty maksetuksi: " . $post->guid . "\n";
        $otsikko = "Tilaus merkitty maksetuksi";
        $recipient = $kirjoittaja;
        $sender = $vastaanottaja;
        update_post_meta($post->ID, "maksuaika",$today);
      }
      if (($updated == "true") && ($tila == "valmis") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nTulostustyö arvosteltu: " . $post->guid . "\n";
        $otsikko = "Uusi arvostelu";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
        update_post_meta($post->ID, "valmisaika",$today);
        require_once('/var/www/dev/wp-content/plugins/tulostinkartta/block_io.php'); 
        $apiKey = get_option( 'blockio_api_key_0' );  
    	$version = 2; 
        $pin = get_option( 'blockio_pin_1' );    
        $block_io = new BlockIo($apiKey, $pin, $version);   
        $amounts = get_post_meta($post->ID, "bitcoin_hinta",true);
        $amounts = floatval($amounts);
        $amounts = round($amounts, 4);
        $amounts = ($amounts-0.0001);
        $tulos = $block_io->withdraw_from_labels(array('amounts' => $amounts, 'from_labels' => '1', 'to_labels' => $vastaanottaja));
        tt_blockio_balances();
      }
      if (($updated == "true") && ($tila == "peruutettu") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nTulostustyö peruutettu: " . $post->guid . "\n";
        $otsikko = "Tulostustyö peruutettu";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
        update_post_meta($post->ID, "peruutusaika",$today);
      }
      if (($updated == "true") && ($tila == "peruutettu") && ($current_user_ID == $vastaanottaja)) {
        $message = "\nTulostustyö peruutettu: " . $post->guid . "\n";
        $otsikko = "Tulostustyö peruutettu";
        $recipient = $kirjoittaja;
        $sender = $vastaanottaja;
        update_post_meta($post->ID, "peruutusaika",$today);
      }          
        $args = array( 'recipients' => $recipient, 'sender_id' => $sender, 'subject' => $otsikko, 'content' => $message );
        messages_new_message( $args );    
        printjob_reload();    
    }

/* BuddyPress-privaattiviestin lähettäminen tulostuspyynnöstä */
                
function printjob_message($recipient,$sender,$otsikko,$message) {
    global $wpdb,$bp;
    global $post;
    global $user_ID;

    $args = array( 'recipients' => $recipient, 'sender_id' => $sender, 'subject' => $otsikko, 'content' => $message );      
    messages_new_message( $args );
    }

/* Ladataan tulostuspyyntösivu uudelleen päivityksen jälkeen */

function printjob_reload() {    
    $tulostuspyyntourl =  get_permalink($post->ID);
        echo "<script type='text/javascript'>                                                                                                   
	           window.location.assign('" . $tulostuspyyntourl  . "')                                                                             
	           </script>";
        }
    
/* Tulostuspyynnön tilaviestit ja funktiot */

function printjob_status() {
                global $post;
                global $wpdb;
                global $bp;

                $tila = get_post_meta( get_the_ID(), 'tila', true );				   

                if ($tila == "tarjous") { 
                    echo "<h1>Odottaa tulostajan tarjousta</h1>";
                    printjob_text();
		            printjob_tarjous();
                }
    
                if ($tila == "tilaus") { 
                    echo "<h1>Odottaa tilaajan tilausta</h1>";
		            printjob_text(); 
                    printjob_tilaus();
                }
    
                if ($tila == "maksu") { 
                    echo "<h1>Odottaa tulostajan kuittausta toimituksesta</h1>"; 
		            printjob_text();
                    printjob_maksu();
                }
    
                if ($tila == "arvostelu") { 
                    echo "<h1>Odottaa tilaajan arvostelua</h1>";
		            printjob_text(); 
                    printjob_arvostelu();
                }
    
                if ($tila == "valmis") { 
                    echo "<h1>Tulostustyö on valmis</h1>";
		            printjob_text(); 
                    printjob_valmis();
                }
    
                if ($tila == "peruutettu") { 
                    echo "<h1>Tulostustyö on peruutettu</h1>"; 
		            printjob_text();
                    printjob_peruutettu();                       
                }                
        }

/* Tulostuspyyntösivun tekstit */

function printjob_text() {
                global $post;
                global $wpdb;
                global $bp;

		        $tiedosto = get_post_meta($post->ID,"tiedosto",true);

		        echo do_shortcode( '[canvasio3D width="320" height="320" border="1" borderCol="#F6F6F6" dropShadow="0" backCol="#000000" backImg="..." Mouse="on" rollMode="off" rollSpeedH="10" rollSpeedV="10" objPath="' . $tiedosto . '" objScale="1" objColor="#808080" lightSet="7" reflection="off" refVal="5" objShadow="off" floor="off" floorHeight="42" lightRotate="off" Help="off"] [/canvasio3D]' );
	                
		        echo "<h3>Tulostuspyynnön tiedot</h3>";
		        echo "<p>Lähetetty ";
		        the_field("aika");
		        echo "</p>";
		        echo "<ul>";
                echo "<li>Tilaaja: ";
                the_author_link();
                echo "</li><li>Tulostin: ";
		        echo '<a href="/tulostin/';
                the_field("tulostin");
                echo '/">';
                the_field("tulostin");
                echo '</a>';
                echo "</li><li>Tiedosto: ";
		        echo '<a href="';
                the_field("tiedosto");
                echo '">';
                the_field("tiedosto");
                echo '</a>';
                echo "</li><li>Materiaali: ";
                the_field("materiaali");
                echo "</li><li>Määrä: ";    
                the_field("maara");
                echo "</li><li>Kappalehinta: ";    
                the_field("hinta");
                echo "</li><li>Kokonaishinta: ";    
                the_field("tarjous");
                echo "</li>";
                echo "</li><li>Kuvaus: ";
                the_field("kuvaus");
                echo "</li>";
                echo "</ul>";

if( get_field( "tarjousaika" ) ): ?>
    <h3>Tarjouksen tiedot</h3>
    <p>Tarjous lähetetty: <?php the_field( "tarjousaika" ); ?></p>
    <ul>
        <li>Tarjouksen kommentti: <?php the_field( "tarjous_kommentti" ); ?></li>
        <li>Hinta bitcoineissa: <?php the_field( "bitcoin_hinta" ); ?></li>
    </ul>
<?php endif;

if( get_field( "tilausaika" ) ): ?>
    <h3>Tilauksen tiedot</h3>
    <p>Tilattu: <?php the_field( "tilausaika" ); ?></p>
    <ul>
        <li>Tilaajan osoite: <?php the_field( "toimitusosoite" ); ?></li>
        <li>Tilauksen kommentti: <?php the_field( "tilauksen_kommentti" ); ?></li>
    </ul>
<?php endif;

if( get_field( "maksuaika" ) ): ?>
    <h3>Tilauksen toimitus</h3>
    <p>Merkitty toimitetuksi: <?php the_field( "maksuaika" ); ?></p>
    <ul>
        <li>Tulostajan kommentti toimituksesta: <?php the_field( "maksu_kommentti" ); ?></li>
    </ul>
<?php endif;

if( get_field( "valmisaika" ) ): ?>
    <h3>Tulostustyön arvostelu</h3>
    <p>Merkitty valmiiksi: <?php the_field( "valmisaika" ); ?></p>
    <ul>
        <li>Arvosana: <?php the_field( "arvostelunumero" ); ?></li>
        <li>Arvio: <?php the_field( "tekstiarvostelu" ); ?></li>
    </ul>
<?php endif;
}

/* Tarjouksen Advanced Custom Forms -kentät */

function printjob_tarjous() {
                global $post;
                global $wpdb;
                global $bp;
                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

  if ($current_user_ID == $vastaanottaja){
    acf_form(array(
                   'post_id'=> $postid,
                   'field_groups' => array('4321'),
                   'return' => '?updated=true',
                   'submit_value'=> 'Päivitä',
                   ));
}
}

/* Tilauksen Advanced Custom Forms -kentät */

function printjob_tilaus() {
                global $post;
                global $wpdb;
                global $bp;
				global $user_ID;
                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID == $kirjoittaja){
                    $hinnoittelu = get_post_meta($postid,"bitcoin_hinta",true);
                    $lompakko = get_user_meta($current_user_ID,"btc_available",true);
                    $hinnoittelu = floatval($hinnoittelu);
                    $lompakko = floatval($lompakko);

                    /* Näitä bitcoin-laskelmia pitää katsoa vielä "hieman" tarkemmin */    
    
                    if($lompakko>$hinnoittelu) {
                        acf_form(array(
                            'post_id'=> $postid,
                            'field_groups' => array('4322'),
                            'return' => '?updated=true',
                            'submit_value'=> 'Päivitä',
                            ));
                    }

                    if($lompakko<$hinnoittelu) {
			             echo "<h1>Sinulla ei ole tarpeeksi varaa!</h1>";
                         echo "<p>Siirrä ensin bittirahaa lompakkoosi...</p>";
                    }
                }
}

/* Maksun Advanced Custom Forms -kentät */

function printjob_maksu() {
                global $post;
                global $wpdb;
                global $bp;
                global $user_ID;

                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID == $vastaanottaja){
                    acf_form(array(
                        'post_id'=> $postid,
                        'field_groups' => array('4323'),
                        'return' => '?updated=true',
                        'submit_value'=> 'Päivitä',
                        ));
                }
}

/* Arvostelun Advanced Custom Forms -kentät */

function printjob_arvostelu() {
                global $post;
                global $wpdb;
                global $bp;
                global $user_ID;

                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID == $kirjoittaja){
                    acf_form(array(
                        'post_id'=> $postid,
                        'field_groups' => array('4324'),
                        'return' => '?updated=true',
                        'submit_value'=> 'Päivitä',
                        ));
                }
}

/* Valmiiseen ja peruutettuun tulostuspyyntöön ei tarvita mitään? */

function printjob_valmis() {
}

function printjob_peruutettu() {
}
