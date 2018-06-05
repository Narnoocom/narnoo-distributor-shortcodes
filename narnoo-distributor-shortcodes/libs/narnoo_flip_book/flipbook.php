<?php
Narnoo_Shortcodes::load_scripts_for_flip_book();

extract( shortcode_atts( array(
	'brochure' 	=> '',
	'operator'	=> false,
	'thumb'		=> 'thumb'
), $atts ) );


if( empty($brochure) ){
	echo $error_msg_prefix . __( 'A brochure ID is required', NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN );
}


$list 			= null;
/**
*
*   Need to update the caching aspect of this plugin
*	$cache	 		= Narnoo_Distributor_Helper::init_noo_cache();
*
*/


if(empty($operator)){
	$request 		= Narnoo_Distributor_Helper::init_api('media');
}else{
	$request 		= Narnoo_Distributor_Helper::init_api('operator');
}


if ( ! is_null( $request ) ) {
	
	/*if(empty($operator)){
	   $list = $cache->get('print_'.$brochure);
	}else{
	    $list = $cache->get('print_'.$brochure.$operator);
	}*/

	if(empty($list)){

		try {

			if(empty($operator)){
				$list = $request->getBrochureDetails( $brochure );
			}else{
				$list = $request->getBrochureDetails( $operator,$brochure );
			}

			
			if ( ! is_array( $list->zoom_pages ) ) {
				throw new Exception( sprintf( __( "Error retrieving brochure details. Unexpected format in response brochure #%d.", NARNOO_DISTRIBUTOR_SHORTCODE_I18N_DOMAIN ), $brochure ) );
			}

			if(!empty( $list->success ) ){
				//	Set the cache
				//$cache->set('print_'.$brochure, $list, 43200);
		    }

		} catch ( Exception $ex ) {
			Narnoo_Distributor_Helper::show_api_error( $ex );
		}


	}


}

switch ($thumb) {
	case 'thumb':
		$timg = $list->thumb_image_path;
		break;
	case 'crop':
		$timg = $list->xcrop_image_path;
		break;
	case 'preview':
		$timg = $list->preview_image_path;
		break;
	default:
		$timg = $list->thumb_image_path;
		break;
}

//print_r($list);
?>

<!-- begin flipbook lightbox code --> 
<a class="btn" href="load_book_lightbox('narnoo_flip_book')" data-content="<?php echo NARNOO_DISTRIBUTOR_SHORTCODE_URL . 'libs/narnoo_flip_book/pages/content.php?'.http_build_query( $list->zoom_pages ).'&download='.urlencode($list->file_path_to_pdf);  ?>" data-config="<?php echo NARNOO_DISTRIBUTOR_SHORTCODE_URL . 'libs/narnoo_flip_book/pages/config.html'?>"  target="_blank">
	<img src="<?php echo $timg; ?>" class="img-responsive"/>

</a>
 <!-- end flipbook lightbox code  -->
