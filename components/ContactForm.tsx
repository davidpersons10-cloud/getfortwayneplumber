"use client";

import { FormEvent, useState } from "react";
import { SERVICES, SITE } from "@/lib/site";

export default function ContactForm() {
  const [status, setStatus] = useState<"idle" | "ok" | "error">("idle");
  const [errorMsg, setErrorMsg] = useState("");

  function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const form = e.currentTarget;
    const fd = new FormData(form);
    const name = String(fd.get("name") || "").trim();
    const phone = String(fd.get("phone") || "").trim();
    const email = String(fd.get("email") || "").trim();
    const service = String(fd.get("service") || "").trim();
    const message = String(fd.get("message") || "").trim();

    if (!name || !phone || !email || !service) {
      setStatus("error");
      setErrorMsg("Please fill in name, phone, email, and service needed.");
      return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setStatus("error");
      setErrorMsg("Please enter a valid email address.");
      return;
    }

    const subject = encodeURIComponent(
      `Fort Wayne plumbing lead: ${service} — ${name}`
    );
    const body = encodeURIComponent(
      [
        `Name: ${name}`,
        `Phone: ${phone}`,
        `Email: ${email}`,
        `Service needed: ${service}`,
        "",
        "Message:",
        message || "(none)",
        "",
        `Source: ${SITE.domain}`,
      ].join("\n")
    );

    window.location.href = `mailto:${SITE.email}?subject=${subject}&body=${body}`;
    setStatus("ok");
    setErrorMsg("");
    form.reset();
  }

  return (
    <div className="fw-contact-form-wrap">
      {status === "ok" ? (
        <div className="fw-notice fw-notice-success" role="status">
          Your email client should open with the request. If it does not, email{" "}
          <a href={`mailto:${SITE.email}`}>{SITE.email}</a> or call{" "}
          <a href={SITE.phoneTel}>{SITE.phone}</a>.
        </div>
      ) : null}
      {status === "error" ? (
        <div className="fw-notice fw-notice-error" role="alert">
          {errorMsg}
        </div>
      ) : null}
      <form className="fw-contact-form" onSubmit={onSubmit} noValidate>
        <p>
          <label htmlFor="fw-name">
            Name <span className="required">*</span>
          </label>
          <input id="fw-name" name="name" type="text" required autoComplete="name" />
        </p>
        <p>
          <label htmlFor="fw-phone">
            Phone <span className="required">*</span>
          </label>
          <input id="fw-phone" name="phone" type="tel" required autoComplete="tel" />
        </p>
        <p>
          <label htmlFor="fw-email">
            Email <span className="required">*</span>
          </label>
          <input id="fw-email" name="email" type="email" required autoComplete="email" />
        </p>
        <p>
          <label htmlFor="fw-service">
            Service needed <span className="required">*</span>
          </label>
          <select id="fw-service" name="service" required defaultValue="">
            <option value="" disabled>
              Select a service…
            </option>
            {SERVICES.map((s) => (
              <option key={s.slug} value={s.name}>
                {s.name}
              </option>
            ))}
            <option value="Other / Not sure">Other / Not sure</option>
          </select>
        </p>
        <p>
          <label htmlFor="fw-message">Message</label>
          <textarea id="fw-message" name="message" rows={5} />
        </p>
        <p className="fw-form-disclaimer">
          By submitting, you agree we may share your request with local licensed
          plumbers to fulfill your inquiry. We are a lead-connection service, not
          the plumbing contractor.
        </p>
        <p>
          <button type="submit" className="fw-btn fw-btn-call">
            Send Request
          </button>
        </p>
        <p className="fw-mailto-fallback">
          Prefer email?{" "}
          <a href={`mailto:${SITE.email}`}>mailto:{SITE.email}</a>
        </p>
      </form>
    </div>
  );
}
