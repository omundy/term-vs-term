# Term vs. Term

Compare search results for phrases from the [Digital Public Library of America API](https://pro.dp.la/developers/api-codex).

**Live app:** https://omundy.github.io/term-vs-term/

Link directly to a comparison with `q1` and `q2` (use `+` for spaces):

```
https://omundy.github.io/term-vs-term/?q1=cats&q2=dogs
https://omundy.github.io/term-vs-term/?q1=the+end+of&q2=the+world+as+we+know+it
```

## How it works

A static site on GitHub Pages ([Bootstrap 5.3](https://getbootstrap.com/), no build step) fetches result counts through a small [Cloudflare Worker](worker/worker.js). The Worker keeps the DPLA API key secret and adds the CORS headers the DPLA API doesn't send.

See [SETUP.md](SETUP.md) for deployment, local development, and update instructions.

## Credits

Created by [Owen Mundy](https://owenmundy.com) for the Digital Humanities DPLA Hackathon @ Florida State University, 2014.
