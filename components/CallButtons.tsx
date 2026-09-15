import Link from "next/link";
import { SITE } from "@/lib/site";

type Props = {
  secondaryHref?: string;
  secondaryLabel?: string;
  showSecondary?: boolean;
};

export default function CallButtons({
  secondaryHref = "/contact/",
  secondaryLabel = "Request Service",
  showSecondary = true,
}: Props) {
  return (
    <p className="fw-cta">
      <a className="fw-btn fw-btn-call" href={SITE.phoneTel}>
        Call Now: {SITE.phone}
      </a>
      {showSecondary ? (
        <Link className="fw-btn fw-btn-secondary" href={secondaryHref}>
          {secondaryLabel}
        </Link>
      ) : null}
    </p>
  );
}
