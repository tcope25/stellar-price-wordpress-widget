<?php
/**
 * Plugin Name: Stellar Price Widget
 * Plugin URI: https://www.stellarwidget.com
 * Description: This plugin adds the Stellar Lumens latest price to your site
 * Version: 1.0.0
 * Author: Trey Copeland
 * Author URI: https://www.treycopeland.com
 * License: GPL2
 */
 
 define ( 'URL', 'https://stellarwidget.com/api/price/');
 
// The widget class
class Stellar_Price_Widget extends WP_Widget {

	protected $url = URL;

	// Main constructor
	public function __construct() {
		parent::__construct(
			'stellar_price_widget',
			__( 'Stellar Price', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'text'     => '',
			'textarea' => '',
			'calculator' => '',
			'usdvalue' => '1',
			'btcvalue' => '',
			'select'   => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php // USD Checkbox ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'usdvalue' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'usdvalue' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $usdvalue ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'usdvalue' ) ); ?>"><?php _e( 'Show price in USD', 'text_domain' ); ?></label>
		</p>

		<?php // BTC Checkbox ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'btcvalue' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'btcvalue' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $btcvalue ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'btcvalue' ) ); ?>"><?php _e( 'Show price in BTC', 'text_domain' ); ?></label>
		</p>

		<?php // Calculator Checkbox ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'calculator' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'calculator' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $calculator ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'calculator' ) ); ?>"><?php _e( 'Show Calculator', 'text_domain' ); ?></label>
		</p>

		
	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['calculator'] = isset( $new_instance['calculator'] ) ? 1 : false;
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : '';
		$instance['btcvalue'] = isset( $new_instance['btcvalue'] ) ? 1 : false;
		$instance['usdvalue'] = isset( $new_instance['usdvalue'] ) ? 1 : false;
		
		
		return $instance;
	}

	
	// Display the widget
	public function widget( $args, $instance ) {

		extract( $args );
				
		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$calculator = ! empty( $instance['calculator'] ) ? $instance['calculator'] : false;
		$btcvalue = ! empty( $instance['btcvalue'] ) ? $instance['btcvalue'] : false;
		$usdvalue = ! empty( $instance['usdvalue'] ) ? $instance['usdvalue'] : false;

		$jsonObj = json_decode($this->getPrice($this->url), true);		
		
		// WordPress core before_widget hook (always include )
		echo $before_widget;

	
	   // Display the widget
	   echo '<div class="widget-text wp_widget_plugin_box">';
	   
   
			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}	

			echo '
			<div style="text-align:center;">
				<div style="border:1px solid #e4e4e4;border-radius:6px;font-family:Helvetica,Arial,sans-serif;padding-top:6px;">
					<div style="color:#08b5e5;text-align:center;font-size:1em;padding:8px;"><img src="https://stellarwidget.com/files/stellar-rocket.png"><a href="https://www.stellar.org/" target="_blank" style="color:#08b5e5;r">Stellar Lumens (XLM)</a></div>';
					if ($usdvalue) {
					echo '
					<div style="padding-bottom:10px;font-size:.8em;color:#656D6D; text-align: center;" id="currentprice" amount="'.$jsonObj[0]['price_usd'].'">Current price: <strong>'. $jsonObj[0]['price_usd'] . '</strong> USD 
					<span style="';
					if ($jsonObj[0]['percent_change_24h']>0) { echo "color:green;"; } else { echo "color:red;"; }
					echo '">
					('.$jsonObj[0]['percent_change_24h'].'%)
					</span>
					</div>';
					}
					if ($btcvalue) {
					echo '
					<div style="padding-bottom:10px;font-size:.8em;color:#656D6D; text-align: center;" id="currentprice" amount="'.$jsonObj[0]['price_btc'].'">Current price: <strong>'. $jsonObj[0]['price_btc'] . '</strong> BTC</div>';
					}	
					if ($calculator) {
							echo '
								<input type="text" style="display:none;" id="curprice" value="'.$jsonObj[0]['price_usd'].'">
								<input type="text" placeholder="Enter XML amount" style="padding:5px;text-align:center;width:130px;font-size:.8em;" id="stellaramount" oninput="multiplyBy();">
								<div style="color:green;padding-bottom:10px;padding-top:10px;" id="amountinusd"> USD</div>
								';
					}
			echo '
				</div>
			</div>
			';
			

			
		echo '
		<script type="text/javascript">pcentColor();</script>
		</div>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;

	}

	public function getPrice($url) {
	
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
			$price = curl_exec($ch);
		
			if (!is_string($price) || !strlen($price)) {
			$price = '0.00';
			}
			
			curl_close($ch);

		return $price;
	
	}	
	
}

add_action('wp_enqueue_scripts','stellar_widget_script');

function stellar_widget_script() {
    wp_enqueue_script( 'stellar', plugins_url( 'stellar-scripts.js', __FILE__ ));
}

// Register the widget
function my_register_stellar_widget() {
	register_widget( 'Stellar_Price_Widget' );
}
add_action( 'widgets_init', 'my_register_stellar_widget' );