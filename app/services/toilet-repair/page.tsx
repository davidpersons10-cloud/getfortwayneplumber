import type { Metadata } from "next";
import ServicePage from "@/components/ServicePage";
import { SERVICES } from "@/lib/site";

const service = SERVICES.find((s) => s.slug === "toilet-repair")!;

export const metadata: Metadata = {
  title: service.title,
  description: service.description,
  openGraph: {
    title: service.title,
    description: service.description,
    url: service.href,
  },
  alternates: { canonical: service.href },
};

export default function Page() {
  return <ServicePage service={service} />;
}
