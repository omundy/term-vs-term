# Term vs. Term: Setup

How the app is built, deployed, and run locally. See the [README](README.md) for an overview.

## How it works

```
Browser (GitHub Pages)  ──?q=term──▶  Cloudflare Worker  ──q + api_key──▶  DPLA API
  index.html, assets/                   worker/worker.js                    api.dp.la/v2/items
           ◀──────── {"count": n} ────────
```

- **Front end:** static HTML/CSS/JS with [Bootstrap 5.3](https://getbootstrap.com/) from a CDN. No build step and no dependencies. Served by GitHub Pages from the `main` branch root.
- **Proxy:** the DPLA API doesn't send CORS headers and needs a private key, so the browser can't call it directly. A small [Cloudflare Worker](worker/worker.js) holds the key as a secret, forwards the query, caches each result for a day, and returns only the count. It only answers requests from the origins listed in `ALLOWED_ORIGINS`.

| Piece | Where | URL |
| --- | --- | --- |
| Site | GitHub Pages (`main`, `/`) | https://omundy.github.io/term-vs-term/ |
| Proxy | Cloudflare Worker `dpla-proxy` | https://dpla-proxy.omundy.workers.dev |

## Setup from scratch

You need Node.js (for `npx wrangler`), a free [Cloudflare account](https://dash.cloudflare.com/sign-up), and a [DPLA API key](https://pro.dp.la/developers/policies#get-a-key).

### 1. Deploy the Worker

```sh
cd worker
npx wrangler login                 # opens a browser to authorize Cloudflare
npx wrangler secret put DPLA_KEY   # type the name as-is; paste your key at the prompt
npx wrangler deploy                # prints https://dpla-proxy.<subdomain>.workers.dev
```

Check that it works:

```sh
curl "https://dpla-proxy.<subdomain>.workers.dev/?q=cats"   # {"count":11178}
```

### 2. Point the site at the Worker

In [assets/js/main.js](assets/js/main.js), set the production URL in `API_URL` to your Worker URL. If the site will be served from a domain other than `omundy.github.io` or `owenmundy.com`, add that domain to `ALLOWED_ORIGINS` in [worker/worker.js](worker/worker.js) and run `npx wrangler deploy` again.

### 3. Turn on GitHub Pages

Push to `main`. Then go to **Settings → Pages**, set **Source** to *Deploy from a branch*, choose `main` and `/ (root)`, and save. Once GitHub has issued the certificate, tick **Enforce HTTPS**. The site also redirects `http` to `https` by itself, since the Worker only accepts `https` origins.

Or use the GitHub CLI:

```sh
gh api -X POST repos/<owner>/term-vs-term/pages -f 'source[branch]=main' -f 'source[path]=/'
```

## Local development

```sh
echo "DPLA_KEY=your-key" > worker/.dev.vars    # gitignored
(cd worker && npx wrangler dev)                 # Worker at http://localhost:8787
python3 -m http.server 8000                     # site at http://localhost:8000
```

On `localhost`, the page uses the local Worker automatically. Use port 8000, because that's the local origin the Worker allows.

## Updating

- **Site:** commit and push to `main`. Pages redeploys within a minute or two.
- **Worker code:** `cd worker && npx wrangler deploy`
- **API key:** `cd worker && npx wrangler secret put DPLA_KEY` (takes effect immediately, no redeploy needed)
- **Worker logs:** `cd worker && npx wrangler tail`
