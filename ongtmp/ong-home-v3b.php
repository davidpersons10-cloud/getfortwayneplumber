<?php
/**
 * Plugin Name: ONG Home Hub v3b
 * Description: Renders the approved ONG homepage mock v3b (photos throughout) via shortcode [ong_home_v3b]. Local assets only; no sticky mock nav.
 * Version: 1.0.6
 * Author: Offsite Network Global
 * Text Domain: ong-home-v3b
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ONG_HOME_V3B_VERSION', '1.0.5' );
define( 'ONG_HOME_V3B_FILE', __FILE__ );
define( 'ONG_HOME_V3B_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Asset URL helper.
 *
 * @param string $rel Path under assets/.
 * @return string
 */
function ong_home_v3b_asset( $rel ) {
	return plugins_url( 'assets/' . ltrim( $rel, '/' ), ONG_HOME_V3B_FILE );
}

/**
 * Early enqueue when front page or post content contains the shortcode.
 */
function ong_home_v3b_enqueue_on_demand() {
	if ( is_admin() ) {
		return;
	}
	$need = false;
	if ( is_front_page() || is_home() ) {
		$need = true;
	} elseif ( is_singular() ) {
		$post = get_post();
		if ( $post && ( has_shortcode( $post->post_content, 'ong_home_v3b' ) || false !== strpos( $post->post_content, '[ong_home_v3b]' ) ) ) {
			$need = true;
		}
	}
	if ( $need ) {
		ong_home_v3b_enqueue_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'ong_home_v3b_enqueue_on_demand', 20 );

/**
 * Register / enqueue stylesheet + CSS custom properties for hero/CTA backgrounds.
 * Safe to call from shortcode (Elementor late render): prints link in footer if head already sent.
 */
function ong_home_v3b_enqueue_assets() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$href = ong_home_v3b_asset( 'ong-home-v3b.css' );
	wp_register_style( 'ong-home-v3b', $href, array(), ONG_HOME_V3B_VERSION );
	wp_enqueue_style( 'ong-home-v3b' );

	$hero_full = ong_home_v3b_asset( 'warehouse.webp' );
	$hero_1200 = ong_home_v3b_asset( 'warehouse-1200.webp' );
	$cta_full  = ong_home_v3b_asset( 'crane.webp' );
	$cta_800   = ong_home_v3b_asset( 'crane-800.webp' );
	// Mobile-first: no competing CSS hero photo under 901px (LCP img is enough).
	$css      = sprintf(
		'.ong-home-v3b{--ong-hero-bg:none;--ong-cta-bg:none;}'
		. '@media(min-width:901px){.ong-home-v3b{--ong-hero-bg:url(%1$s);--ong-cta-bg:url(%2$s);}}'
		. '@media(min-width:1201px){.ong-home-v3b{--ong-hero-bg:url(%3$s);--ong-cta-bg:url(%4$s);}}',
		esc_url( $hero_1200 ),
		esc_url( $cta_800 ),
		esc_url( $hero_full ),
		esc_url( $cta_full )
	);
	wp_add_inline_style( 'ong-home-v3b', $css );

	// If rendering after wp_head (Elementor), force print in footer.
	if ( did_action( 'wp_head' ) ) {
		add_action(
			'wp_footer',
			static function () use ( $href, $css ) {
				printf(
					'<link rel="stylesheet" id="ong-home-v3b-css" href="%s" media="all" />' . "
" . '<style id="ong-home-v3b-inline-css">%s</style>' . "
",
					esc_url( $href . '?ver=' . rawurlencode( ONG_HOME_V3B_VERSION ) ),
					$css // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URLs already esc_url'd
				);
			},
			5
		);
	}
}

/**
 * Preload responsive LCP hero WebP early (mobile-first src).
 */
function ong_home_v3b_preload_lcp() {
	if ( is_admin() ) {
		return;
	}
	if ( ! ( is_front_page() || is_home() ) ) {
		return;
	}
	$w640  = ong_home_v3b_asset( 'assembly-640.webp' );
	$w800  = ong_home_v3b_asset( 'assembly-800.webp' );
	$w1080 = ong_home_v3b_asset( 'assembly-1080.webp' );
	$full  = ong_home_v3b_asset( 'assembly.webp' );
	$srcset = esc_attr( $w640 . ' 640w, ' . $w800 . ' 800w, ' . $w1080 . ' 1080w, ' . $full . ' 1400w' );
	$sizes  = esc_attr( '(max-width: 900px) 100vw, 550px' );
	printf(
		'<link rel="preload" as="image" type="image/webp" href="%1$s" imagesrcset="%2$s" imagesizes="%3$s" fetchpriority="high" />' . "\n",
		esc_url( $w800 ),
		$srcset,
		$sizes
	);
}
add_action( 'wp_head', 'ong_home_v3b_preload_lcp', 2 );

/**
 * Picture helper: WebP source + JPEG/PNG img fallback.
 *
 * @param string $base       Filename without extension (e.g. meeting).
 * @param array  $atts       img attributes: alt, width, height, class, loading, fetchpriority, decoding.
 * @param string $fallback   Extension for fallback img (jpg|png).
 * @return string
 */
function ong_home_v3b_picture( $base, $atts = array(), $fallback = 'jpg' ) {
	$defaults = array(
		'alt'           => '',
		'width'         => '',
		'height'        => '',
		'class'         => '',
		'loading'       => 'lazy',
		'fetchpriority' => '',
		'decoding'      => 'async',
		'sizes'         => '',
	);
	$a = array_merge( $defaults, $atts );

	$webp = ong_home_v3b_asset( $base . '.webp' );
	$fb   = ong_home_v3b_asset( $base . '.' . $fallback );

	$img_attrs = array(
		'src="' . esc_url( $fb ) . '"',
		'alt="' . esc_attr( $a['alt'] ) . '"',
	);
	if ( $a['width'] ) {
		$img_attrs[] = 'width="' . esc_attr( $a['width'] ) . '"';
	}
	if ( $a['height'] ) {
		$img_attrs[] = 'height="' . esc_attr( $a['height'] ) . '"';
	}
	if ( $a['class'] ) {
		$img_attrs[] = 'class="' . esc_attr( $a['class'] ) . '"';
	}
	if ( $a['loading'] ) {
		$img_attrs[] = 'loading="' . esc_attr( $a['loading'] ) . '"';
	}
	if ( $a['fetchpriority'] ) {
		$img_attrs[] = 'fetchpriority="' . esc_attr( $a['fetchpriority'] ) . '"';
	}
	if ( $a['decoding'] ) {
		$img_attrs[] = 'decoding="' . esc_attr( $a['decoding'] ) . '"';
	}

	// LCP hero: single in-flow img with responsive WebP srcset (mobile-sized candidate).
	if ( ! empty( $a['fetchpriority'] ) && 'high' === $a['fetchpriority'] ) {
		$w640  = ong_home_v3b_asset( $base . '-640.webp' );
		$w800  = ong_home_v3b_asset( $base . '-800.webp' );
		$w1080 = ong_home_v3b_asset( $base . '-1080.webp' );
		$img_attrs[0] = 'src="' . esc_url( $w800 ) . '"';
		$img_attrs[]  = 'srcset="' . esc_attr( $w640 . ' 640w, ' . $w800 . ' 800w, ' . $w1080 . ' 1080w, ' . $webp . ' 1400w' ) . '"';
		$img_attrs[]  = 'sizes="(max-width: 900px) 100vw, 550px"';
		return '<img ' . implode( ' ', $img_attrs ) . ' />';
	}

	// Pathway / card photos: true display-size WebP srcset when variants exist on disk.
	$w400_path = ONG_HOME_V3B_DIR . 'assets/' . $base . '-400.webp';
	$w800_path = ONG_HOME_V3B_DIR . 'assets/' . $base . '-800.webp';
	if ( file_exists( $w400_path ) && file_exists( $w800_path ) ) {
		$w400 = ong_home_v3b_asset( $base . '-400.webp' );
		$w800 = ong_home_v3b_asset( $base . '-800.webp' );
		$sizes = ! empty( $a['sizes'] ) ? $a['sizes'] : '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 360px';
		$srcset = esc_attr( $w400 . ' 400w, ' . $w800 . ' 800w, ' . $webp . ' 1200w' );
		return '<picture>'
			. '<source type="image/webp" srcset="' . $srcset . '" sizes="' . esc_attr( $sizes ) . '" />'
			. '<img ' . implode( ' ', $img_attrs ) . ' />'
			. '</picture>';
	}

	return '<picture>'
		. '<source srcset="' . esc_url( $webp ) . '" type="image/webp" />'
		. '<img ' . implode( ' ', $img_attrs ) . ' />'
		. '</picture>';
}

/**
 * Shortcode [ong_home_v3b]
 *
 * @return string
 */
function ong_home_v3b_shortcode() {
	ong_home_v3b_enqueue_assets();

	ob_start();
	?>
<div class="ong-home-v3b">

	<!-- Announcement strip -->
	<div class="announce" role="status">
		<div class="wrap announce-inner">
			<span><strong>Knowledge Center</strong> live</span>
			<span class="sep" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Browse careers</a>
			<span class="sep" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( home_url( '/tools/tools-calculator/' ) ); ?>">Tools calculator open</a>
		</div></div>

	<!-- Hero: CSS warehouse bg + ONE in-flow LCP img (assembly) -->
	<section class="hero" aria-label="Hero">
		<div class="wrap hero-grid">
			<div>
				<p class="kicker">Offsite Network Global</p>
				<p class="tagline">Connect. Learn. Build.</p>
				<h1>The network for modular, prefab &amp; offsite builders</h1>
				<p class="sub">Factories, fabricators, and MMC teams — one place for membership, chapter learning, certifications, careers, and tools that move offsite forward.</p>
				<div class="hero-ctas">
					<a class="btn btn-join" href="<?php echo esc_url( home_url( '/join/' ) ); ?>">Join the Network</a>
					<a class="btn btn-ghost" href="#ong-pillars">Explore pathways</a>
					<a class="text-link" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Browse open roles</a>
				</div>
			</div>
			<div class="hero-photo-card">
				<?php
				echo ong_home_v3b_picture(
					'assembly',
					array(
						'class'         => 'ong-lcp-hero-img',
						'alt'           => 'Crew assembling modular structure on site — module set and install',
						'width'         => '1400',
						'height'        => '927',
						'fetchpriority' => 'high',
						'loading'       => 'eager',
						'decoding'      => 'async',
					)
				);
				?>
			</div>
		</div>
	</section>

	<!-- Trust strip hidden 2026-09-18 audit (Partner A/B placeholders) -->

	<!-- Three pathway pillars -->
	<section class="sec" id="ong-pillars">
		<div class="wrap">
			<p class="eyebrow">How to engage</p>
			<h2>Three pathways into the network</h2>
			<p class="lead">Networking, professional development, and knowledge — built for factories, fabricators, and offsite teams. Pick the door that fits how you grow.</p>
			<div class="pillars">
				<article class="pillar" id="ong-members">
					<?php
					echo ong_home_v3b_picture(
						'meeting',
						array(
							'class'  => 'pillar-photo',
							'alt'    => 'Professionals networking — join the ONG network',
							'width'  => '1200',
							'height' => '800',
						)
					);
					?>
					<div class="pillar-body">
						<p class="pillar-num">01 — Membership</p>
						<h3>Join the Network</h3>
						<p>Belong with peers who design, fabricate, and install modular buildings. Directory access, chapter roots, and a seat at the MMC table.</p>
						<p class="pillar-meta">Chapters: Chicago · Atlanta · Idaho</p>
						<a class="pillar-cta" href="<?php echo esc_url( home_url( '/join/' ) ); ?>">Become a member →</a>
					</div>
				</article>
				<article class="pillar" id="ong-kc">
					<?php
					echo ong_home_v3b_picture(
						'classroom',
						array(
							'class'  => 'pillar-photo',
							'alt'    => 'Hands-on learning with technical drawings and tools',
							'width'  => '1200',
							'height' => '800',
						)
					);
					?>
					<div class="pillar-body">
						<p class="pillar-num">02 — Learning</p>
						<h3>Gather &amp; Learn</h3>
						<p>Knowledge Center playbooks plus chapter energy and symposium-style sessions — Offsite Connect for people who ship modules, not decks.</p>
						<p class="pillar-meta">Knowledge Center · Chapter meetups · Offsite Connect</p>
						<a class="pillar-cta" href="<?php echo esc_url( home_url( '/media/' ) ); ?>">Explore learning →</a>
					</div>
				</article>
				<article class="pillar" id="ong-institute">
					<?php
					echo ong_home_v3b_picture(
						'steel',
						array(
							'class'  => 'pillar-photo',
							'alt'    => 'Welder fabricating steel — modular plant career path',
							'width'  => '1200',
							'height' => '800',
						)
					);
					?>
					<div class="pillar-body">
						<p class="pillar-num">03 — Career</p>
						<h3>Advance Your Career</h3>
						<p>Offsite Pro Institute certifications and a careers board built for plant, VDC, and install talent — hire and get hired with discretion.</p>
						<p class="pillar-meta">Institute certs · Careers job board · Talent profiles</p>
						<a class="pillar-cta" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Grow your career →</a>
					</div>
				</article>
			</div>
		</div>
	</section>

	<!-- Stats illustrative hidden 2026-09-18 audit -->

	<!-- Spotlight -->
	<section class="sec navy-band" id="ong-featured">
		<div class="wrap">
			<p class="eyebrow">Featured</p>
			<h2>Spotlight — learn, certify, gather</h2>
			<p class="lead">Flagship pathways for modular pros — certifications, knowledge, and chapter connection.</p>
			<div class="spotlight">
				<article class="spot">
					<div class="spot-visual">
						<?php
						echo ong_home_v3b_picture(
							'inst-factory',
							array(
								'alt'    => 'Modular factory floor with robotic framing — Offsite Pro Institute',
								'width'  => '806',
								'height' => '435',
							)
						);
						?>
						<span class="spot-badge">Institute</span>
					</div>
					<div class="spot-body">
						<h3>Offsite Pro Institute</h3>
						<p>Certification tracks for plant production, VDC/DfMA, and install leadership — credentials that travel with MMC talent.</p>
						<a href="<?php echo esc_url( home_url( '/institute/' ) ); ?>">View cert programs →</a>
					</div>
				</article>
				<article class="spot">
					<div class="spot-visual">
						<?php
						echo ong_home_v3b_picture(
							'classroom',
							array(
								'alt'    => 'Technical learning session — Knowledge Center',
								'width'  => '1200',
								'height' => '800',
							)
						);
						?>
						<span class="spot-badge">Knowledge</span>
					</div>
					<div class="spot-body">
						<h3>Knowledge Center</h3>
						<p>Guides, playbooks, and industry intel for prefab yards and factory floors — practical, not theoretical.</p>
						<a href="<?php echo esc_url( home_url( '/media/' ) ); ?>">Browse resources →</a>
					</div>
				</article>
				<article class="spot">
					<div class="spot-visual">
						<?php
						echo ong_home_v3b_picture(
							'meeting',
							array(
								'alt'    => 'Chapter meetup — members connecting',
								'width'  => '1200',
								'height' => '800',
							)
						);
						?>
						<span class="spot-badge">Chapters</span>
					</div>
					<div class="spot-body">
						<h3>Chapter hubs</h3>
						<p>Chicago, Atlanta, and Idaho chapters connect factory, field, and owner peers locally — join a table, then expand as plants and projects move.</p>
						<a href="<?php echo esc_url( home_url( '/members/' ) ); ?>">See chapter hubs →</a>
					</div>
				</article>
			</div>
		</div>
	</section>

	<!-- Careers -->
	<section class="sec" id="ong-careers">
		<div class="wrap">
			<span class="teaser-label">Careers</span>
			<h2>Jobs &amp; talent in modular construction</h2>
			<p class="lead">Recent open roles and featured seekers — employer names stay private on public cards.</p>
			<div class="careers-layout">
				<div>
					<div class="careers-grid">
						<div class="teaser-block" id="ong-home-recent-roles">
							<h3>Recent open roles</h3>
							<?php
							$ong_home_jobs = ong_home_v3b_recent_jobs( 3 );
							if ( $ong_home_jobs ) :
								foreach ( $ong_home_jobs as $ong_job ) :
									?>
									<a class="mini-job" href="<?php echo esc_url( $ong_job['url'] ); ?>" style="display:block;text-decoration:none;color:inherit">
										<strong><?php echo esc_html( $ong_job['title'] ); ?></strong>
										<?php if ( ! empty( $ong_job['meta'] ) ) : ?>
											<span><?php echo esc_html( $ong_job['meta'] ); ?></span>
										<?php endif; ?>
										<span class="employer">Employer on ONG Directory</span>
									</a>
									<?php
								endforeach;
							else :
								?>
								<p class="mini-job-empty" style="margin:0.5rem 0 0;opacity:.85">Live roles post here when published — <a href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">browse careers</a>.</p>
								<?php
							endif;
							?>
						</div>
<div class="teaser-block">
							<h3>Featured job seekers</h3>
							<div class="mini-seeker">
								<strong>A.M. — Plant Production Supervisor</strong>
								<span>Southeast · 8 yrs · Lean, Welding QA</span>
							</div>
							<div class="mini-seeker">
								<strong>R.T. — BIM / VDC Specialist</strong>
								<span>Remote (US) · 5 yrs · Revit, DfMA</span>
							</div>
							<div class="mini-seeker">
								<strong>S.L. — Prefab Install Superintendent</strong>
								<span>Texas / Southwest · 12 yrs</span>
							</div>
						</div>
					</div>
					<div class="teaser-ctas">
						<a class="btn" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Browse jobs</a>
						<a class="btn btn-navy" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Post a job</a>
						<a class="btn btn-outline" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">View talent</a>
					</div>
				</div>
				<aside class="careers-side" aria-label="Careers photo">
					<?php
					echo ong_home_v3b_picture(
						'jobs-people',
						array(
							'alt'    => 'Modular construction crew — plant, VDC, and install talent',
							'width'  => '806',
							'height' => '435',
						)
					);
					?>
					<div class="careers-side-cap">Plant · VDC · Install talent</div>
				</aside>
			</div>
		</div>
	</section>

	<!-- Tools & financing -->
	<section class="sec alt" id="ong-tools">
		<div class="wrap">
			<h2>Tools &amp; financing</h2>
			<p class="lead">Estimate modular project costs, explore financing, and watch for iMod — built for fabricators and offsite teams.</p>
			<div class="tools-strip">
				<div class="tool-card hl">
					<?php
					echo ong_home_v3b_picture(
						'tools',
						array(
							'class'  => 'tool-thumb',
							'alt'    => 'Modular cost calculator — physical and digital twin',
							'width'  => '806',
							'height' => '409',
						)
					);
					?>
					<div class="tool-body">
						<h3>Modular Calculator</h3>
						<p>Real-time estimate chips for plant / module scenarios. ESTIMATE ONLY — not a bid.</p>
						<div class="est-row">
							<span class="est">Shell ~$142/sf</span>
							<span class="est">Fit-out ~$68/sf</span>
							<span class="est">Install ~$24/sf</span>
						</div>
						<a class="btn btn-sm" href="<?php echo esc_url( home_url( '/tools/tools-calculator/' ) ); ?>" style="background:var(--gold);color:var(--navy)">Open calculator</a>
					</div>
				</div>
				<div class="tool-card">
					<?php
					echo ong_home_v3b_picture(
						'modularhome',
						array(
							'class'  => 'tool-thumb',
							'alt'    => 'Finished modular home — equipment and growth financing',
							'width'  => '1200',
							'height' => '800',
						)
					);
					?>
					<div class="tool-body">
						<h3>Apply for Financing</h3>
						<p>Equipment, working capital, and growth options for modular manufacturers and fabricators.</p>
						<a class="btn btn-sm btn-navy" href="https://go.mypartner.io/business-financing/?ref=001Qk00000Wr35NIAR" target="_blank" rel="noopener noreferrer">Apply for Financing</a>
					</div>
				</div>
				<div class="tool-card">
					<?php
					echo ong_home_v3b_picture(
						'modules',
						array(
							'class'  => 'tool-thumb',
							'alt'    => 'Jobsite modules and crew — iMod coming soon',
							'width'  => '1200',
							'height' => '800',
						)
					);
					?>
					<div class="tool-body">
						<span class="chip-soon">Coming Soon</span>
						<h3>iMod</h3>
						<p>Next-gen modular planning tool. Placeholder chip for homepage — full hub on Tools.</p>
						<a class="btn btn-sm btn-outline" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Learn more</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Belonging -->
	<section class="sec" id="ong-why">
		<div class="wrap belong">
			<div class="belong-copy">
				<p class="eyebrow">Community</p>
				<h2>Built to belong — for people who build offsite</h2>
				<p>ONG is the professional home for modular and MMC: membership that roots you in chapters, learning that stays practical, and careers that respect discretion.</p>
				<ul class="belong-list">
					<li>Network with factories, fabricators, OEMs, and install crews</li>
					<li>Develop skills through Institute certs and Knowledge Center playbooks</li>
					<li>Share knowledge at chapter gatherings and Offsite Connect sessions</li>
				</ul>
				<div class="belong-photo">
					<?php
					echo ong_home_v3b_picture(
						'moduleship',
						array(
							'alt'    => 'Digital module planning on the shop floor — built to belong',
							'width'  => '1200',
							'height' => '800',
						)
					);
					?>
				</div>
			</div>
			<div class="belong-card">
				<h3>Ready to plug in?</h3>
				<p>Join for directory access, chapter invites, and early looks at cert tracks and career tools.</p>
				<a class="btn btn-join" href="<?php echo esc_url( home_url( '/join/' ) ); ?>">Join ONG</a>
				<a class="btn btn-ghost" href="#ong-pillars" style="border-color:rgba(255,255,255,.7)">See pathways</a>
			</div>
		</div>
	</section>

	<!-- Final CTA -->
	<section class="cta" id="ong-join">
		<div class="wrap cta-inner">
			<div>
				<h2>Join the network building modular’s future</h2>
				<p>Connect. Learn. Build. Membership unlocks directory, chapters, learning, and career pathways for offsite pros.</p>
			</div>
			<div class="cta-actions">
				<a class="btn btn-join" href="<?php echo esc_url( home_url( '/join/' ) ); ?>">Join ONG</a>
				<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/members/' ) ); ?>">Explore membership</a>
			</div>
		</div>
	</section>

	<!-- Footer brand line (logo mark only — site header already has logo) -->
	<footer class="footer-note">
		<div class="wrap">
			<div class="footer-brand">
				<?php
				$logo_png  = ong_home_v3b_asset( 'ong-logo-header.png' );
				$logo_webp = ong_home_v3b_asset( 'ong-logo-header.webp' );
				$logo_180  = ong_home_v3b_asset( 'ong-logo-header-180.webp' );
				$logo_280  = ong_home_v3b_asset( 'ong-logo-header-280.webp' );
				$logo_364  = ong_home_v3b_asset( 'ong-logo-header-364.webp' );
				$logo_ss   = esc_attr( $logo_180 . ' 180w, ' . $logo_280 . ' 280w, ' . $logo_364 . ' 364w' );
				?>
				<picture>
					<source type="image/webp" srcset="<?php echo $logo_ss; ?>" sizes="(max-width: 720px) 180px, 280px" />
					<img class="ong-logo-footer" src="<?php echo esc_url( $logo_png ); ?>" width="364" height="128" alt="Offsite Network Global" decoding="async" loading="lazy" />
				</picture>
			</div>
			<p><strong>Offsite Network Global</strong> · managed by Offsite Pro, LLC · Miami, Florida</p>
			<p>Contact: <a href="mailto:connect@offsitenetworkglobal.com">connect@offsitenetworkglobal.com</a></p>
		</div>
	</footer>

</div>
	<?php
	return ob_get_clean();
}

/**
 * Recent published ong_job posts for homepage teaser (CPT; same source as /wp-json/ong/v1/jobs).
 *
 * @param int $limit Max cards.
 * @return array<int, array{title:string,url:string,meta:string}>
 */
function ong_home_v3b_recent_jobs( $limit = 3 ) {
	$limit = max( 1, min( 6, (int) $limit ) );
	if ( ! post_type_exists( 'ong_job' ) ) {
		return array();
	}
	$now  = current_time( 'mysql' );
	$args = array(
		'post_type'              => 'ong_job',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
		'meta_query'             => array(
			'relation' => 'OR',
			array(
				'key'     => '_ong_expires_at',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_ong_expires_at',
				'value'   => $now,
				'compare' => '>=',
				'type'    => 'DATETIME',
			),
		),
	);
	$q    = new WP_Query( $args );
	$jobs = array();
	foreach ( $q->posts as $p ) {
		$loc = trim( (string) get_post_meta( $p->ID, '_ong_location', true ) );
		$emp = trim( (string) get_post_meta( $p->ID, '_ong_employment_type', true ) );
		if ( $emp === '' ) {
			$emp = 'Full-time';
		}
		$parts = array_filter( array( $loc, $emp ) );
		$jobs[] = array(
			'title' => get_the_title( $p ),
			'url'   => get_permalink( $p ),
			'meta'  => implode( ' · ', $parts ),
		);
	}
	return $jobs;
}

add_shortcode( 'ong_home_v3b', 'ong_home_v3b_shortcode' );

/**
 * Ensure Elementor text/shortcode widgets process the shortcode.
 */
add_filter( 'widget_text', 'do_shortcode' );
