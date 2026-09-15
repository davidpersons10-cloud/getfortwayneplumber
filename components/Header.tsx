"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { NAV, SITE } from "@/lib/site";

export default function Header() {
  const pathname = usePathname() || "/";

  return (
    <header className="fw-site-header">
      <div className="fw-header-inner">
        <Link className="fw-logo" href="/">
          {SITE.name}
          <span>{SITE.altName}</span>
        </Link>
        <nav className="fw-nav" aria-label="Primary">
          {NAV.map((item) => {
            const current =
              item.href === "/"
                ? pathname === "/"
                : pathname === item.href || pathname.startsWith(item.href);
            return (
              <Link
                key={item.href}
                href={item.href}
                aria-current={current ? "page" : undefined}
              >
                {item.label}
              </Link>
            );
          })}
        </nav>
        <p className="fw-header-call" style={{ margin: 0 }}>
          <a className="fw-btn fw-btn-call" href={SITE.phoneTel}>
            Call {SITE.phone}
          </a>
        </p>
      </div>
    </header>
  );
}
