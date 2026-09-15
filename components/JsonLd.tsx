import { SITE } from "@/lib/site";

export default function JsonLd() {
  const data = {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    name: SITE.name,
    alternateName: SITE.altName,
    url: SITE.url,
    telephone: SITE.phone,
    description:
      "Marketing and lead-connection service connecting Fort Wayne, IN homeowners with independent, locally licensed plumbers.",
    areaServed: [
      { "@type": "City", name: "Fort Wayne", containedInPlace: { "@type": "State", name: "Indiana" } },
      { "@type": "AdministrativeArea", name: "Allen County, Indiana" },
    ],
    address: {
      "@type": "PostalAddress",
      addressLocality: SITE.city,
      addressRegion: SITE.state,
      addressCountry: SITE.country,
    },
    // Service-area based — no street address
  };

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
    />
  );
}
