# Get Fort Wayne Plumber — Lead-Gen Website

Production Next.js (App Router) static site for **getfortwayneplumber.com**.

**Business:** Get Fort Wayne Plumber / Fort Wayne Plumbing Pros  
**Role:** Marketing & lead-connection only — **not** a plumbing contractor.

---

## Local development

```bash
npm install
npm run dev
```

Open [http://localhost:3000](http://localhost:3000).

## Build (static export)

```bash
npm install
npm run build
```

Output is written to `out/` (Next.js `output: "export"` with trailing slashes). Serve locally with any static host, e.g.:

```bash
npx serve out
```

## Deploy to Vercel

1. Import the GitHub repo `davidpersons10-cloud/getfortwayneplumber` in the [Vercel dashboard](https://vercel.com/new).
2. Framework preset: **Next.js** (build command `npm run build`, output uses static export).
3. Deploy. Optionally attach a Vercel preview domain first.
4. When ready for production DNS, point `getfortwayneplumber.com` to Vercel (or your chosen host).

**Note:** `getfortwayneplumber.com` still points at Hostinger parking until DNS is updated. Connecting Vercel alone does not change the live apex until nameservers / records are switched.

## Set the tracking phone number

Replace the placeholder in **one place**:

- File: `lib/site.ts`
- Constant: `TRACKING_NUMBER_PLACEHOLDER`

That value drives on-page display and all `tel:` links. Do **not** invent a street address — NAP / JSON-LD is service-area based (Fort Wayne, IN metro).

## Contact leads

The contact form uses client-side validation and a **mailto** fallback to `leads@getfortwayneplumber.com`. Swap in a form backend or serverless handler later if desired.

## Routes

| Path | Page |
|------|------|
| `/` | Home |
| `/services/` | Services hub |
| `/services/drain-cleaning/` | Drain cleaning |
| `/services/water-heater/` | Water heater |
| `/services/sewer-line/` | Sewer line |
| `/services/leak-repair/` | Leak repair |
| `/services/toilet-repair/` | Toilet repair |
| `/services/faucet-fixture/` | Faucet & fixture |
| `/services/emergency-plumbing/` | Emergency |
| `/service-area/` | Service area |
| `/about/` | About |
| `/contact/` | Contact |
| `/privacy/` | Privacy |
| `/terms/` | Terms |

Also includes `public/robots.txt` and `public/sitemap.xml`.

## Disclosure

Lead-gen disclosure appears on **Home**, **About**, **Contact**, and the **sitewide footer**.
