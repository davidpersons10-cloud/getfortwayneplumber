import type { Metadata } from "next";
import Link from "next/link";
import CallButtons from "@/components/CallButtons";
import { SERVICES, SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "Plumbing Services in Fort Wayne, IN | Fort Wayne Plumbing Pros",
  description:
    "Browse Fort Wayne plumbing services: drain cleaning, water heaters, sewer lines, leak repair, toilets, faucets, and 24/7 emergency plumbing.",
  openGraph: {
    title: "Plumbing Services in Fort Wayne, IN | Fort Wayne Plumbing Pros",
    description:
      "Browse Fort Wayne plumbing services: drains, water heaters, sewer, leaks, toilets, faucets, emergencies.",
    url: "/services/",
  },
  alternates: { canonical: "/services/" },
};

export default function ServicesHubPage() {
  return (
    <article className="fw-page">
      <h1>Plumbing Services in Fort Wayne, IN</h1>
      <p>
        From clogged drains to emergency repairs, <strong>{SITE.altName}</strong>{" "}
        / <strong>{SITE.name}</strong> helps you connect with local licensed
        plumbers for the jobs Fort Wayne homeowners need most.
      </p>
      <CallButtons />
      <ul className="fw-services-list">
        {SERVICES.map((s) => (
          <li key={s.slug}>
            <Link href={s.href}>{s.name}</Link>
          </li>
        ))}
      </ul>
      <p>
        Need help choosing? Call <a href={SITE.phoneTel}>{SITE.phone}</a> and
        describe your issue — we will connect you with a local Fort Wayne–area
        plumber.
      </p>
    </article>
  );
}
