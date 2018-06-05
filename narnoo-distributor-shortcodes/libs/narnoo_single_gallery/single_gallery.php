<?php 
Narnoo_Shortcodes::load_scripts_for_single_gallery();
extract( shortcode_atts( array(
	'album' => '',	
    'operator'  => false,		
	'width' => '200',			// optional width
	'height' => '150',			// optional height
), $atts ) );

if(empty($album)){
	echo $error_msg_prefix . __( 'An album key is required', NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN );
}

$list 			= null;
$current_page 	= 1;
//$cache	 		= Narnoo_Operator_Helper::init_noo_cache();
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
            }else{
                $list = $request->getAlbumImages( $operator,$album );
            }
			
			if(empty($operator)){

        			if ( ! is_array( $list->distributor_albums_images ) ) {
        				throw new Exception( sprintf( __( "Error retrieving album images. Unexpected format in response page #%d.", NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
        			}

                    $galleryImages = $list->distributor_albums_images;
            }else{

                    if ( ! is_array( $list->operator_albums_images ) ) {
                        throw new Exception( sprintf( __( "Error retrieving album images. Unexpected format in response page #%d.", NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
                    }

                    $galleryImages = $list->operator_albums_images;

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

<!-- These are the images that will open in Imagebox. Because they have no thumbnails, they can be placed anywhere in the page. -->
				

<?php 
foreach ($galleryImages as $img) {
           
        echo '<a href="'.$img->xlarge_image_path.'" rel="imagebox[slg]" id="narnoo_single_link_gallery"></a>';
 }
 ?>
<a href="javascript:imagebox.open(document.getElementById('narnoo_single_link_gallery'));" class="thumbnail">
	<img src="<?php echo $galleryImages[0]->thumb_image_path; ?>" alt="Narnoo image" width="200" height="150" />
	<span class="cover"></span>
</a>
		
