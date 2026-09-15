import { DISCLOSURE_TEXT } from "@/lib/site";

export default function Disclosure() {
  return (
    <aside className="fw-disclosure" role="note">
      <p>
        <strong>Important:</strong> {DISCLOSURE_TEXT}
      </p>
    </aside>
  );
}
