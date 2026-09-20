<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$home = (int) get_option('page_on_front');
$data = (string) get_post_meta($home, '_elementor_data', true);
// Render front page HTML via HTTP to self if possible
$html = '';
$resp = wp_remote_get(home_url('/?deghost_dump=1'), ['timeout' => 30, 'sslverify' => false]);
if (!is_wp_error($resp)) {
  $html = (string) wp_remote_retrieve_body($resp);
}
$out = [
  'home' => $home,
  'data_engage' => substr_count($data, 'Engage with'),
  'data_browse' => substr_count($data, 'Browse Companies'),
  'data_join' => substr_count($data, 'Join Directory'),
  'data_heading_widgets' => substr_count($data, '"widgetType":"heading"'),
  'data_button_widgets' => substr_count($data, '"widgetType":"button"'),
  'data_html_widgets' => substr_count($data, '"widgetType":"html"'),
  'data_has_hero_class' => (strpos($data, 'opd-home-hero-photo') !== false),
  'html_len' => strlen($html),
  'html_engage' => substr_count($html, 'Engage with'),
  'html_browse' => substr_count($html, 'Browse Companies'),
  'html_join' => substr_count($html, 'Join Directory'),
  'html_500' => substr_count($html, '500+'),
  'html_nav_directory' => substr_count($html, '>Directory<'),
  'html_nav_members' => substr_count($html, '>Members<'),
  'html_nav_about' => substr_count($html, '>About<'),
  'html_welcome' => substr_count($html, 'Welcome to Offsite'),
  'css_option_has_deghost' => (strpos((string) get_option('opd_match_custom_css', ''), 'DEGHOST') !== false),
];
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
