import { SITE } from "@/lib/site";

export default function StickyMobileCTA() {
  return (
    <div className="fw-sticky-call" aria-label="Call now">
      <a className="fw-btn fw-btn-call" href={SITE.phoneTel}>
        Call {SITE.phone}
      </a>
    </div>
  );
}
