<?php
/*
Name: Irish Shipping Module with Fastway Integration
Module URI: http://abandon.ie/
Description: This is a module made for Jigoshop that calculates Irish An Post Shipping rates. You can find info on these rates at http://www.anpost.ie/AnPost/PostalRates/Standard+Post.htm
Author: Abban Dunne.
Version: 1.0
*/

class irish_shipping extends jigoshop_shipping_method {
	
	public function __construct() { 
        $this->id 			= 'irish_shipping';
        $this->enabled		= get_option('jigoshop_irish_shipping_enabled');
		$this->title 		= get_option('jigoshop_irish_shipping_title');
		$this->availability = get_option('jigoshop_irish_shipping_availability');
		$this->countries 	= get_option('jigoshop_irish_shipping_countries');
		
		if (isset($_SESSION['_chosen_method_id']) && $_SESSION['_chosen_method_id']==$this->id) $this->chosen = true;
		
		add_action('jigoshop_update_options', array(&$this, 'process_admin_options'));
		add_option('jigoshop_irish_shipping_availability', 'all');
		add_option('jigoshop_irish_shipping_title', 'Irish Shipping');
    } 
    
    public function calculate_shipping() {
    	$this->shipping_total 	= 0;
		$this->shipping_tax 	= 0;
		$this->europe_countries	= array('AT','BE','BG','CY','CZ','DK','EE','FI','FR','DE','GR','NL','HU','IT',
										'LV','LT','LU','MT','PL','PT','RO','SK','SI','ES','SE','AL','AD','BY',
										'BA','HR','FO','GE','GI','GL','GG','IS','JE','LI','MK','MD','ME','NO',
										'RU','SM','RS','CH','TR','UA','VA');
		
		//jigoshop::clear_messages(); IF ANYONE CAN TELL ME HOW TO CLEAR FREAKIN MESSAGES OUTSIDE THE SESSION DROP ME A MAIL: himself@abandon.ie
    	
    	if ($_SESSION['_chosen_method_id'] == 'irish_shipping') :
			if (sizeof(jigoshop_cart::$cart_contents)>0) : 
				foreach (jigoshop_cart::$cart_contents as $values) :
					$_product = $values['data'];
					if ($_product->exists() && $values['quantity']>0) $weight += $_product->get_weight()*$values['quantity'];
				endforeach;
				
				if(jigoshop_customer::get_country()=='IE'):
				
					if($weight>=0 && $weight<1.499) : $this->shipping_total = 6.5;
						
					elseif($weight>=1.5 && $weight<2.499) : $this->shipping_total = 7.5;
						
					elseif($weight>=2.5 && $weight<2.999) : $this->shipping_total = 8.7;
						
					elseif($weight>=3 && $weight<3.499) : $this->shipping_total = 9.8;
						
					elseif($weight>=3.5 && $weight<3.999) : $this->shipping_total = 11;
						
					elseif($weight>=4 && $weight<4.499) : $this->shipping_total = 12;
						
					elseif($weight>=4.5 && $weight<4.999) : $this->shipping_total = 13;
						
					elseif($weight>=5 && $weight<=20) : $this->shipping_total = 13 + (floor($weight)-4);
						
					else: $this->irish_shipping_error_message(); endif;
					
				elseif(jigoshop_customer::get_country()=='GB'):
				
					if($weight>=0 && $weight<1.499) : $this->shipping_total = 18.25;
						
					elseif($weight>=1.5 && $weight<2.499) : $this->shipping_total = 22;
						
					elseif($weight>=2 && $weight<2.999) : $this->shipping_total = 25;
						
					elseif($weight>=2.5 && $weight<2.999) : $this->shipping_total = 27.5;
						
					elseif($weight>=3 && $weight<3.499) : $this->shipping_total = 31;
						
					elseif($weight>=3.5 && $weight<3.999) : $this->shipping_total = 33;
						
					elseif($weight>=4 && $weight<4.499) : $this->shipping_total = 35;
						
					elseif($weight>=4.5 && $weight<4.999) : $this->shipping_total = 37.5;
						
					elseif($weight>=5 && $weight<=20) : $this->shipping_total = 39 + (floor($weight)-4);
						
					else: $this->irish_shipping_error_message(); endif;
				
				else:
				
					if($weight>=0 && $weight<1.499) : $this->shipping_total = 22;
						
					elseif($weight>=1.5 && $weight<2.499) : $this->shipping_total = 28;
						
					elseif($weight>=2 && $weight<2.999) : $this->shipping_total = 30;
						
					elseif($weight>=2.5 && $weight<2.999) : $this->shipping_total = 34;
						
					elseif($weight>=3 && $weight<3.499) : $this->shipping_total = 38;
						
					elseif($weight>=3.5 && $weight<3.999) : $this->shipping_total = 44;
						
					elseif($weight>=4 && $weight<4.499) : $this->shipping_total = 48.5;
						
					elseif($weight>=4.5 && $weight<4.999) : $this->shipping_total = 52.5;
					
					elseif($weight>=5 && $weight<5.999) : $this->shipping_total = 55;
						
					elseif($weight>=6 && $weight<=20):
						
						$i = (in_array(jigoshop_customer::get_country(), $this->europe_countries)) ? 3 : 5; 
						
						$this->shipping_total = 55 + ((floor($weight)-5)*$i);
						
					else: $this->irish_shipping_error_message(); endif;
				
				endif;
			endif;
		endif;	
    }
    
    public function irish_shipping_error_message(){
    	jigoshop::add_error( __('Sorry, the max weight we are able to ship is 20kg. Please edit your cart and try again.', 'jigoshop') );
    }
    
    public function admin_options() {
    	?>
    	<thead><tr><th scope="col" width="200px"><?php _e('Irish Shipping', 'jigoshop'); ?></th><th scope="col" class="desc">This is a module for handling Irish An Post Shipping Rates</th></tr></thead>
    	<tr>
	        <td class="titledesc"><?php _e('Enable Irish Shipping', 'jigoshop') ?>:</td>
	        <td class="forminp">
		        <select name="jigoshop_irish_shipping_enabled" id="jigoshop_irish_shipping_enabled" style="min-width:100px;">
		            <option value="yes" <?php if (get_option('jigoshop_irish_shipping_enabled') == 'yes') echo 'selected="selected"'; ?>><?php _e('Yes', 'jigoshop'); ?></option>
		            <option value="no" <?php if (get_option('jigoshop_irish_shipping_enabled') == 'no') echo 'selected="selected"'; ?>><?php _e('No', 'jigoshop'); ?></option>
		        </select>
	        </td>
	    </tr>
	    <tr>
	        <td class="titledesc"><a href="#" tip="<?php _e('This controls the title which the user sees during checkout.','jigoshop') ?>" class="tips" tabindex="99"></a><?php _e('Method Title', 'jigoshop') ?>:</td>
	        <td class="forminp">
		        <input type="text" name="jigoshop_irish_shipping_title" id="jigoshop_irish_shipping_title" style="min-width:50px;" value="<?php if ($value = get_option('jigoshop_irish_shipping_title')) echo $value; else echo 'Irish Shipping'; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <td class="titledesc"><?php _e('Method available for', 'jigoshop') ?>:</td>
	        <td class="forminp">
		        <select name="jigoshop_irish_shipping_availability" id="jigoshop_irish_shipping_availability" style="min-width:100px;">
		            <option value="all" <?php if (get_option('jigoshop_irish_shipping_availability') == 'all') echo 'selected="selected"'; ?>><?php _e('All allowed countries', 'jigoshop'); ?></option>
		            <option value="specific" <?php if (get_option('jigoshop_irish_shipping_availability') == 'specific') echo 'selected="selected"'; ?>><?php _e('Specific Countries', 'jigoshop'); ?></option>
		        </select>
	        </td>
	    </tr>
	    <?php
    	$countries = jigoshop_countries::$countries;
    	$selections = get_option('jigoshop_irish_shipping_countries', array());
    	?><tr class="multi_select_countries">
            <td class="titledesc"><?php _e('Specific Countries', 'jigoshop'); ?>:</td>
            <td class="forminp">
            	<div class="multi_select_countries"><ul><?php
        			if ($countries) foreach ($countries as $key=>$val) :
            			                    			
        				echo '<li><label><input type="checkbox" name="jigoshop_irish_shipping_countries[]" value="'. $key .'" ';
        				if (in_array($key, $selections)) echo 'checked="checked"';
        				echo ' />'. __($val, 'jigoshop') .'</label></li>';

            		endforeach;
       			?></ul></div>
       		</td>
       	</tr>
       	<script type="text/javascript">
		jQuery(function() {
			jQuery('select#jigoshop_irish_shipping_availability').change(function(){
				if (jQuery(this).val()=="specific") {
					jQuery(this).parent().parent().next('tr.multi_select_countries').show();
				} else {
					jQuery(this).parent().parent().next('tr.multi_select_countries').hide();
				}
			}).change();
		});
		</script>
    	<?php
    }
    
    public function process_admin_options() {
   		
   		if(isset($_POST['jigoshop_irish_shipping_enabled'])) update_option('jigoshop_irish_shipping_enabled', jigowatt_clean($_POST['jigoshop_irish_shipping_enabled'])); else @delete_option('jigoshop_irish_shipping_enabled');
   		
   		if(isset($_POST['jigoshop_irish_shipping_title'])) update_option('jigoshop_irish_shipping_title', jigowatt_clean($_POST['jigoshop_irish_shipping_title'])); else @delete_option('jigoshop_irish_shipping_title');
   		
   		if(isset($_POST['jigoshop_irish_shipping_availability'])) update_option('jigoshop_irish_shipping_availability', jigowatt_clean($_POST['jigoshop_irish_shipping_availability'])); else @delete_option('jigoshop_irish_shipping_availability');
	    
	    if (isset($_POST['jigoshop_irish_shipping_countries'])) $selected_countries = $_POST['jigoshop_irish_shipping_countries']; else $selected_countries = array();
	    update_option('jigoshop_irish_shipping_countries', $selected_countries);
   		
    }
    	
}

function add_irish_shipping_method( $methods ) {
	$methods[] = 'irish_shipping'; return $methods;
}

add_filter('jigoshop_shipping_methods', 'add_irish_shipping_method' );