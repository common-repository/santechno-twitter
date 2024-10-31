<?php

function retrieve_tweets( $widget_id, $instance ) {

    global $cb;
    $consumer_key = $instance['twitter_consumer_key'];
    $consumer_secret = $instance['twitter_consumer_secret'];
    $access_token = $instance['twitter_access_token'];
    $access_secret = $instance['twitter_access_token_secret'];

    include('codebird.php');
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