import type { Metadata } from "next";
import Link from "next/link";
import { SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "Terms of Use | Get Fort Wayne Plumber",
  description:
    "Terms of Use for GetFortWaynePlumber.com — lead connection service disclosures and limitations.",
  openGraph: {
    title: "Terms of Use | Get Fort Wayne Plumber",
    description: "Terms of Use for GetFortWaynePlumber.com.",
    url: "/terms/",
  },
  alternates: { canonical: "/terms/" },
};

export default function TermsPage() {
  return (
    <article className="fw-page fw-legal">
      <h1>Terms of Service</h1>
      <p>
        <em>
          Last updated: September 2026. Sample terms — have counsel review
          before publishing.
        </em>
      </p>
      <p>
        By using {SITE.domain}, you agree to these Terms of Service.
      </p>
      <h2>Nature of the Service</h2>
      <p>
        This website is a <strong>marketing and lead generation</strong>{" "}
        platform. We help connect consumers seeking plumbing services in the
        Fort Wayne, IN area with local contractors or partners when available.
        Until a licensed local contractor is assigned, we do not represent that
        we are the licensed plumber who will perform the work.
      </p>
      <h2>No Guarantee of Availability</h2>
      <p>
        Contractor availability, pricing, licensing, and scheduling are
        determined by the assigned professional. We do not guarantee response
        time, same-day service, or that every request will be accepted.
      </p>
      <h2>Not a Substitute for Emergency Services</h2>
      <p>
        For life-threatening emergencies, call 911. For gas leaks or immediate
        hazards, follow your utility&apos;s emergency instructions.
      </p>
      <h2>Accuracy of Information</h2>
      <p>
        You agree to provide accurate contact and job details. Misrepresentations
        may result in declined service.
      </p>
      <h2>Limitation of Liability</h2>
      <p>
        To the fullest extent permitted by law, Get Fort Wayne Plumber and its
        operators are not liable for workmanship, damage, or disputes arising
        from services performed by third-party contractors. Claims related to
        plumbing work should be directed to the contractor who performed the
        work.
      </p>
      <h2>Intellectual Property</h2>
      <p>
        Site content, branding, and design are owned by the site operator or
        licensors. You may not copy or reuse them without permission.
      </p>
      <h2>Privacy</h2>
      <p>
        Your use of the site is also governed by our{" "}
        <Link href="/privacy/">Privacy Policy</Link>.
      </p>
      <h2>Changes</h2>
      <p>
        We may update these terms at any time. Continued use after changes
        constitutes acceptance.
      </p>
      <h2>Contact</h2>
      <p>
        Questions: see our <Link href="/contact/">Contact</Link> page or call{" "}
        <a href={SITE.phoneTel}>{SITE.phone}</a>.
      </p>
    </article>
  );
}
