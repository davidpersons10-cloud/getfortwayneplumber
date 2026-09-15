import type { Metadata } from "next";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import StickyMobileCTA from "@/components/StickyMobileCTA";
import JsonLd from "@/components/JsonLd";
import { SITE } from "@/lib/site";
import "./globals.css";

export const metadata: Metadata = {
  metadataBase: new URL(SITE.url),
  title: {
    default: "Fort Wayne Plumber | Emergency & Local Plumbing Pros",
    template: "%s",
  },
  description:
    "Need a plumber in Fort Wayne, IN? Get fast connections to local licensed plumbers for drains, water heaters, leaks, and emergencies.",
  openGraph: {
    type: "website",
    locale: "en_US",
    siteName: SITE.name,
    url: SITE.url,
    title: "Fort Wayne Plumber | Emergency & Local Plumbing Pros",
    description:
      "Need a plumber in Fort Wayne, IN? Get fast connections to local licensed plumbers.",
  },
  robots: { index: true, follow: true },
  alternates: { canonical: "/" },
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en">
      <body>
        <JsonLd />
        <a className="fw-skip" href="#main">
          Skip to content
        </a>
        <Header />
        <main id="main" className="fw-wrap">
          {children}
        </main>
        <Footer />
        <StickyMobileCTA />
      </body>
    </html>
  );
}
