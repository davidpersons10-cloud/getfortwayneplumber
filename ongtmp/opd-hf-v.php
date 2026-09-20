<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$home = (int) get_option('page_on_front');
$data = (string) get_post_meta($home, '_elementor_data', true);
$css = (string) get_option('opd_match_custom_css', '');
$sc = WP_CONTENT_DIR . '/plugins/ong-directory/includes/class-shortcodes.php';
$sc_raw = is_readable($sc) ? (string) file_get_contents($sc) : '';
$front = WP_CONTENT_DIR . '/plugins/ong-directory/assets/css/front.css';
$front_raw = is_readable($front) ? (string) file_get_contents($front) : '';
$out = [
  'home' => $home,
  'has_hero_class' => (strpos($data, 'opd-home-hero-photo') !== false),
  'has_join_cta' => (strpos($data, 'Join Directory') !== false),
  'has_browse_cta' => (strpos($data, 'Browse Companies') !== false),
  'has_welcome' => (strpos($data, 'Welcome to Offsite Pro Directory') !== false),
  'css_has_header_fix' => (strpos($css, 'elementor-widget-opd-directory-header') !== false),
  'front_has_header_fix' => (strpos($front_raw, 'OPD-HOMEFIX-20260920') !== false),
  'nav_has_directory_label' => (strpos($sc_raw, "__( 'Directory', 'ong-directory' )") !== false),
  'nav_still_has_members_item' => (strpos($sc_raw, "__( 'Members', 'ong-directory' ), 'url' => home_url( '/members/' )") !== false),
  'nav_has_about' => (strpos($sc_raw, "__( 'About', 'ong-directory' )") !== false),
  'nav_has_resources' => (strpos($sc_raw, "__( 'Resources', 'ong-directory' )") !== false),
  'nav_has_contact_us' => (strpos($sc_raw, "__( 'Contact Us', 'ong-directory' )") !== false),
  'hero_id' => (int) get_option('opd_match_hero_attachment_id', 0),
  'homefix_log' => get_option('opd_homefix_log'),
];
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
