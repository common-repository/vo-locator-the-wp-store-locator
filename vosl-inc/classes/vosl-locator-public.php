<?php
class VoStoreLocator_Public
{
	public function __construct() {	
	} 
	
	public function render_template($content)
	{
		global $vosl_dir, $vosl_base, $vosl_path, $votext_domain, $wpdb, $vosl_vars, $vosl_inc_dir, $form;
		if(! preg_match('|\[vo-locator\]|i', $content)) {
			return $content;
		}
		else {
			
			$this->vo_load_locator_js();
			//echo "Hii";
			include($vosl_path."/vosl-inc/includes/locations.php");	
			return preg_replace("@\[vo-locator(.*)?\]@i", $form, $content);
			}
	}

	public function render_standard_template()
	{
		global $vosl_dir, $vosl_base, $vosl_uploads_base, $vosl_path, $votext_domain, $wpdb, $vosl_vars, $vosl_inc_dir, $form;
		
		$this->vo_load_locator_js();
		include($vosl_path."/vosl-inc/includes/locations.php");	
		return $form;
	}
	
	public function vosl_add_bLinks()
	{
		$vosl_show_love = vosl_data('vosl_show_love');
	
		if($vosl_show_love=='')
			$vosl_show_love = 1;
			
		if($vosl_show_love)
			$style = '';
		else
			$style = 'style="display:none;"';		
		?>	
		<div class="voslrow voslblink" <?php echo $style; ?>>
			<a title="WP Store Locator" target="_blank" href="<?php echo VOSL_MAINWEBSITE_URL."/vo-locator-wordpress-store-locator-plugin/"; ?>">VO Store Locator</a>
			&nbsp;|&nbsp; Vitalized By <a target="_blank" title="Workflow Management Solution" href="<?php echo VOSL_MAINWEBSITE_URL; ?>">Vital Organizer</a>
		</div>
		<?php
	}
	
	public function register_volocator_script()
	{
		global $vosl_base;
		
		wp_register_script( 'voloadlocator', $vosl_base . '/js/locator.js', array( 'jquery' ) );
	}
	
	public function vo_load_locator_js() {
		global $vosl_base, $wp_scripts;
		// load our jquery file that sends the $.post request
		//wp_enqueue_script( "voloadlocator", $vosl_base . '/js/locator.js', array( 'jquery' ) );
		
		wp_enqueue_script( "voloadlocator" );
		// make the ajaxurl var available to the above script
		wp_localize_script( 'voloadlocator', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
		
	}
}
?>