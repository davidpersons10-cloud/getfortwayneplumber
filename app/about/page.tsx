import type { Metadata } from "next";
import Link from "next/link";
import Disclosure from "@/components/Disclosure";
import { SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "About Get Fort Wayne Plumber | Local Lead Connection",
  description:
    "Get Fort Wayne Plumber connects homeowners with local licensed plumbers. We are a marketing service — not the plumbing company itself.",
  openGraph: {
    title: "About Get Fort Wayne Plumber | Local Lead Connection",
    description:
      "Marketing and lead-connection service for Fort Wayne plumbing — not a plumbing contractor.",
    url: "/about/",
  },
  alternates: { canonical: "/about/" },
};

export default function AboutPage() {
  return (
    <article className="fw-page">
      <h1>About Get Fort Wayne Plumber</h1>
      <p>
        <strong>{SITE.name}</strong> (marketed as{" "}
        <strong>{SITE.altName}</strong>) is a local lead-generation and
        marketing service for homeowners in Fort Wayne, Indiana and nearby
        communities.
      </p>
      <Disclosure />
      <div className="fw-section">
        <h2>What we do</h2>
        <p>
          We help people who need plumbing help find and connect with{" "}
          <strong>independent, locally licensed plumbers</strong>. When you call
          or submit a request, we work to match you with a plumber who serves
          your area.
        </p>
      </div>
      <div className="fw-section">
        <h2>What we are not</h2>
        <p>
          We are <strong>not</strong> a plumbing contractor. We do{" "}
          <strong>not</strong> perform plumbing repairs or installations
          ourselves. Any plumbing work is performed under the plumber&apos;s own
          license, insurance, and customer agreement.
        </p>
      </div>
      <div className="fw-section">
        <h2>How It Works</h2>
        <ol>
          <li>You call or submit a service request.</li>
          <li>
            Your request is matched with an available local licensed plumber when
            one is assigned.
          </li>
          <li>
            The assigned professional follows up to schedule and perform the work
            under their own terms.
          </li>
        </ol>
      </div>
      <div className="fw-section">
        <h2>Get in Touch</h2>
        <p>
          Phone: <a href={SITE.phoneTel}>{SITE.phone}</a>
        </p>
        <p>
          <Link className="fw-btn" href="/contact/">
            Contact form
          </Link>
        </p>
      </div>
    </article>
  );
}
