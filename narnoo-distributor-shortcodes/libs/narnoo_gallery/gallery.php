<?php 
Narnoo_Shortcodes::load_scripts_for_image_gallery();

extract( shortcode_atts( array(
	'album' 	=> '',
    'operator'  => false,
	'width' 	=> '200',	// optional width
	'height' 	=> '150',	// optional height
	'speed'		=> 5000,
), $atts ) );


if( empty($album) && empty($operator)){
	echo $error_msg_prefix . __( 'An album key is required', NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN );
}

$list 			= null;
$current_page 	= 1;
//$cache	 		= Narnoo_Distributor_Helper::init_noo_cache();
if(empty($operator)){
    $request        = Narnoo_Distributor_Helper::init_api('media');
}else{
    $request        = Narnoo_Distributor_Helper::init_api('operator');
}


if ( ! is_null( $request ) ) {

	//$list = $cache->get('album_'.$album.$current_page);
	

	if(empty($list)){

		try {

            if(empty($operator)){
                $list = $request->getAlbumImages( $album, $current_page );
            }elseif( !empty($operator) && !empty($album) ){
                $list = $request->getAlbumImages( $operator,$album );
            }else{
            	 $list = $request->getImages( $operator );
            }
			//$list = $request->getAlbumImages( $album, $current_page );
			if(empty($operator)){

        			if ( ! is_array( $list->distributor_albums_images ) ) {
        				throw new Exception( sprintf( __( "Error retrieving album images. Unexpected format in response page #%d.", NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
        			}

                    $galleryImages = $list->distributor_albums_images;
            


            }elseif( !empty($operator) && !empty($album) ){
               
                if ( ! is_array( $list->operator_albums_images ) ) {
                        throw new Exception( sprintf( __( "Error retrieving album images. Unexpected format in response page #%d.", NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
                }

                $galleryImages = $list->operator_albums_images;
               

            }else{

            	if ( ! is_array( $list->operator_images ) ) {
                        throw new Exception( sprintf( __( "Error retrieving operator images. Unexpected format in response page #%d.", NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
                }
                $galleryImages = $list->operator_images;
   

            }

			if(!empty( $list->success ) ){
				//$cache->set('album_'.$album.$current_page, $list, 43200);
			}

		} catch ( Exception $ex ) {
			Narnoo_Distributor_Helper::show_api_error( $ex );
		} 


	}


} 

$cssRand = rand(1,100);
//print_r($list); onSliderLoad: function(el){ el.lightGallery({selector:'#noo-gallery .lslide'})}
?>
<script>
    	 jQuery(document).ready(function() {
			jQuery('#noo-gallery-<?php echo $cssRand; ?>').lightSlider({
                gallery:true,
                item:1,
                thumbItem:8,
                slideMargin: 0,
                pause: <?php echo $speed; ?>,
                speed:800,
                auto:true,
                loop:true,
                onSliderLoad: function() {
                    jQuery('#noo-gallery-<?php echo $cssRand; ?>').removeClass('cS-hidden');
                }  
            });
		});
    </script>
<div class="noo-gallery-holder" style="width:400px">      
            <div class="clearfix" style="max-width:474px;">
                <ul id="noo-gallery-<?php echo $cssRand; ?>" class="list-unstyled cS-hidden">
                    <?php foreach ($galleryImages as $img) {
                    	
                    	echo '<li data-thumb="' .$img->image_400_path . '"> 
                        		<img src="' . $img->image_800_path . '" />
                    		</li>';

                    }?>
                </ul>
        </div>
</div>