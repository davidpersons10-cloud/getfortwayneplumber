<?php
/**
 * Plugin Name: ONG Knowledge Center Hub
 * Description: CoreNet Source–inspired Knowledge Center (mock v1). Images localized to wp-content/uploads/ong-hub-media.
 * Version: 1.0.5
 * Author: Offsite Network Global
 */
defined( 'ABSPATH' ) || exit;

add_shortcode( 'ong_kc_hub', 'ong_kc_hub_shortcode' );

/**
 * @return string
 */
function ong_kc_hub_shortcode() {
	$join = home_url( '/join/' );
	ob_start();
	?>
<style>
  .ong-kc{--navy:#0F2442;--ink:#0a1628;--gold:#C9A227;--cream:#F4F1EA;--muted:#556677;--line:#e2e6ea;--podia:#0776ba;margin:0;font-family:Arial,Helvetica,sans-serif;background:#fff;color:var(--ink);line-height:1.5}
  .ong-kc *,.ong-kc *::before,.ong-kc *::after{box-sizing:border-box}
  .ong-kc img{max-width:100%;display:block}
  .ong-kc a{color:inherit}
  .ong-kc .wrap{max-width:1100px;margin:0 auto;padding:0 1.5rem}
  .ong-kc .hero{position:relative;min-height:420px;color:#fff;display:flex;align-items:center;background:#0a1628 center/cover no-repeat;background-image:linear-gradient(105deg,rgba(10,22,40,.92) 0%,rgba(15,36,66,.75) 55%,rgba(10,22,40,.55) 100%),url("https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1581091226825-a6a2a5aee158.jpg")}
  .ong-kc .hero .kicker{letter-spacing:.08em;text-transform:uppercase;font-size:.72rem;font-weight:700;color:#9fd2f0;margin:0 0 .5rem}
  .ong-kc .hero h1{margin:0 0 .75rem;font-size:clamp(1.75rem,3.4vw,2.55rem);line-height:1.15;max-width:22ch}
  .ong-kc .hero p{margin:0 0 1.25rem;color:#d6dde6;max-width:46rem;font-size:1.05rem}
  .ong-kc .btn{display:inline-block;background:var(--gold);color:var(--navy);font-weight:700;text-decoration:none;padding:.7rem 1.15rem;border-radius:4px}
  .ong-kc .btn-ghost{background:transparent;color:#fff;border:2px solid #fff;margin-left:.5rem}
  .ong-kc .btn-sm{padding:.55rem .9rem;font-size:.9rem}
  .ong-kc .sec{padding:2.5rem 0;border-bottom:1px solid var(--line)}
  .ong-kc .sec.alt{background:var(--cream)}
  .ong-kc .sec h2{margin:0 0 .75rem;font-size:1.55rem;color:var(--navy)}
  .ong-kc .sec .lead{margin:0 0 1.35rem;color:var(--muted);max-width:50rem}
  .ong-kc .intro{display:grid;grid-template-columns:1.15fr .85fr;gap:1.5rem;align-items:center}
  .ong-kc .intro .photo{border-radius:6px;overflow:hidden;min-height:260px;background:#ddd center/cover no-repeat;box-shadow:0 8px 24px rgba(15,36,66,.12);background-image:url("https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1503387762-592deb58ef4e.jpg")}
  .ong-kc .pillars{display:grid;grid-template-columns:repeat(2,1fr);gap:1.15rem;align-items:stretch}
  .ong-kc .pillar{background:#fff;border:1px solid var(--line);border-radius:6px;overflow:hidden;display:flex;flex-direction:column;height:100%}
  .ong-kc .pillar .ph{flex-shrink:0;height:0;padding-bottom:56.25%;position:relative;overflow:hidden;background:#bbb center/cover no-repeat}
  .ong-kc .pillar .ph-yt{background:#000}
  .ong-kc .pillar .ph-yt iframe{position:absolute;inset:0;width:100%;height:100%;border:0;display:block}
  .ong-kc .pillar .body{padding:1.05rem 1.1rem 1.2rem;border-top:4px solid var(--gold);flex:1 1 auto;display:flex;flex-direction:column;min-height:0}
  .ong-kc .pillar .tag{display:inline-block;font-size:.68rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--podia);margin-bottom:.35rem}
  .ong-kc .pillar h3{margin:0 0 .45rem;font-size:1.12rem;color:var(--navy)}
  .ong-kc .pillar p{margin:0 0 .65rem;color:var(--muted);font-size:.92rem}
  .ong-kc .pillar .blurb{flex:1 1 auto}
  .ong-kc .pillar ul{margin:0 0 1rem;padding-left:1.1rem;color:#445;font-size:.88rem;flex:1 1 auto}
  .ong-kc .pillar li{margin:.2rem 0}
  .ong-kc .pillar .cta-row{margin-top:auto;padding-top:.35rem}
  .ong-kc .news{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}
  .ong-kc .ncard{background:#fff;border:1px solid var(--line);border-radius:6px;overflow:hidden}
  .ong-kc .ncard .ph{height:140px;background:#ccc center/cover no-repeat}
  .ong-kc .ncard .body{padding:.9rem 1rem 1.05rem}
  .ong-kc .ncard .meta{font-size:.72rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--gold);margin:0 0 .35rem}
  .ong-kc .ncard h3{margin:0 0 .4rem;font-size:1rem;color:var(--navy);line-height:1.3}
  .ong-kc .ncard p{margin:0;color:var(--muted);font-size:.88rem}
  .ong-kc .submit{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;align-items:center}
  .ong-kc .submit .photo{min-height:220px;border-radius:6px;background:#ccc center/cover no-repeat;background-image:url("https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1581092795360-fd1ca04f0952.jpg")}
  .ong-kc .chips{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.85rem}
  .ong-kc .chip{background:#eaf4fb;color:var(--podia);font-weight:700;padding:.35rem .7rem;border-radius:999px;font-size:.82rem}
  .ong-kc .cta{color:#fff;padding:2.5rem 0;background:#0a1628 center/cover no-repeat;background-image:linear-gradient(90deg,rgba(10,22,40,.92),rgba(15,36,66,.78)),url("https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1504307651254-35680f356dfd.jpg")}
  .ong-kc .cta-inner{display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between}
  .ong-kc .cta h3{margin:0 0 .35rem;font-size:1.35rem;color:#fff}
  .ong-kc .cta p{margin:0;color:#c9d3de;font-size:.95rem;max-width:36rem}
  @media(max-width:900px){.ong-kc .intro,.ong-kc .submit,.ong-kc .pillars,.ong-kc .news{grid-template-columns:1fr 1fr}}
  @media(max-width:620px){.ong-kc .intro,.ong-kc .submit,.ong-kc .pillars,.ong-kc .news{grid-template-columns:1fr}.ong-kc .btn-ghost{margin-left:0;margin-top:.5rem;display:inline-block}}
</style>
<div class="ong-kc">

<header class="hero">
  <div class="wrap">
    <p class="kicker">Knowledge Center</p>
    <h1>Stay informed on offsite, prefab &amp; MMC</h1>
    <p>Adapting Summit Connect and CoreNet’s resource engine into an Offsite Network Global framework — transforming corporate real estate knowledge into a high-tech manufacturing, prefabrication, and Modern Methods of Construction intelligence hub.</p>
    <a class="btn" href="#pillars">Explore resources</a>
    <a class="btn btn-ghost" href="#pulse">Read The Offsite Pulse</a>
  </div>
</header>

<section class="sec">
  <div class="wrap intro">
    <div>
      <h2>Industry intelligence, curated for builders of the built environment</h2>
      <p class="lead" style="margin-bottom:.9rem">Factory directors, modular fabricators, structural engineers, owners, and policy partners don’t have time to sift thousands of pages. ONG brings symposium archives, research, field case studies, and weekly market dispatch to one place.</p>
      <p class="lead" style="margin:0">Track DfMA breakthroughs, embodied-carbon standards, cross-border compliance, and regional industrial capacity — so you stay ahead of the curve in volumetric, panelized, and automated construction.</p>
    </div>
    <div class="photo" role="img" aria-label="Modular factory floor"></div>
  </div>
</section>

<section class="sec alt" id="pillars">
  <div class="wrap">
    <h2>Resources</h2>
    <p class="lead">Four engines that replace general CRE content with offsite manufacturing and MMC depth — videos, research, peer field data, and weekly intelligence.</p>
    <div class="pillars">

      <article class="pillar">
        <div class="ph ph-yt">
          <iframe src="https://www.youtube.com/embed/l4tNSbm-qMs" title="Offsite Dirt Network" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
        <div class="body">
          <span class="tag">On-demand library</span>
          <h3>Offsite Dirt Network</h3>
          <p class="blurb">Offsite Dirt Network is the video and intel site for modular and offsite construction — factory floors, smart systems, and practitioner interviews.</p>
          <ul>
            <li>Factory-floor walkthroughs and process footage</li>
            <li>Practitioner interviews and operator Q&amp;A</li>
            <li>Smart systems, DfMA, and MMC field intel</li>
            <li>Weekly / on-demand sessions at offsitedirtnetwork.com</li>
          </ul>
          <div class="cta-row"><a class="btn btn-sm" href="/go/?u=https%3A%2F%2Fwww.offsitedirtnetwork.com%2F" target="_blank" rel="noopener">Browse sessions</a></div>
        </div>
      </article>

      <article class="pillar">
        <div class="ph" style="background-image:url('https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1454165804606-c3d57bc86b40.jpg')"></div>
        <div class="body">
          <span class="tag">Research agenda</span>
          <h3>2. Global Offsite Research Council</h3>
          <p><strong>Who shapes the agenda.</strong> A cross-disciplinary cohort of offsite factory directors, structural engineers, LCA specialists, and modular code strategists.</p>
          <ul>
            <li>Standardizing embodied carbon for volumetric modules</li>
            <li>Cross-border seismic &amp; fire-rating compliance</li>
            <li>Circular-material supply chains</li>
          </ul>
          <div class="cta-row"><a class="btn btn-sm" href="/contact/">Contact the council team</a></div>
        </div>
      </article>

      <article class="pillar">
        <div class="ph" style="background-image:url('https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1503387762-592deb58ef4e.jpg')"></div>
        <div class="body">
          <span class="tag">Peer submissions</span>
          <h3>3. Case Study &amp; Innovation Portal</h3>
          <p><strong>Field data, published by practitioners.</strong> Modular fabricators, offsite structural engineers, and logistics leads share what actually works on site and in the plant.</p>
          <ul>
            <li>Hook-to-anchor cycle times</li>
            <li>Over-dimensional transport permit workarounds</li>
            <li>Zero-defect QA/QC loops</li>
          </ul>
          <div class="cta-row"><a class="btn btn-sm" href="#submit">Submit a case study</a></div>
        </div>
      </article>

      <article class="pillar">
        <div class="ph" style="background-image:url('https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1460925895917-afdab827c52f.jpg')"></div>
        <div class="body">
          <span class="tag">Weekly intelligence</span>
          <h3>4. The Offsite Pulse &amp; Global Dispatch</h3>
          <p><strong>Market intel, not generic CRE news.</strong> Regional industrial capacity and supply-chain heatmaps across North America, EMEA, and APAC.</p>
          <ul>
            <li>CLT/GLT timber pricing volatility</li>
            <li>Heavy-haul crane availability</li>
            <li>MMC government mandate trackers</li>
          </ul>
          <div class="cta-row"><a class="btn btn-sm" href="#pulse">Read this week</a></div>
        </div>
      </article>

    </div>
  </div>
</section>

<section class="sec" id="pulse">
  <div class="wrap">
    <h2>Latest from The Offsite Pulse</h2>
    <p class="lead">Recent capacity, materials, and policy signals for modular and MMC teams — curated for factory and field leaders.</p>
    <div class="news">
      <article class="ncard">
        <div class="ph" style="background-image:url('https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1541888946425-d81bb19240f5.jpg')"></div>
        <div class="body">
          <p class="meta">North America · Capacity</p>
          <h3>Volumetric plant utilization climbs in the Southeast corridor</h3>
          <p>Factory throughput and crane booking windows for Q4 — what owners should watch.</p>
        </div>
      </article>
      <article class="ncard">
        <div class="ph" style="background-image:url('https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1513828583688-c526614cde94.jpg')"></div>
        <div class="body">
          <p class="meta">EMEA · Materials</p>
          <h3>CLT/GLT spot pricing: volatility map for European mills</h3>
          <p>Weekly heatmap of glued timber inputs tied to modular housing pipelines.</p>
        </div>
      </article>
      <article class="ncard">
        <div class="ph" style="background-image:url('https://offsitenetworkglobal.com/wp-content/uploads/ong-hub-media/photo-1486406146926-c627a92ad1ab.jpg')"></div>
        <div class="body">
          <p class="meta">APAC · Policy</p>
          <h3>MMC mandate tracker: new procurement rules in three markets</h3>
          <p>Government offsite targets and what they mean for fabricators and EPCs.</p>
        </div>
      </article>
    </div>
  </div>
</section>

<section class="sec alt" id="submit">
  <div class="wrap submit">
    <div>
      <h2>Share field knowledge</h2>
      <p class="lead" style="margin-bottom:.85rem">Submit articles, whitepapers, session clips, or plant data to the Case Study &amp; Innovation Portal. Peer-contributed content keeps the Knowledge Center grounded in real factory and jobsite performance.</p>
      <p class="lead" style="margin:0">Ideal topics: DfMA playbooks, QA/QC loops, logistics permits, embodied-carbon methods, and digital-twin handoffs.</p>
      <div class="chips">
        <span class="chip">Whitepapers</span>
        <span class="chip">Video / sessions</span>
        <span class="chip">Plant metrics</span>
        <span class="chip">Code &amp; compliance</span>
      </div>
      <p style="margin:1.1rem 0 0"><a class="btn" href="mailto:connect@offsitenetworkglobal.com">Propose a submission</a></p>
    </div>
    <div class="photo" role="img" aria-label="Engineers reviewing modular plans"></div>
  </div>
</section>

<section class="cta">
  <div class="wrap cta-inner">
    <div>
      <h3>Member access to the full intelligence hub</h3>
      <p>Symposium archive, Research Council briefings, portal submissions, and weekly Dispatch — part of the ONG member experience alongside Directory and chapters.</p>
    </div>
    <a class="btn" href="<?php echo $join; ?>">Join ONG</a>
  </div>
</section>
</div>
	<?php
	return (string) ob_get_clean();
}