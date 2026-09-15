import Link from "next/link";
import CallButtons from "@/components/CallButtons";
import type { SERVICES } from "@/lib/site";

type Service = (typeof SERVICES)[number];

export default function ServicePage({ service }: { service: Service }) {
  return (
    <article className="fw-page">
      <p className="fw-breadcrumb">
        <Link href="/">Home</Link> / <Link href="/services/">Services</Link> /{" "}
        {service.name}
      </p>
      <h1>{service.h1}</h1>
      <p>{service.intro}</p>
      <CallButtons />
      <p className="fw-muted-note">
        We connect you with independent, locally licensed plumbers. We do not
        perform the plumbing work ourselves.
      </p>
      <h2>{service.bulletsTitle}</h2>
      <ul>
        {service.bullets.map((b) => (
          <li key={b}>{b}</li>
        ))}
      </ul>
      {service.slug === "leak-repair" ? (
        <p>
          Emergency? See{" "}
          <Link href="/services/emergency-plumbing/">Emergency Plumbing</Link>.
        </p>
      ) : null}
      {service.slug === "emergency-plumbing" ? (
        <>
          <h2>What to do right away</h2>
          <ol>
            <li>
              <strong>Shut off</strong> the main water valve if you can locate it
              safely.
            </li>
            <li>
              <strong>Turn off</strong> electricity to affected areas if water is
              near outlets or panels — only if safe.
            </li>
            <li>
              <strong>Call</strong> so we can help connect you with a local
              plumber.
            </li>
          </ol>
          <p>
            Emergency response times depend on the independent plumber&apos;s
            availability. For life-threatening emergencies, call 911.
          </p>
        </>
      ) : null}
      <p>
        <Link href="/services/">← All plumbing services</Link>
      </p>
    </article>
  );
}
