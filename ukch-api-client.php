<?php
   /*
   Plugin Name: UKCH API Client
   Plugin URI: https://github.com/scman1
   description: >-
  a plugin to get data from UKCH API
   Version: 0.1
   Author: ANH
   Author URI: https://github.com/scman1
   License: GPL2
   */
  

function ukchapi_get_as_json( $action, $params ) {
 
    $api_endpoint = "http://188.166.149.246/";
 
    if ( null == $params ) {
        $params = array();
    }
 
    // Create URL with params
    $url = $api_endpoint . $action . '?' . http_build_query($params);
 
    // Use curl to make the query
    $ch = curl_init();
 
    curl_setopt_array(
        $ch, array( 
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        )
    );
 
    $output = curl_exec($ch);
 
    // Decode output into an array
    $json_data = json_decode( $output, true );
 
    curl_close( $ch );
 
    return $json_data;
}

function show_publications_list() {
	 $params = array(
				'page' => '1'
			);	
    $pubs_data = ukchapi_get_as_json('articles.json', $params); //decoded json data
?>    
    <div class="publications-list">
        <ul>
        <?php if (count($pubs_data) > 0) : ?>
 
            <?php foreach ($pubs_data as $item) : ?>  
                <li>
                    <?php echo $item["title"]; ?>, <?php echo $item["container_title"]; ?>,
                    <?php echo $item["doi"]; ?>
                </li>
            <?php endforeach; ?>
 
        <?php else : ?>
            Your shopping cart is empty.
        <?php endif; ?>
        </ul>
    </div>
<?php
}

// Creating the widget 
class uac_widget extends WP_Widget {
  
function __construct() {
parent::__construct(
  
// Base ID of your widget
'uac_widget', 
  
// Widget name will appear in UI
__('UKCH API Client Widget', 'uac_widget_domain'), 
  
// Widget description
array( 'description' => __( 'Widget for querying UKCH rails API', 'uac_widget_domain' ), ) 
);
}
  
// Creating widget front-end
  
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
  
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
show_publications_list();
echo $args['after_widget'];
}
          
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'uac_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
      
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
 
// Class wpb_widget ends here
} 
 
// Register and load the widget
function uac_load_widget() {
    register_widget( 'uac_widget' );
}
add_action( 'widgets_init', 'uac_load_widget' );
