import type { Metadata } from "next";
import ContactForm from "@/components/ContactForm";
import Disclosure from "@/components/Disclosure";
import { SITE } from "@/lib/site";

export const metadata: Metadata = {
  title: "Contact | Get Fort Wayne Plumber",
  description:
    "Contact Get Fort Wayne Plumber to connect with local licensed plumbers in Fort Wayne, IN. Call TRACKING_NUMBER_PLACEHOLDER.",
  openGraph: {
    title: "Contact | Get Fort Wayne Plumber",
    description:
      "Contact Get Fort Wayne Plumber to connect with local licensed plumbers in Fort Wayne, IN.",
    url: "/contact/",
  },
  alternates: { canonical: "/contact/" },
};

export default function ContactPage() {
  return (
    <article className="fw-page">
      <h1>Contact Get Fort Wayne Plumber</h1>
      <p>
        Need a plumber in Fort Wayne? Reach us and we will help connect you with
        an independent, locally licensed plumber. For emergencies, phone is
        fastest.
      </p>
      <p className="fw-cta">
        <a className="fw-btn fw-btn-call" href={SITE.phoneTel}>
          Call {SITE.phone}
        </a>
      </p>
      <Disclosure />
      <h2>Service Request Form</h2>
      <ContactForm />
      <div className="fw-nap fw-section">
        <h2>NAP / Business Info</h2>
        <p>
          <strong>{SITE.name}</strong>
        </p>
        <p>
          <em>Also known as:</em> {SITE.altName}
        </p>
        <p>
          <strong>Phone:</strong>{" "}
          <a href={SITE.phoneTel}>{SITE.phone}</a>
        </p>
        <p>
          {SITE.city}, {SITE.state} (US)
        </p>
        <p>{SITE.addressNote}</p>
        <p>
          <em>
            Replace the phone tracking placeholder in{" "}
            <code>lib/site.ts</code> before publishing. Do not invent a street
            address.
          </em>
        </p>
      </div>
    </article>
  );
}
