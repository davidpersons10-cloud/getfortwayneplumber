import type { Metadata } from "next";
import CallButtons from "@/components/CallButtons";
import { NEARBY_TOWNS, SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "Service Area | Fort Wayne Metro & Nearby Towns",
  description:
    "Plumbing connections across Fort Wayne, IN and nearby towns including New Haven, Huntertown, Leo-Cedarville, and more.",
  openGraph: {
    title: "Service Area | Fort Wayne Metro & Nearby Towns",
    description:
      "Plumbing connections across Fort Wayne, IN metro and nearby towns.",
    url: "/service-area/",
  },
  alternates: { canonical: "/service-area/" },
};

export default function ServiceAreaPage() {
  return (
    <article className="fw-page">
      <h1>Plumbing Service Area — Fort Wayne Metro &amp; Nearby Towns</h1>
      <p>
        <strong>{SITE.altName}</strong> / <strong>{SITE.name}</strong> helps
        connect homeowners across the Fort Wayne, Indiana metro and nearby
        communities with independent, locally licensed plumbers.
      </p>
      <CallButtons />
      <p>
        <strong>Location type:</strong> {SITE.addressNote}
      </p>

      <div className="fw-section">
        <h2>Primary City</h2>
        <ul className="fw-cities">
          <li>
            <strong>Fort Wayne, Indiana</strong> — neighborhoods across north,
            south, east, and west Fort Wayne
          </li>
        </ul>
      </div>

      <div className="fw-section">
        <h2>Nearby Cities &amp; Towns</h2>
        <ul className="fw-cities">
          {NEARBY_TOWNS.map((t) => (
            <li key={t}>{t}</li>
          ))}
        </ul>
        <p>
          Not sure if we cover your address? Call{" "}
          <a href={SITE.phoneTel}>{SITE.phone}</a> with your ZIP code —
          coverage depends on available local contractors.
        </p>
      </div>

      <div className="fw-section">
        <h2>Neighborhoods (Fort Wayne)</h2>
        <p>
          We commonly receive requests from areas such as Northside, Southtown,
          West Central, Lakeside, Pine Valley, St. Joe Township, Aboite,
          Waynedale, and downtown Fort Wayne. Exact availability varies by day
          and contractor assignment.
        </p>
      </div>
    </article>
  );
}
