/** Single source of truth for tracking phone — replace before go-live. */
export const TRACKING_NUMBER_PLACEHOLDER = "TRACKING_NUMBER_PLACEHOLDER";

export const SITE = {
  name: "Get Fort Wayne Plumber",
  altName: "Fort Wayne Plumbing Pros",
  domain: "getfortwayneplumber.com",
  url: "https://getfortwayneplumber.com",
  phone: TRACKING_NUMBER_PLACEHOLDER,
  phoneTel: `tel:${TRACKING_NUMBER_PLACEHOLDER}`,
  email: "leads@getfortwayneplumber.com",
  addressNote:
    "Serving Fort Wayne, IN metro and nearby towns (service-area based — no retail storefront)",
  city: "Fort Wayne",
  state: "IN",
  country: "US",
} as const;

export const DISCLOSURE_TEXT =
  "Get Fort Wayne Plumber / Fort Wayne Plumbing Pros is a marketing and lead-connection service. We are not a plumbing contractor and do not perform plumbing work ourselves. When you call or submit a request, we connect you with independent, locally licensed plumbers serving the Fort Wayne, Indiana area. Any work is performed under the plumber's own license, insurance, and terms.";

export const FOOTER_DISCLOSURE =
  "Fort Wayne Plumbing Pros · Get Fort Wayne Plumber. Marketing / lead-connection service — we do not perform plumbing work ourselves.";

export const NAV = [
  { href: "/", label: "Home" },
  { href: "/services/", label: "Services" },
  { href: "/service-area/", label: "Service Area" },
  { href: "/about/", label: "About" },
  { href: "/contact/", label: "Contact" },
] as const;

export const SERVICES = [
  {
    slug: "drain-cleaning",
    href: "/services/drain-cleaning/",
    name: "Drain Cleaning",
    title: "Drain Cleaning Fort Wayne, IN | Clogged Drain Plumbers",
    description:
      "Clogged drains in Fort Wayne? Connect with local licensed plumbers for professional drain cleaning and sewer snaking. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Drain Cleaning in Fort Wayne, IN",
    intro:
      "Slow sinks, backed-up tubs, or a gurgling main line? We connect you with local licensed plumbers who handle professional drain cleaning across Fort Wayne and nearby towns.",
    bulletsTitle: "Common drain problems we help with",
    bullets: [
      "Kitchen sink and garbage disposal clogs",
      "Bathroom sink, tub, and shower backups",
      "Toilet-related drain issues",
      "Main sewer line snaking and clearing",
      "Recurring clogs that DIY snakes will not fix",
    ],
  },
  {
    slug: "water-heater",
    href: "/services/water-heater/",
    name: "Water Heater Repair & Install",
    title: "Water Heater Repair & Install Fort Wayne, IN",
    description:
      "Water heater repair or replacement in Fort Wayne, IN. Get matched with local licensed plumbers for tank and tankless systems. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Water Heater Repair & Installation in Fort Wayne, IN",
    intro:
      "No hot water, lukewarm showers, or a leaking tank? We connect Fort Wayne homeowners with local licensed plumbers for water heater repair and replacement — traditional tank and tankless systems.",
    bulletsTitle: "Services local plumbers commonly provide",
    bullets: [
      "Diagnosis of no-hot-water and inconsistent temperature",
      "Repair of thermostats, elements, burners, and valves",
      "Tank water heater replacement",
      "Tankless water heater service and install (where appropriate)",
      "Safety checks for leaks, pressure relief valves, and venting",
    ],
  },
  {
    slug: "sewer-line",
    href: "/services/sewer-line/",
    name: "Sewer Line Services",
    title: "Sewer Line Repair Fort Wayne, IN | Local Plumbers",
    description:
      "Sewer line problems in Fort Wayne? Connect with local plumbers for inspection, repair, and replacement. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Sewer Line Repair in Fort Wayne, IN",
    intro:
      "Multiple drain backups, sewage odors, or soggy spots in the yard can point to a sewer line issue. We connect you with local licensed plumbers who inspect and repair sewer lines in the Fort Wayne area.",
    bulletsTitle: "Common sewer line needs",
    bullets: [
      "Camera inspection of the main line",
      "Root intrusion clearing",
      "Spot repairs and trenchless options (when suitable)",
      "Full or partial line replacement",
      "Cleanouts and access point work",
    ],
  },
  {
    slug: "leak-repair",
    href: "/services/leak-repair/",
    name: "Leak Repair",
    title: "Leak Repair Fort Wayne, IN | Pipe & Fixture Leaks",
    description:
      "Water leak repair in Fort Wayne, IN. Fast connections to licensed plumbers for pipe, slab, and fixture leaks. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Leak Repair in Fort Wayne, IN",
    intro:
      "Dripping pipes, wet floors, or unexplained water bills? We connect Fort Wayne homeowners with local licensed plumbers for leak detection and repair.",
    bulletsTitle: "Leak types we help connect you for",
    bullets: [
      "Visible pipe and fitting leaks",
      "Under-sink and supply-line drips",
      "Toilet base and tank leaks",
      "Suspected slab or in-wall leaks (diagnosis)",
      "Outdoor spigot and irrigation-adjacent plumbing leaks",
    ],
  },
  {
    slug: "toilet-repair",
    href: "/services/toilet-repair/",
    name: "Toilet Repair & Install",
    title: "Toilet Repair Fort Wayne, IN | Running & Clogged Toilets",
    description:
      "Toilet repair and replacement in Fort Wayne. Connect with local licensed plumbers for clogs, leaks, and installs. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Toilet Repair in Fort Wayne, IN",
    intro:
      "Running toilets, clogs, and base leaks disrupt daily life. We connect you with local licensed plumbers for toilet repair and replacement in Fort Wayne, IN.",
    bulletsTitle: "Common toilet issues",
    bullets: [
      "Continuously running or phantom flushing",
      "Weak flush or incomplete clear",
      "Clogs that plunging will not fix",
      "Wax ring / base leaks",
      "New toilet installation or replacement",
    ],
  },
  {
    slug: "faucet-fixture",
    href: "/services/faucet-fixture/",
    name: "Faucet & Fixture Repair",
    title: "Faucet & Fixture Repair Fort Wayne, IN",
    description:
      "Faucet and fixture repair or install in Fort Wayne, IN. Get connected with local licensed plumbers. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Faucet & Fixture Repair in Fort Wayne, IN",
    intro:
      "Dripping faucets, stiff handles, and worn shower valves waste water and raise bills. We connect Fort Wayne homeowners with local licensed plumbers for faucet and fixture repair or installation.",
    bulletsTitle: "Fixture work local plumbers handle",
    bullets: [
      "Kitchen and bathroom faucet repair / replace",
      "Shower and tub valve service",
      "Sink installs and drain assemblies",
      "Angle stops and supply line upgrades",
      "Low-flow fixture upgrades",
    ],
  },
  {
    slug: "emergency-plumbing",
    href: "/services/emergency-plumbing/",
    name: "Emergency Plumbing",
    title: "Emergency Plumber Fort Wayne, IN | 24/7 Help",
    description:
      "Emergency plumber in Fort Wayne, IN. Burst pipes, floods, and urgent plumbing — connect with local licensed pros. Call TRACKING_NUMBER_PLACEHOLDER.",
    h1: "Emergency Plumber in Fort Wayne, IN",
    intro:
      "Burst pipes, sewage backups, and major leaks cannot wait. We help Fort Wayne homeowners connect with local licensed plumbers for urgent plumbing situations.",
    bulletsTitle: "Situations that often need emergency help",
    bullets: [
      "Burst or frozen pipes",
      "Major water leaks flooding floors",
      "Sewage backup into the home",
      "No water or critically low pressure after a break",
      "Water heater failure causing flooding",
    ],
  },
] as const;

export const NEARBY_TOWNS = [
  "New Haven",
  "Huntertown",
  "Leo-Cedarville",
  "Grabill",
  "Woodburn",
  "Hoagland",
  "Monroeville",
  "Roanoke",
  "Columbia City (select requests)",
  "Auburn / Garrett area (select requests)",
  "Decatur (nearby)",
  "Bluffton (nearby)",
] as const;

export function absoluteUrl(path: string): string {
  const p = path.startsWith("/") ? path : `/${path}`;
  return `${SITE.url}${p}`;
}
