import type { Metadata } from "next";
import { SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "Privacy Policy | Get Fort Wayne Plumber",
  description:
    "Privacy Policy for GetFortWaynePlumber.com — how we collect, use, and protect information.",
  openGraph: {
    title: "Privacy Policy | Get Fort Wayne Plumber",
    description: "Privacy Policy for GetFortWaynePlumber.com.",
    url: "/privacy/",
  },
  alternates: { canonical: "/privacy/" },
};

export default function PrivacyPage() {
  return (
    <article className="fw-page fw-legal">
      <h1>Privacy Policy</h1>
      <p>
        <em>
          Last updated: September 2026. This is a sample policy — have counsel
          review before publishing.
        </em>
      </p>
      <p>
        Get Fort Wayne Plumber (&quot;we,&quot; &quot;us&quot;) operates{" "}
        {SITE.domain}, a marketing and lead generation site for plumbing
        services in Fort Wayne, Indiana.
      </p>
      <h2>Information We Collect</h2>
      <ul>
        <li>
          Contact details you submit (name, phone, email, address, service
          description)
        </li>
        <li>
          Technical data such as IP address, browser type, and pages visited
          (via standard server or analytics logs)
        </li>
        <li>
          Call detail if you dial our published number (may be logged by our
          call/tracking provider)
        </li>
      </ul>
      <h2>How We Use Information</h2>
      <ul>
        <li>
          To respond to service requests and connect you with local plumbing
          contractors or partners
        </li>
        <li>To improve the website and measure marketing performance</li>
        <li>To comply with legal obligations</li>
      </ul>
      <h2>Sharing</h2>
      <p>
        Service request information may be shared with assigned local
        contractors, call centers, or CRM/lead platforms needed to fulfill your
        request. We do not sell personal information in a way unrelated to
        connecting you with plumbing services, except as required by law.
      </p>
      <h2>Cookies &amp; Tracking</h2>
      <p>
        We may use cookies or similar technologies for basic site function and
        marketing analytics. You can control cookies through your browser
        settings.
      </p>
      <h2>Data Retention</h2>
      <p>
        We retain lead and contact records as long as needed for business,
        dispute resolution, and legal compliance.
      </p>
      <h2>Your Choices</h2>
      <p>
        To request access, correction, or deletion of your information (subject
        to applicable law), contact us using the details on our Contact page or
        by calling the published phone number.
      </p>
      <h2>Children</h2>
      <p>
        This site is not directed at children under 13. We do not knowingly
        collect information from children.
      </p>
      <h2>Changes</h2>
      <p>
        We may update this policy; the &quot;Last updated&quot; date will change
        when we do.
      </p>
      <h2>Contact</h2>
      <p>
        Questions about privacy: use the Contact page or call{" "}
        <a href={SITE.phoneTel}>{SITE.phone}</a>.
      </p>
    </article>
  );
}
