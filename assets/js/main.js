/**
 * Term vs. Term
 * Compare result counts for two phrases from the DPLA API.
 * Requests go through a Cloudflare Worker (worker/) so the DPLA key stays secret
 * (the DPLA API does not allow direct browser requests via CORS).
 */

// Cloudflare Worker URL (see worker/ and README)
const API_URL = 'https://dpla-proxy.YOUR-SUBDOMAIN.workers.dev';
const MIN_BAR = 2;
const MAX_BAR = 300;

const fmt = new Intl.NumberFormat();

function scale(value, max) {
	if (max === 0) return 0;
	return Math.round(MIN_BAR + (value / max) * (MAX_BAR - MIN_BAR));
}

async function getCount(term) {
	const res = await fetch(`${API_URL}?q=${encodeURIComponent(term)}`);
	if (!res.ok) throw new Error(`Request failed (${res.status})`);
	const data = await res.json();
	return Number(data.count) || 0;
}

function showResult(n, term, count, score, winner) {
	const bar = document.getElementById(`bar${n}`);
	bar.style.height = `${score}px`;
	bar.classList.toggle('win', winner);

	const result = document.getElementById(`result${n}`);
	const link = result.querySelector('a');
	link.href = `https://dp.la/search?q=${encodeURIComponent(term)}`;
	link.textContent = `${fmt.format(count)} results`;
	result.hidden = false;
}

async function compare(t1, t2) {
	const status = document.querySelector('#status small');
	const button = document.querySelector('.compare-btn');

	document.title = `Term vs. Term - ${t1} vs. ${t2}`;
	document.getElementById('questionmark').hidden = true;
	status.textContent = 'Searching…';
	button.disabled = true;

	const start = performance.now();
	try {
		const [c1, c2] = await Promise.all([getCount(t1), getCount(t2)]);
		const total = c1 + c2;
		const s1 = scale(c1, total);
		const s2 = scale(c2, total);

		showResult(1, t1, c1, s1, s1 >= s2);
		showResult(2, t2, c2, s2, s2 >= s1);

		const secs = ((performance.now() - start) / 1000).toFixed(3);
		status.textContent = `${fmt.format(total)} results returned in ${secs} seconds`;
	} catch (err) {
		console.error(err);
		status.textContent = 'Sorry, the search failed. Please try again.';
	} finally {
		button.disabled = false;
	}
}

document.addEventListener('DOMContentLoaded', () => {
	const params = new URLSearchParams(window.location.search);
	const t1 = (params.get('q1') || '').trim();
	const t2 = (params.get('q2') || '').trim();

	document.getElementById('q1').value = t1;
	document.getElementById('q2').value = t2;

	if (t1 && t2) compare(t1, t2);
});
