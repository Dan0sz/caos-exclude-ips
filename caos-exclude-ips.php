<?php
/**
 * Plugin Name: CAOS - Exclude IPs Plugin
 * Description: This extension allows you to exclude certain IP addresses from Google Analytics using CAOS.
 * Version: 1.0.0
 * Author: Daan from Daan.dev
 * Author URI: https://daan.dev
 * License: GPL2v2 or later
 * Text Domain: host-analyticsjs-local
 */

defined('ABSPATH') || exit;

/**
 * Decide whether this IP address should be tracked.
 * 
 * @filter caos_exclude_from_tracking
 * 
 * @param  mixed $exclude 
 * @return void|bool 
 */
function caos_exclude_ip($exclude)
{
    if (CAOS::get('track_administrators') === 'on' && is_user_logged_in() && current_user_can('manage_options')) {
        return $exclude;
    }
    
    /**
     * Use this filter as follows:
     * 
     * add_filter('caos_exclude_ips', function () { return [ '1.1.1.1', '2.2.2.2', '3.3.3.3' ]; });
     */
    $to_exclude = apply_filters('caos_exclude_ips', []);
    $ip         = caos_get_ip();
    
    return in_array($ip, $to_exclude);
}

add_filter('caos_exclude_from_tracking', 'caos_exclude_ip');

/**
 * Checks different headers for the current visitor's IP address and returns it.
 * 
 * @return string IP address.
 */
function caos_get_ip()
{
    $ip_headers = [  
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
        'HTTP_CLIENT_IP',
    ];

    $ip = '';

    foreach ( $ip_headers as $header ) {
        if (isset($_SERVER[ $header ]) && ! empty($_SERVER[ $header ]) ) {
            $ip = $_SERVER[ $header ];

            if (is_array(explode(',', $ip)) ) {
                $ip = explode(',', $ip);

                return $ip[0];
            }

            return $ip;
        }
    }
}
