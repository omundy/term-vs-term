/**
 * DPLA proxy for Term vs. Term
 * GET /?q=term -> {"count": n}
 * Keeps the DPLA key server-side (set with: npx wrangler secret put DPLA_KEY)
 * and adds the CORS header the DPLA API doesn't send.
 */

const ALLOWED_ORIGINS = [
	'https://omundy.github.io',
	'https://owenmundy.com',
	'http://localhost:8000',
	'http://127.0.0.1:8000',
];

export default {
	async fetch(request, env, ctx) {
		const origin = request.headers.get('Origin');
		const headers = {
			'Content-Type': 'application/json',
			'Vary': 'Origin',
		};
		if (ALLOWED_ORIGINS.includes(origin)) headers['Access-Control-Allow-Origin'] = origin;

		const reply = (body, status = 200) => new Response(JSON.stringify(body), { status, headers });

		if (request.method !== 'GET') return reply({ error: 'Method not allowed' }, 405);

		const q = (new URL(request.url).searchParams.get('q') || '').trim();
		if (!q || q.length > 200) return reply({ error: 'Missing or invalid q' }, 400);

		const url = 'https://api.dp.la/v2/items?' + new URLSearchParams({
			q,
			page_size: '0',
			api_key: env.DPLA_KEY,
		});

		try {
			// Cache upstream responses at the edge for a day
			const res = await fetch(url, { cf: { cacheTtl: 86400, cacheEverything: true } });
			const data = await res.json();
			if (!res.ok || data.count == null) return reply({ error: 'Upstream request failed' }, 502);
			return reply({ count: Number(data.count) });
		} catch (err) {
			return reply({ error: 'Upstream request failed' }, 502);
		}
	},
};
