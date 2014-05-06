<?php

class BS_Plugin {
	
	private $_prefix = "bs_";
	private $_name;
	private $_prefixed_name;
	
 	public function __construct( $name ) {  
        $this->_name = $name;  
        $this->_prefixed_name = $this->prefix($this->_name);
        
        if( $this->isEnabled() ) {
	        add_filter( 'mce_external_plugins', array( $this, 'registerPlugin' ) );
	        add_filter( 'mce_buttons_3', array($this, 'registerButton') );
	        add_shortcode($this->_prefixed_name, array( $this, 'shortcode' ) );
       	} 	
    } 
    
    public function registerPlugin($plugins) {
    	$plugins[ $this->_prefixed_name ] = plugins_url('js/plugins/' . $this->_name . '.js', dirname(__FILE__));
    	return $plugins;
    }
    
    public function registerButton($buttons) {
		array_push($buttons, $this->_prefixed_name);
		return $buttons;
    }
	
	public function shortcode($params, $content = null) {
		require_once ( $this->_prefixed_name . ".php" ); 
		return call_user_func_array($this->_prefixed_name, array($params, $content));
	}
	
	public function isEnabled() {
		$options = get_option( "bs_options" );
		$option = ( isset( $options['chk_default_options_' . $this->_name]) ) ? $options['chk_default_options_' . $this->_name] : false;
		$result = (bool) $option;
		return $result;
	}
	
	public function getName() {
		return $this->_name;
	}
		
	/**
	 * Get the prefixed version input $name suitable for storing in WP options
	 * Idempotent: if is already prefixed, it is not prefixed again, it is returned without change
	 * @param  $name string to prefix.
	 * @return string
	 */
	public function prefix($name) {

		if (strpos($name, $this->_prefix) === 0) { // 0 but not false
			return $name; // already prefixed
		}
		return $this->_prefix . $name;
	}
	
	/**
	 * Remove the prefix from the input $name.
	 * Idempotent: If no prefix found, just returns what was input.
	 * @param  $name string
	 * @return string $name without the prefix.
	 */
	public function &unPrefix($name) {

		if (strpos($name, $this->_prefix) === 0) {
			$prefixed = substr($name, strlen($this->_prefix));
			return $prefixed;
		}
		return $name;
	}
}

?>