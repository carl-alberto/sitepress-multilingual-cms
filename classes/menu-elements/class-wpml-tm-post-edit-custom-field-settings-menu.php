<?php

class WPML_TM_Post_Edit_Custom_Field_Settings_Menu extends WPML_SP_User {

	/** @var  WPML_Custom_Field_Setting_Factory $setting_factory */
	private $setting_factory;

	/** @var WP_Post $post */
	private $post;

	private $rendered = false;

	/**
	 * WPML_TM_Post_Edit_Custom_Field_Settings_Menu constructor.
	 *
	 * @param SitePress                         $sitepress
	 * @param WPML_Custom_Field_Setting_Factory $settings_factory
	 * @param WP_Post                           $post
	 */
	public function __construct( &$sitepress, &$settings_factory, $post ) {
		parent::__construct( $sitepress );
		$this->setting_factory = &$settings_factory;
		$this->post            = $post;
	}

	/**
	 * @return string
	 */
	public function render() {
		$custom_keys = (array) $this->sitepress->get_wp_api()->get_post_custom_keys( $this->post->ID );
		$custom_keys = $this->setting_factory->filter_custom_field_keys( $custom_keys );
		ob_start();
		if ( 0 !== count( $custom_keys ) ) {
			?>
			<table class="widefat">
				<thead>
				<tr>
					<th colspan="2"><?php _e( 'Custom fields', 'sitepress' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $custom_keys as $cfield ) {
					$field_setting = $this->setting_factory->post_meta_setting( $cfield );
					if ( $field_setting->excluded() ) {
						continue;
					}
					$this->rendered = true;
					$radio_disabled = $field_setting->is_read_only() ? 'disabled="disabled"' : '';
					$status         = $field_setting->status();
					$checked0       = $status == WPML_IGNORE_CUSTOM_FIELD ? ' checked="checked"' : '';
					$checked1       = $status == WPML_COPY_CUSTOM_FIELD ? ' checked="checked"' : '';
					$checked2       = $status == WPML_TRANSLATE_CUSTOM_FIELD ? ' checked="checked"' : '';
					?>
					<tr>
						<td id="icl_mcs_cf_<?php echo base64_encode( $cfield ); ?>"><?php echo $cfield; ?></td>
						<td align="right">
							<label><input class="icl_mcs_cfs"
							              name="icl_mcs_cf_<?php echo base64_encode( $cfield ); ?>"
							              type="radio"
							              value="0" <?php echo $radio_disabled . $checked0 ?> />&nbsp;<?php _e( "Don't translate", 'sitepress' ) ?>
							</label>
							<label><input class="icl_mcs_cfs"
							              name="icl_mcs_cf_<?php echo base64_encode( $cfield ); ?>"
							              type="radio"
							              value="1" <?php echo $radio_disabled . $checked1 ?> />&nbsp;<?php _e( "Copy", 'sitepress' ) ?>
							</label>
							<label><input class="icl_mcs_cfs"
							              name="icl_mcs_cf_<?php echo base64_encode( $cfield ); ?>"
							              type="radio"
							              value="2" <?php echo $radio_disabled . $checked2 ?> />&nbsp;<?php _e( "Translate", 'sitepress' ) ?>
							</label>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<br/>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * @return bool true if there were actual custom fields to display options for
	 */
	public function is_rendered() {

		return $this->rendered;
	}
}