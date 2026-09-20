<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$home = (int) get_option('page_on_front');
$hero_id = (int) get_option('opd_match_hero_attachment_id', 31);
$hero_src = wp_attachment_is_image($hero_id) ? (string) wp_get_attachment_url($hero_id) : home_url('/wp-content/uploads/2026/09/opd-hero-modular.jpg');
$dir = home_url('/directory/');
$join = get_page_by_path('join') ? home_url('/join/') : home_url('/login/');
$eid = static fn() => substr(bin2hex(random_bytes(4)), 0, 7);
$w = static fn($t,$s) => ['id'=>$eid(),'elType'=>'widget','widgetType'=>$t,'settings'=>$s,'elements'=>[]];
$c = static fn($els,$s=[]) => ['id'=>$eid(),'elType'=>'column','settings'=>array_merge(['_column_size'=>100,'_inline_size'=>null],$s),'elements'=>$els,'isInner'=>false];
$s = static fn($cols,$set=[]) => ['id'=>$eid(),'elType'=>'section','settings'=>$set,'elements'=>$cols,'isInner'=>false];

# Use DIV headings — avoid theme/Elementor h1/h2 chrome that can ::before-duplicate
$html = '<div class="opd-home-hero-inner">'
	. '<div class="opd-home-h1" role="heading" aria-level="1">Engage with the Global Prefab and Offsite Construction Hub</div>'
	. '<p class="opd-home-sub">Discover developers, architects, suppliers, and industry professionals revolutionizing construction through modular and offsite methods.</p>'
	. '<div class="opd-home-ctas">'
	. '<a class="opd-home-cta-primary" href="'.esc_url($join).'">Join Directory →</a>'
	. '<a class="opd-home-cta-ghost" href="'.esc_url($dir).'">Browse Companies</a>'
	. '</div>'
	. '<div class="opd-home-stats" role="group" aria-label="Directory stats">'
	. '<div class="opd-home-stat"><span class="opd-home-stat-num">500+</span><span class="opd-home-stat-label">Companies</span></div>'
	. '<div class="opd-home-stat"><span class="opd-home-stat-num">50+</span><span class="opd-home-stat-label">Countries</span></div>'
	. '<div class="opd-home-stat"><span class="opd-home-stat-num">15</span><span class="opd-home-stat-label">Categories</span></div>'
	. '</div></div>';

$els = [
	$s([$c([$w('opd-directory-header',[])])], [
		'background_background'=>'classic','background_color'=>'#FFFFFF',
		'padding'=>['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true],
	]),
	$s([$c([$w('html',['html'=>$html])])], [
		'layout'=>'full_width','gap'=>'no','height'=>'min-height',
		'custom_height'=>['unit'=>'vh','size'=>78,'sizes'=>[]],
		'background_background'=>'classic','background_color'=>'#0066CC',
		'background_image'=>['url'=>$hero_src,'id'=>$hero_id,'source'=>'library'],
		'background_position'=>'center center','background_repeat'=>'no-repeat','background_size'=>'cover',
		'background_overlay_background'=>'classic','background_overlay_color'=>'#0a1628',
		'background_overlay_opacity'=>['unit'=>'px','size'=>0.55,'sizes'=>[]],
		'background_overlay_blend_mode'=>'normal',
		'css_classes'=>'opd-home-hero-photo',
		'padding'=>['unit'=>'px','top'=>'96','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false],
	]),
	$s([$c([$w('opd-directory-sponsors-ticker',[])])]),
];
$json = wp_json_encode($els);
update_post_meta($home,'_elementor_edit_mode','builder');
update_post_meta($home,'_elementor_template_type','wp-page');
update_post_meta($home,'_elementor_data',wp_slash($json));
update_post_meta($home,'_wp_page_template','elementor_canvas');
wp_update_post(['ID'=>$home,'post_content'=>'','post_status'=>'publish','post_title'=>'Home']);
delete_post_meta($home,'_elementor_css');
delete_post_meta($home,'_elementor_page_assets');
delete_post_meta($home,'_elementor_element_cache');

$css = <<<CSS
/* OPD NUKE GHOST */
.elementor-widget-opd-directory-header .opd-ui{min-height:0!important;height:auto!important;background:transparent!important}
.opd-home-hero-photo{min-height:78vh!important;overflow:hidden!important}
.opd-home-hero-photo .elementor-background-overlay{mix-blend-mode:normal!important;background-blend-mode:normal!important;filter:none!important;opacity:.55!important;z-index:0!important}
.opd-home-hero-photo .elementor-motion-effects-container,
.opd-home-hero-photo .elementor-motion-effects-layer{display:none!important}
.opd-home-hero-photo .elementor-container,.opd-home-hero-photo .elementor-widget-wrap,.opd-home-hero-photo .elementor-widget-container{position:relative!important;z-index:2!important;isolation:isolate!important;filter:none!important;mix-blend-mode:normal!important}
.opd-home-hero-photo *::before,.opd-home-hero-photo *::after{content:none!important;display:none!important;background:none!important;border:0!important;box-shadow:none!important}
.opd-home-hero-inner{max-width:52rem;margin:0 auto;text-align:center;color:#fff;position:relative;z-index:3;isolation:isolate;transform:translateZ(0)}
.opd-home-hero-inner,.opd-home-hero-inner *{text-shadow:none!important;-webkit-text-stroke:0!important;filter:none!important;mix-blend-mode:normal!important;opacity:1!important}
.opd-home-h1{margin:0 auto;font-size:clamp(1.75rem,3.6vw,2.75rem);font-weight:700;line-height:1.2;color:#fff!important}
.opd-home-sub{margin:.85rem auto 0;max-width:44rem;font-size:1.05rem;line-height:1.55;color:rgba(255,255,255,.95)!important}
.opd-home-ctas{display:flex;flex-wrap:wrap;gap:.85rem;justify-content:center;margin:1.35rem auto 0}
.opd-home-cta-primary{display:inline-flex;align-items:center;background:#0066CC!important;color:#fff!important;font-weight:700;font-size:1rem;padding:.85rem 1.4rem;border-radius:8px;text-decoration:none!important;border:2px solid #0066CC}
.opd-home-cta-ghost{display:inline-flex;align-items:center;background:transparent!important;color:#fff!important;font-weight:600;font-size:1rem;padding:.85rem 1.4rem;border-radius:8px;text-decoration:none!important;border:2px solid rgba(255,255,255,.92)}
.opd-home-stats{display:flex;flex-wrap:wrap;justify-content:center;gap:2.5rem;margin:2.5rem auto 0;background:transparent!important}
.opd-home-stat{text-align:center;min-width:6rem;background:transparent!important}
.opd-home-stat-num{display:block;font-size:2.35rem;font-weight:700;color:#fff!important;line-height:1.1}
.opd-home-stat-label{display:block;margin-top:.25rem;color:rgba(255,255,255,.92)!important;font-weight:600;font-size:.95rem}
.opd-home-hero-photo .elementor-widget-heading,
.opd-home-hero-photo .elementor-widget-button,
.opd-home-hero-photo .elementor-widget-text-editor{display:none!important}
.opd-home-hero-photo .elementor-widget-html{display:block!important}
CSS;
update_option('opd_match_custom_css',$css,false);
file_put_contents(WP_CONTENT_DIR.'/mu-plugins/opd-match-brand.php', "<?php\ndefined('ABSPATH')||exit;\nadd_action('wp_head',static function(){\n\$c=(string)get_option('opd_match_custom_css','');\nif(\$c==='')return;\necho \"<style id=\\\"opd-match-brand\\\">\\n{\$c}\\n</style>\\n\";\n},99);\n");

$cssdir = WP_CONTENT_DIR.'/uploads/elementor/css';
$del=0;
if (is_dir($cssdir)) foreach (glob($cssdir.'/post-'.$home.'*.css')?:[] as $f){ @unlink($f); $del++; }
if (class_exists('\\Elementor\\Plugin')) {
	$p=\Elementor\Plugin::$instance;
	if (isset($p->files_manager) && method_exists($p->files_manager,'clear_cache')) $p->files_manager->clear_cache();
}
if (function_exists('wp_cache_flush')) wp_cache_flush();
do_action('litespeed_purge_all');
# Disable common optimizers that can double-paint
update_option('litespeed.conf.optm-css_comb', 0, false);
update_option('litespeed.conf.optm-js_comb', 0, false);
update_option('litespeed.conf.optm-html_min', 0, false);
update_option('litespeed.conf.optm-ucss', 0, false);
update_option('litespeed.conf.optm-ccss_gen', 0, false);

$after=(string)get_post_meta($home,'_elementor_data',true);
$out=['ok'=>true,'nuke'=>true,'home'=>$home,'del_css'=>$del,
	'heading'=>substr_count($after,'"widgetType":"heading"'),
	'html'=>substr_count($after,'"widgetType":"html"'),
	'engage'=>substr_count($after,'Engage with'),
	'browse'=>substr_count($after,'Browse Companies'),
	'uses_div_h1'=> (strpos($after,'opd-home-h1')!==false),
];
file_put_contents(__DIR__.'/opd-nuke-result.json', wp_json_encode($out,JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
