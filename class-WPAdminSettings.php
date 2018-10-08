<?php
/*
* @Author 	:	PickPlugins
* Copyright	: 	2015 PickPlugins.com
*
* Version	:	1.0.3	
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


if( ! class_exists( 'WPAdminSettings' ) ) {
	
class WPAdminSettings {
	
	public $data = array();
	
    public function __construct( $args ){
		
		$this->data = &$args;
	
		if( $this->add_in_menu() ) {
			add_action( 'admin_menu', array( $this, 'add_menu_in_admin_menu' ), 12 );
		}
		
		add_action( 'admin_init', array( $this, 'display_fields' ), 12 );
		add_filter( 'whitelist_options', array( $this, 'whitelist_options' ), 99, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
	}
	
	public function add_menu_in_admin_menu() {
		
		if( "main" == $this->get_menu_type() ) {
			add_menu_page( $this->get_menu_name(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), array( $this, 'display_function' ), $this->get_menu_icon() );
		}
		
		if( "submenu" == $this->get_menu_type() ) {
			add_submenu_page( $this->get_parent_slug(), $this->get_page_title(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), array( $this, 'display_function' ) );
		}
	}
	
	public function enqueue_color_picker(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

        wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );

        wp_register_style( 'WPAdminSettings', plugins_url('/', __FILE__).'css/WPAdminSettings.css' );
        wp_enqueue_style( 'WPAdminSettings' );


	}
	
	public function display_fields() { 

 		foreach( $this->get_settings_fields() as $key => $setting ):
		
			add_settings_section(
				$key,
				isset( $setting['title'] ) ? $setting['title'] : "",
				array( $this, 'section_callback' ), 
				$this->get_current_page()
			);
			
			foreach( $setting['options'] as $option ) :
				
			add_settings_field( $option['id'], $option['title'], array($this,'field_generator'), $this->get_current_page(), $key, $option );

			endforeach;
		
		endforeach;
	}
	
	public function field_generator( $option ) {
			
		$id 		= isset( $option['id'] ) ? $option['id'] : "";
		$type 		= isset( $option['type'] ) ? $option['type'] : "";
		$details 	= isset( $option['details'] ) ? $option['details'] : "";
		
		if( empty( $id ) ) return;
		
		try{

		    //var_dump($type);
			    if( isset($option['type']) && $option['type'] === 'select' ) 		$this->generate_field_select( $option );
            elseif( isset($option['type']) && $option['type'] === 'select_multi')	$this->generate_field_select_multi( $option );
            elseif( isset($option['type']) && $option['type'] === 'select2')	    $this->generate_field_select2( $option );
			elseif( isset($option['type']) && $option['type'] === 'checkbox')	    $this->generate_field_checkbox( $option );
			elseif( isset($option['type']) && $option['type'] === 'radio')		    $this->generate_field_radio( $option );
			elseif( isset($option['type']) && $option['type'] === 'textarea')	    $this->generate_field_textarea( $option );
			elseif( isset($option['type']) && $option['type'] === 'number' ) 	    $this->generate_field_number( $option );
			elseif( isset($option['type']) && $option['type'] === 'text' ) 		    $this->generate_field_text( $option );
            elseif( isset($option['type']) && $option['type'] === 'text_multi' ) 	$this->generate_field_text_multi( $option );
			elseif( isset($option['type']) && $option['type'] === 'colorpicker')    $this->generate_field_colorpicker( $option );
			elseif( isset($option['type']) && $option['type'] === 'datepicker')	    $this->generate_field_datepicker( $option );
            elseif( isset($option['type']) && $option['type'] === 'repeater')	    $this->generate_field_repeater( $option );
            elseif( isset($option['type']) && $option['type'] === 'faq')	        $this->generate_field_faq( $option );
            elseif( isset($option['type']) && $option['type'] === 'addons_grid')	$this->generate_field_addons_grid( $option );
            elseif( isset($option['type']) && $option['type'] === 'custom_html')	$this->generate_field_custom_html( $option );





			elseif( isset($option['type']) && $option['type'] === $type ) 	do_action( "wp_admin_settings_custom_field_$type", $option );

			if( !empty( $details ) ) echo "<p class='description'>$details</p>";
		
		}
		catch(Pick_error $e) {
			echo $e->get_error_message();
		}
	}

	public function generate_field_datepicker( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		

		//var_dump(plugins_url('/', __FILE__));
		
		echo "<input type='text' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
		
	
		echo "<script>jQuery(document).ready(function($) { $('#$id').datepicker({dateFormat : 'dd-mm-yy'});});</script>";
	}
	
	public function generate_field_colorpicker( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		
		echo "<input type='text' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
		
		echo "<script>jQuery(document).ready(function($) { $('#$id').wpColorPicker();});</script>";
	}
	
	public function generate_field_text( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		
		echo "<input type='text' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
	}

    public function generate_field_text_multi( $option ){

        $id 			= isset( $option['id'] ) ? $option['id'] : "";
        $placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
        $values 	 		= get_option( $id );


        //var_dump($values);
        ?>

        <script>jQuery(document).ready(function($) {
                html_<?php echo $id; ?> = '<div class=""><input type="text" class="regular-text" name="<?php echo $id?>[]"  placeholder="<?php echo $placeholder; ?>" value="" /><span class="button" onclick="$(this).parent().remove()">X</span></div>';
            });</script>

        <span class="button" onclick="$('#<?php echo $id; ?>').append(html_<?php echo $id; ?>)">Add</span>
        <div class="field-list" id="<?php echo $id; ?>">

            <?php
            if(!empty($values)):

                foreach ($values as $value):

                    ?>
                    <div class="">
                        <input type='text' class='regular-text' name='<?php echo $id?>[]'  placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' /><span class="button" onclick="$(this).parent().remove()">X</span>
                    </div>
                    <?php

                endforeach;

            else:

                ?>
                <div class="">
                    <input type='text' class='regular-text' name='<?php echo $id?>[]'  placeholder='<?php echo $placeholder; ?>' value='' /><span class="button" onclick="$(this).parent().remove()">X</span>
                </div>
                <?php

            endif;

            ?>



        </div>

        <?php

    }







    public function generate_field_number( $option ){
		
		$id 			= isset( $option['id'] ) ? $option['id'] : "";
		$placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		$value 	 		= get_option( $id );
		
		echo "<input type='number' class='regular-text' name='$id' id='$id' placeholder='$placeholder' value='$value' />";
	}
	
	public function generate_field_textarea( $option ){
		
		$id = isset( $option['id'] ) ? $option['id'] : "";
		$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
		
		$value 	 = get_option( $id );
		
		echo "<textarea name='$id' id='$id' cols='40' rows='5' placeholder='$placeholder'>$value</textarea>";
	}
	
	public function generate_field_select( $option ){
		
		$id 	= isset( $option['id'] ) ? $option['id'] : "";
		$args 	= isset( $option['args'] ) ? $option['args'] : array();	
		$args	= is_array( $args ) ? $args : $this->generate_args_from_string( $args );
		$value	= get_option( $id );
		
		echo "<select name='$id' id='$id'>";
		foreach( $args as $key => $name ):
			$selected = $value == $key ? "selected" : "";
			echo "<option $selected value='$key'>$name</option>";
		endforeach;
		echo "</select>";
	}




    public function generate_field_select_multi( $option ){

        $id				= isset( $option['id'] ) ? $option['id'] : "";
        $args			= isset( $option['args'] ) ? $option['args'] : array();
        $args			= is_array( $args ) ? $args : $this->generate_args_from_string( $args );
        $option_value	= get_option( $id );

        echo "<select multiple name='{$id}[]'>";
        foreach( $args as $key => $value ):
            $selected = is_array( $option_value ) && in_array( $key, $option_value ) ? "selected" : "";
            echo "<option value='$key' $selected>$value</option>";
        endforeach;
        echo "</select>";
    }

    public function generate_field_select2( $option ){
        $id 		= isset( $option['id'] ) ? $option['id'] : "";
        $args 		= isset( $option['args'] ) ? $option['args'] : array();
        $args		= is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
        $value		= get_option( $id );
        $multiple 	= isset( $option['multiple'] ) ? $option['multiple'] : '';

        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array('jquery') );

        echo $multiple ? "<select name='{$id}[]' id='$id' multiple>" : "<select name='{$id}' id='$id'>";
        foreach( $args as $key => $name ):

            if( $multiple ) $selected = in_array( $key, $value ) ? "selected" : "";
            else $selected = $value == $key ? "selected" : "";
            echo "<option $selected value='$key'>$name</option>";

        endforeach;
        echo "</select>";

        echo "<script>jQuery(document).ready(function($) { $('#$id').select2({
			width: '320px',
			allowClear: true
		});});</script>";
    }

    public function generate_field_checkbox( $option ){
		
		$id				= isset( $option['id'] ) ? $option['id'] : "";
		$args			= isset( $option['args'] ) ? $option['args'] : array();
		$args			= is_array( $args ) ? $args : $this->generate_args_from_string( $args );
		$option_value	= get_option( $id );


		//var_dump($option_value);

		echo "<fieldset>";
		foreach( $args as $key => $value ):

			$checked = is_array( $option_value ) && in_array( $key, $option_value ) ? "checked" : "";
			echo "<label for='$id-$key'><input name='{$id}[]' type='checkbox' id='$id-$key' value='$key' $checked>$value</label><br>";
			
		endforeach;
		echo "</fieldset>";
	}



    public function generate_field_faq( $option ){

        $id				= isset( $option['id'] ) ? $option['id'] : "";
        $args			= isset( $option['args'] ) ? $option['args'] : array();
        $args			= is_array( $args ) ? $args : $this->generate_args_from_string( $args );
        $option_value	= get_option( $id );


        //var_dump($option_value);

        ?>
        <div class='faq-list'>
            <?php
            foreach( $args as $key => $value ):

                $title = $value['title'];
                $link = $value['link'];
                $content = $value['content'];

                ?>
                <div class="faq-item">
                    <div class="faq-header"><?php echo $title; ?></div>
                    <div class="faq-content"><?php echo $content; ?></div>

                </div>
                <?php

            endforeach;
            ?>

        </div>

        <?php


    }




    public function generate_field_addons_grid( $option ){

        $id 			= isset( $option['id'] ) ? $option['id'] : "";
        $placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
        $args 			= isset( $option['args'] ) ? $option['args'] : "";

        $values 	 		= get_option( $id );

        ?>
        <div class="addons-grid">
            <?php

            foreach($args as $key=>$grid_item){

                $title = isset($grid_item['title']) ? $grid_item['title'] : '';
                $link = isset($grid_item['link']) ? $grid_item['link'] : '';
                $thumb = isset($grid_item['thumb']) ? $grid_item['thumb'] : '';

                ?>

                <div class="item">
                    <div class="thumb"><a href="<?php echo $link; ?>"><img src="<?php echo $thumb; ?>"></img></a></div>
                    <div class="name"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></div>
                </div>
                <?php

            }
            ?>
        </div>

        <style type="text/css">

        </style>



        <?php



    }







    public function generate_field_custom_html( $option ){

        $id 			= isset( $option['id'] ) ? $option['id'] : "";
        $placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
        $args 			= isset( $option['args'] ) ? $option['args'] : "";

        $values 	 		= get_option( $id );
        $html 			= isset( $option['html'] ) ? $option['html'] : "";

        echo $html;



    }















    public function generate_field_repeater( $option ){

        $id				= isset( $option['id'] ) ? $option['id'] : "";
        $fields			= isset( $option['fields'] ) ? $option['fields'] : array();

        $option_value	= get_option( $id );


        //var_dump($option_value);

        ?>

        <fieldsets id="<?php echo $id; ?>">
            <button class="add">Add New</button>
            <?php
            foreach( $fields as $key => $field ):
                $type = isset($field['type']) ? $field['type'] : '';
                $title = isset($field['title']) ? $field['title'] : '';
                ?>
                <fieldset>
                    <label><?php echo $title; ?></label>
                    <?php

                    if($type == 'text'):
                        ?>
                        <input type="text" name="<?php echo $id; ?>[]" value="">
                        <?php

                    elseif ($type == 'textarea'):

                    endif;



                    ?>

                </fieldset>
                <?php
            endforeach;
            ?>

        </fieldsets>
        <?php



    }







	public function generate_field_radio( $option ){

		$id				= isset( $option['id'] ) ? $option['id'] : "";
		$args			= isset( $option['args'] ) ? $option['args'] : array();
		$args			= is_array( $args ) ? $args : $this->generate_args_from_string( $args );
		$option_value	= get_option( $id );

		echo "<fieldset>";
		foreach( $args as $key => $value ):

			$checked = is_array( $option_value ) && in_array( $key, $option_value ) ? "checked" : "";
			echo "<label for='$id-$key'><input name='{$id}[]' type='radio' id='$id-$key' value='$key' $checked>$value</label><br>";
				
		endforeach;
		echo "</fieldset>";
	}






















	public function section_callback( $section ) { 
		
		$data = isset( $section['callback'][0]->data ) ? $section['callback'][0]->data : array();
		$description = isset( $data['pages'][$this->get_current_page()]['page_settings'][$section['id']]['description'] ) ? $data['pages'][$this->get_current_page()]['page_settings'][$section['id']]['description'] : "";
		
		echo $description;
	}
	
	public function whitelist_options( $whitelist_options ){
		
		foreach( $this->get_pages() as $page_id => $page ): foreach( $page['page_settings'] as $section ):
			foreach( $section['options'] as $option ):
				$whitelist_options[$page_id][] = $option['id'];
			endforeach; endforeach;
		endforeach;
		
		return $whitelist_options;
	}
	
	public function display_function(){

        ?>
        <div class='wrap wpadminsettings'>
            <h2><?php echo $this->get_menu_page_title(); ?></h2><br>
        <?php

		
		parse_str( $_SERVER['QUERY_STRING'], $nav_menu_url_args );
		global $pagenow;
		
		
		settings_errors();
		
		$tab_count 	 = 0;

		?>
        <nav class='nav-tab-wrapper'>
        <?php
		echo "";
		foreach( $this->get_pages() as $page_id => $page ): $tab_count++;
			
			$active = $this->get_current_page() == $page_id ? 'nav-tab-active' : '';
			$nav_menu_url_args['tab'] = $page_id;
			$nav_menu_url = http_build_query( $nav_menu_url_args );
			
			?>
            <a href='<?php echo $pagenow.'?'.$nav_menu_url; ?>' class='nav-tab <?php echo $active; ?>'><?php echo $page['page_nav']; ?></a>
            <?php

		endforeach;
        ?>
        </nav>
        <form action='options.php' method='post'>
        <?php

		settings_fields( $this->get_current_page() );
		do_settings_sections( $this->get_current_page() );
		submit_button();

		?>
        </form>
        </div>
        <?php
	}
	
	
	// Default Functions
	
	public function generate_args_from_string( $string ){
		
		if( strpos( $string, 'WPADMINSETTINGS_PAGES_ARRAY' ) !== false ) return $this->get_pages_array();
		if( strpos( $string, 'WPADMINSETTINGS_TAX_' ) !== false ) return $this->get_taxonomies_array( $string );
		
		
		return array();
	}
	
	public function get_taxonomies_array( $string ){
		
		$taxonomies = array();

		preg_match_all( "/\%([^\]]*)\%/", $string, $matches );

		//var_dump($matches);

		if( isset( $matches[1][0] ) ) $taxonomy = $matches[1][0];
		else throw new Pick_error('Invalid taxonomy declaration !');
		
		if( ! taxonomy_exists( $taxonomy ) ) throw new Pick_error("Taxonomy <strong>$taxonomy</strong> doesn't exists !");
		
		$terms = get_terms( $taxonomy, array(
			'hide_empty' => false,
		) );
		
		foreach( $terms as $term ) $taxonomies[ $term->term_id ] = $term->name;
				
		return $taxonomies;		
	}
	
	public function get_pages_array(){
		
		$pages_array = array();
		foreach( get_pages() as $page ) $pages_array[ $page->ID ] = $page->post_title;
		
		return apply_filters( 'WPADMINSETTINGS_PAGES_ARRAY', $pages_array );
	}
	
	
	// Get Data from Dataset //
	
	public function get_option_ids(){
		
		$option_ids = array();
		foreach( $this->get_pages() as $page ):
			$setting_sections = isset( $page['page_settings'] ) ? $page['page_settings'] : array();
			foreach( $setting_sections as $setting_section ):
		
				$options = isset( $setting_section['options'] ) ? $setting_section['options'] : array();
				foreach( $options as $option ) $option_ids[] = isset( $option['id'] ) ? $option['id'] : '';
				
			endforeach;
		endforeach;
		return $option_ids; 
	}
	
	public function get_current_page(){
		
		$all_pages 		= $this->get_pages();
		$page_keys 		= array_keys($all_pages);
		$default_tab 	= ! empty( $all_pages ) ? reset( $page_keys ) : "";
		
		return isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $default_tab;
	}
	private function get_menu_type(){
		if( isset( $this->data['menu_type'] ) ) return $this->data['menu_type'];
		else return "main";
	}
	private function get_pages(){
		if( isset( $this->data['pages'] ) ) return $this->data['pages'];
		else return array();
	}
	private function get_settings_fields(){
		if( isset( $this->get_pages()[$this->get_current_page()]['page_settings'] ) ) return $this->get_pages()[$this->get_current_page()]['page_settings'];
		else return array();
	}
	private function get_settings_name(){
		if( isset( $this->data['settings_name'] ) ) return $this->data['settings_name'];
		else return "my_custom_settings";
	}
	private function get_menu_icon(){
		if( isset( $this->data['menu_icon'] ) ) return $this->data['menu_icon'];
		else return "";
	}
	private function get_menu_slug(){
		if( isset( $this->data['menu_slug'] ) ) return $this->data['menu_slug'];
		else return "my-custom-settings";
	}
	private function get_capability(){
		if( isset( $this->data['capability'] ) ) return $this->data['capability'];
		else return "manage_options";
	}
	private function get_menu_page_title(){
		if( isset( $this->data['menu_page_title'] ) ) return $this->data['menu_page_title'];
		else return "My Custom Menu";
	}
	private function get_menu_name(){
		if( isset( $this->data['menu_name'] ) ) return $this->data['menu_name'];
		else return "Menu Name";
	}
	private function get_menu_title(){
		if( isset( $this->data['menu_title'] ) ) return $this->data['menu_title'];
		else return "Menu Title";
	}
	private function get_page_title(){
		if( isset( $this->data['page_title'] ) ) return $this->data['page_title'];
		else return "Page Title";
	}
	private function add_in_menu(){
		if( isset( $this->data['add_in_menu'] ) && $this->data['add_in_menu'] ) return true;
		else return false;
	}
	private function get_parent_slug(){
		if( isset( $this->data['parent_slug'] ) && $this->data['parent_slug'] ) return $this->data['parent_slug'];
		else return "";
	}
	
}

}


if( ! class_exists( 'Pick_error' ) ) {
	class Pick_error extends Exception { 

		public function __construct($message, $code = 0, Exception $previous = null) {
			parent::__construct($message, $code, $previous);
		}
		
		public function get_error_message(){
			
			return "<p class='notice notice-error' style='padding: 10px;'>{$this->getMessage()}</p>";
		}
	}
}