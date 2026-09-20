<?php
require __DIR__.'/wp-load.php';
$home=(int)get_option('page_on_front');
$data=get_post_meta($home,'_elementor_data',true);
$css=(string)get_option('opd_match_custom_css','');
$hero=(int)get_option('opd_match_hero_attachment_id',0);
$n= (new WP_Query(['post_type'=>'opd_company','post_status'=>'publish','posts_per_page'=>1,'fields'=>'ids']))->found_posts;
$kit=(int)get_option('elementor_active_kit');
$ks=get_post_meta($kit,'_elementor_page_settings',true);
$out=[
 'home'=>$home,
 'companies'=>$n,
 'hero_id'=>$hero,
 'hero_url'=>wp_get_attachment_url($hero),
 'has_hero_img'=>is_string($data)&&str_contains($data,'opd-hero-modular'),
 'has_header_widget'=>is_string($data)&&str_contains($data,'opd-directory-header'),
 'has_overlay'=>is_string($data)&&str_contains($data,'background_overlay'),
 'css_has_0066CC'=>str_contains($css,'0066CC'),
 'kit_primary'=>$ks['system_colors'][0]['color']??null,
 'front_css_snip'=>substr((string)@file_get_contents(WP_CONTENT_DIR.'/plugins/ong-directory/assets/css/front.css'),0,400),
];
header('Content-Type: application/json');
echo json_encode($out,JSON_PRETTY_PRINT);
@unlink(__FILE__);
