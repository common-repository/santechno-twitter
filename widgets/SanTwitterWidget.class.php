<?php

class SanTwitterWidget extends WP_Widget {
    /** Widget setup **/
    function SanTwitterWidget() {
        parent::WP_Widget(
            false, __( 'SANTechno Twitter Widget', 'mytextdomain' ),
            array('description' => __( 'Displays a list of tweets from a specified user name', 'mytextdomain' )),
            array('width' => '400px')
        );
    }
    /** The back-end form **/
    function form( $instance ) {

	$defaults = array(
        'twitter_consumer_key' => '',
        'twitter_consumer_secret' => '',
        'twitter_access_token' => '',
        'twitter_access_token_secret' => '',
        'title'    => '',
        'limit'    => 5,
        'username' => 'nabazmaaruf'
    );
	$values = wp_parse_args( $instance, $defaults );
?>
        <p>
            <label for='<?php echo $this->get_field_id( 'twitter_consumer_key' ); ?>'>
                <?php _e( 'Consumer Key:', 'mytextdomain' ); ?>
                <input class='widefat' id='<?php echo $this->get_field_id( 'twitter_consumer_key' ); ?>' name='<?php echo $this->get_field_name( 'twitter_consumer_key' ); ?>' type='text' value='<?php echo $values['twitter_consumer_key']; ?>' />
            </label>
        </p>
        <p>
            <label for='<?php echo $this->get_field_id( 'twitter_consumer_secret' ); ?>'>
                <?php _e( 'Consumer Secret:', 'mytextdomain' ); ?>
                <input class='widefat' id='<?php echo $this->get_field_id( 'twitter_consumer_secret' ); ?>' name='<?php echo $this->get_field_name( 'twitter_consumer_secret' ); ?>' type='text' value='<?php echo $values['twitter_consumer_secret']; ?>' />
            </label>
        </p>
        <p>
            <label for='<?php echo $this->get_field_id( 'twitter_access_token' ); ?>'>
                <?php _e( 'Access Token:', 'mytextdomain' ); ?>
                <input class='widefat' id='<?php echo $this->get_field_id( 'twitter_access_token' ); ?>' name='<?php echo $this->get_field_name( 'twitter_access_token' ); ?>' type='text' value='<?php echo $values['twitter_access_token']; ?>' />
            </label>
        </p>
        <p>
            <label for='<?php echo $this->get_field_id( 'twitter_access_token_secret' ); ?>'>
                <?php _e( 'Access Token Secret:', 'mytextdomain' ); ?>
                <input class='widefat' id='<?php echo $this->get_field_id( 'twitter_access_token_secret' ); ?>' name='<?php echo $this->get_field_name( 'twitter_access_token_secret' ); ?>' type='text' value='<?php echo $values['twitter_access_token_secret']; ?>' />
            </label>
        </p>

<p>
    <label for='<?php echo $this->get_field_id( 'title' ); ?>'>
        <?php _e( 'Title:', 'mytextdomain' ); ?>
        <input class='widefat' id='<?php echo $this->get_field_id( 'title' ); ?>' name='<?php echo $this->get_field_name( 'title' ); ?>' type='text' value='<?php echo $values['title']; ?>' />
    </label>
</p>

<p>
    <label for='<?php echo $this->get_field_id( 'limit' ); ?>'>
        <?php _e( 'Tweets to show:', 'mytextdomain' ); ?>
        <input class='widefat' id='<?php echo $this->get_field_id( 'limit' ); ?>' name='<?php echo $this->get_field_name( 'limit' ); ?>' type='text' value='<?php echo $values['limit']; ?>' />
    </label>
</p>

<p>
    <label for='<?php echo $this->get_field_id( 'username' ); ?>'>
        <?php _e( 'Twitter user name:', 'mytextdomain' ); ?>
        <input class='widefat' id='<?php echo $this->get_field_id( 'username' ); ?>' name='<?php echo $this->get_field_name( 'username' ); ?>' type='text' value='<?php echo $values['username']; ?>' />
    </label>
</p>
<?php
    }
    /** Saving form data **/
    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
    /** The front-end display **/
    function widget( $args, $instance ) {
        $tweets = $this->get_tweets( $args['widget_id'], $instance );
        if( !empty( $tweets['tweets'] ) AND empty( $tweets['tweets']->errors ) ) {

            echo $args['before_widget'];
            echo $args['before_title'] . $instance['title'] .  $args['after_title'];

            $user = current( $tweets['tweets'] );
            $user = $user->user;

           echo '
		<div class="twitter-profile">
		<img src="' . $user->profile_image_url . '">
		<h2><a class="heading-text-color" href="http://twitter.com/' . $user->screen_name . '">' . $user->screen_name . '</a></h2>
		<div class="description content">' . $user->description . '</div>
		</div>	';

            echo '<ul>';
            foreach( $tweets['tweets'] as $tweet ) {
                if( is_object( $tweet ) ) {
                    $tweet_text = htmlentities($tweet->text, ENT_QUOTES);
                    $tweet_text = preg_replace( '/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', 'http://$1', $tweet_text );

                    echo '
				<li>
					<span class="content">' . $tweet_text . '</span>
					<div class="date">' . human_time_diff( strtotime( $tweet->created_at ) ) . ' ago </div>
				</li>';
                }
            }
            echo '</ul>';
            echo $args['after_widget'];

        }

    }

    function retrieve_tweets( $widget_id, $instance ) {

        global $cb;

        $consumer_key = $instance['twitter_consumer_key'];
        $consumer_secret = $instance['twitter_consumer_secret'];
        $access_token = $instance['twitter_access_token'];
        $access_secret = $instance['twitter_access_token_secret'];

        include_once dirname( __FILE__ ) .'/codebird.php';
        \Codebird\Codebird::setConsumerKey( $consumer_key, $consumer_secret );
        $cb = \Codebird\Codebird::getInstance();
        $cb->setToken( $access_token, $access_secret );
        $timeline = $cb->statuses_userTimeline( 'screen_name=' . $instance['username']. '&count=' . $instance['limit'] . '&exclude_replies=true' );
        return $timeline;
    }

    function save_tweets( $widget_id, $instance ) {
        $timeline = $this->retrieve_tweets( $widget_id, $instance );
        $tweets = array( 'tweets' => $timeline, 'update_time' => time() + ( 60 * 5 ) );
        update_option( 'my_tweets_' . $widget_id, $tweets );
        return $tweets;
    }
    function get_tweets( $widget_id, $instance ) {
        $tweets = get_option( 'my_tweets_' . $widget_id );
        if( empty( $tweets ) OR time() > $tweets['update_time'] ) {
            $tweets = $this->save_tweets( $widget_id, $instance );
        }
        return $tweets;
    }


}

//register_widget('SanTwitterWidget');

add_action('widgets_init', 'register_santwitterwidget');
function register_santwitterwidget(){
    register_widget('SanTwitterWidget');
}

