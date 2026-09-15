import type { Metadata } from "next";
import Link from "next/link";
import CallButtons from "@/components/CallButtons";
import Disclosure from "@/components/Disclosure";
import { SERVICES, SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "Fort Wayne Plumber | Emergency & Local Plumbing Pros",
  description:
    "Need a plumber in Fort Wayne, IN? Get fast connections to local licensed plumbers for drains, water heaters, leaks, and emergencies. Call TRACKING_NUMBER_PLACEHOLDER.",
  openGraph: {
    title: "Fort Wayne Plumber | Emergency & Local Plumbing Pros",
    description:
      "Need a plumber in Fort Wayne, IN? Get fast connections to local licensed plumbers.",
    url: "/",
  },
  alternates: { canonical: "/" },
};

export default function HomePage() {
  return (
    <article className="fw-page">
      <div className="fw-hero">
        <h1>Fort Wayne Plumber — Fast Connections to Local Licensed Pros</h1>
        <p className="fw-lead">
          When a pipe bursts, a drain backs up, or the water heater fails, you
          need help you can trust. <strong>{SITE.name}</strong> (also known as{" "}
          <strong>{SITE.altName}</strong>) connects Fort Wayne, Indiana
          homeowners with independent, locally licensed plumbers — often the
          same day.
        </p>
        <CallButtons />
      </div>

      <Disclosure />

      <div className="fw-section">
        <h2>Why homeowners call us</h2>
        <ul>
          <li>
            <strong>Local focus</strong> — Fort Wayne metro and nearby towns
          </li>
          <li>
            <strong>Licensed plumbers</strong> — we connect you with independent
            pros who hold Indiana licenses
          </li>
          <li>
            <strong>Common &amp; urgent jobs</strong> — drains, water heaters,
            sewer lines, leaks, toilets, fixtures, emergencies
          </li>
          <li>
            <strong>Simple process</strong> — call or request service; we help
            match you with an available local plumber
          </li>
        </ul>
      </div>

      <div className="fw-section">
        <h2>Our Plumbing Services</h2>
        <ul className="fw-services-list">
          {SERVICES.map((s) => (
            <li key={s.slug}>
              <Link href={s.href}>{s.name}</Link>
            </li>
          ))}
        </ul>
      </div>

      <div className="fw-section">
        <h2>Serving Fort Wayne &amp; Surrounding Areas</h2>
        <p>
          We help homeowners across Fort Wayne, New Haven, Huntertown,
          Leo-Cedarville, Grabill, Woodburn, and nearby Allen County communities
          connect with local plumbing professionals.
        </p>
        <p>
          <Link href="/service-area/">View full service area →</Link>
        </p>
      </div>

      <div className="fw-section fw-nap" id="contact">
        <h2>Contact Us</h2>
        <p>
          <strong>Phone:</strong>{" "}
          <a href={SITE.phoneTel}>{SITE.phone}</a>
        </p>
        <p>
          <strong>Service Area:</strong> {SITE.addressNote}
        </p>
        <p>
          <Link className="fw-btn" href="/contact/">
            Send a Message
          </Link>
        </p>
      </div>
    </article>
  );
}
