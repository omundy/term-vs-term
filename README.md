
# Term vs. Term

Compare search results for phrases from the <a href="https://pro.dp.la/developers/api-codex">Digital Public Library of America API</a>

View the live app: http://owenmundy.com/work/term-vs-term/

## Setup

The site is static HTML/CSS/JS (Bootstrap 5.3 via CDN), hosted on GitHub Pages. The DPLA API doesn't allow browser requests (no CORS) and needs a private key, so requests go through a small [Cloudflare Worker](worker/worker.js).

### Worker

```sh
cd worker
npx wrangler login
npx wrangler secret put DPLA_KEY   # paste your DPLA API key
npx wrangler deploy                # prints https://dpla-proxy.<subdomain>.workers.dev
```

Set `API_URL` in [assets/js/main.js](assets/js/main.js) to the deployed URL. Allowed origins are listed in `ALLOWED_ORIGINS` in the worker.

Get a key: https://pro.dp.la/developers/policies#get-a-key

### Local development

```sh
echo "DPLA_KEY=your-key" > worker/.dev.vars
(cd worker && npx wrangler dev)    # http://localhost:8787
python3 -m http.server 8000        # site at http://localhost:8000
```

Temporarily set `API_URL` to `http://localhost:8787` while developing locally.
