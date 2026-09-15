import Link from "next/link";
import { SITE } from "@/lib/site";

export default function Footer() {
  return (
    <footer className="fw-footer-bar">
      <div className="fw-footer-inner">
        <p className="fw-footer-disclosure">
          <strong>{SITE.altName}</strong> · {SITE.name}. Marketing /
          lead-connection service — we do not perform plumbing work ourselves.{" "}
          <Link href="/privacy/">Privacy</Link> ·{" "}
          <Link href="/terms/">Terms</Link>
        </p>
        <p className="fw-footer-call">
          <a className="fw-btn fw-btn-call" href={SITE.phoneTel}>
            Call {SITE.phone}
          </a>
        </p>
      </div>
    </footer>
  );
}
