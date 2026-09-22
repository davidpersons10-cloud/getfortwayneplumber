<?php
/**
 * Shortcode [modular_cost_calculator].
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Shortcode {
	public static function register() {
		add_shortcode( 'modular_cost_calculator', array( __CLASS__, 'mcc_render' ) );
	}

	public static function mcc_render( $atts = array() ) {
		MCC_Assets::mcc_enqueue();
		return self::mcc_markup();
	}

	/**
	 * Progressive-enhancement host: inputs + results exist in public HTML.
	 * React createRoot replaces children when mcc-app.js loads.
	 */
	public static function mcc_markup() {
		ob_start();
		?>
<div class="mcc-app-host" data-mcc-root>
  <div class="mcc-pe" data-mcc-pe="1">
    <div class="mcc-layout" style="display:grid;grid-template-columns:minmax(280px,1fr) minmax(280px,1fr);gap:1.25rem;align-items:start">
      <form class="mcc-panel mcc-sticky" method="get" action="#" onsubmit="return false;">
        <div class="mcc-panel-title">Project inputs</div>
        <div class="mcc-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <label class="mcc-field is-wide" style="grid-column:1/-1">Project name
            <input id="mcc-pe-projectName" name="projectName" type="text" autocomplete="off" />
          </label>
          <label class="mcc-field">City
            <input id="mcc-pe-city" name="city" type="text" />
          </label>
          <label class="mcc-field">State
            <select id="mcc-pe-state" name="state" required>
              <option value="ID">Idaho (ID)</option>
              <option value="CO">Colorado (CO)</option>
              <option value="GA">Georgia (GA)</option>
              <option value="IL">Illinois (IL)</option>
              <option value="TX">Texas (TX)</option>
              <option value="CA">California (CA)</option>
              <option value="NY">New York (NY)</option>
              <option value="FL">Florida (FL)</option>
            </select>
          </label>
          <label class="mcc-field">Area (sf)
            <input id="mcc-pe-area" name="area" type="number" min="1" step="1" value="20000" />
          </label>
          <label class="mcc-field">Stories
            <input id="mcc-pe-stories" name="stories" type="number" min="1" step="1" value="2" />
          </label>
          <label class="mcc-field">Haul (miles)
            <input id="mcc-pe-haul" name="haulMiles" type="number" min="0" step="1" value="150" />
          </label>
          <label class="mcc-field">Modules
            <input id="mcc-pe-modules" name="modules" type="number" min="1" step="1" value="24" />
          </label>
        </div>
        <p style="margin:.85rem 0 0;display:flex;gap:.5rem;flex-wrap:wrap">
          <button type="submit" class="mcc-btn" style="background:#c9a227;color:#0f2442;font-weight:700;border:0;padding:.65rem 1rem;border-radius:4px;cursor:pointer">Calculate estimate</button>
          <button type="reset" class="mcc-btn-secondary" style="background:transparent;color:#0f2442;border:1px solid #cbd5e1;padding:.65rem 1rem;border-radius:4px;cursor:pointer">Reset</button>
        </p>
        <p class="mcc-hint" style="margin:.65rem 0 0;color:#64748b;font-size:.88rem">ESTIMATE ONLY — modular stack planning figures, not a bid or turnkey total. Full calculator loads when scripts are available.</p>
      </form>
      <aside class="mcc-panel mcc-results" aria-live="polite">
        <div class="mcc-panel-title">Results</div>
        <ul class="mcc-result-chips" style="list-style:none;margin:0;padding:0;display:grid;gap:.5rem">
          <li style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:.75rem 1rem"><strong>Shell</strong> — enter inputs, then Calculate</li>
          <li style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:.75rem 1rem"><strong>Fit-out</strong> — estimate chip</li>
          <li style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:.75rem 1rem"><strong>Install / set</strong> — estimate chip</li>
          <li style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:.75rem 1rem"><strong>Haul</strong> — estimate chip</li>
        </ul>
        <p style="margin:.85rem 0 0;color:#64748b;font-size:.88rem">Results update after Calculate when the interactive engine is loaded.</p>
      </aside>
    </div>
  </div>
</div>
		<?php
		return (string) ob_get_clean();
	}
}
