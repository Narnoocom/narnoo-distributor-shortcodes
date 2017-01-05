<?php
Narnoo_Shortcodes::load_scripts_for_slider_gallery();

extract( shortcode_atts( array(
	'album' 	=> '',
	'number'	=> 6,
	'height' 	=> '400',	// optional height
	'speed'		=> 5000,
), $atts ) );


if(empty($album)){
	echo $error_msg_prefix . __( 'An album key is required', NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN );
}

$list 			= null;
$current_page 	= 1;
//$cache	 		= Narnoo_Distributor_Helper::init_noo_cache();
$request 		= Narnoo_Distributor_Helper::init_api('media');


if ( ! is_null( $request ) ) {

	//$list = $cache->get('album_'.$album.$current_page);


	if(empty($list)){

		try {
			$list = $request->getAlbumImages( $album, $current_page );

			if ( ! is_array( $list->distributor_albums_images ) ) {
				throw new Exception( sprintf( __( "Error retrieving album images. Unexpected format in response page #%d.", NARNOO_OPERATOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
			}

			if(!empty( $list->success ) ){
				//$cache->set('album_'.$album.$current_page, $list, 43200);
			}

		} catch ( Exception $ex ) {
			Narnoo_Distributor_Helper::show_api_error( $ex );
		}


	}


}


?>
<style>
.noo_slider-main-slide{
    max-height:<?php echo $height; ?>px;
    max-width:100%;
    padding:0;
    overflow:hidden;
    position: relative;
}
</style>
<div class="noo_slider-main-slide">

    <div class="cycle-slideshow" data-cycle-fx=fadeout data-cycle-timeout=<?php echo $speed; ?> style="position: relative;">
        <?php
				shuffle($list->distributor_albums_images);
				$a=0;
        foreach ($list->distributor_albums_images as $img) {
					if($a <= $number){
						echo '<img class="slide" src="'.$img->xlarge_image_path.'" >';
					}
					$a++;
         }
         ?>
    </div>
  </div>
