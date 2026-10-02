import http from 'k6/http';
import { check, sleep } from 'k6';

// Jalankan dari mesin terpisah terhadap staging yang diotorisasi:
// k6 run -e BASE_URL=https://staging.domain.tld -e VUS=20 -e DURATION=2m config/deployment/load-test.js
// Beban GET publik; checkout/stok serentak perlu uji terpisah memakai akun staging dan Midtrans sandbox.
if (!__ENV.BASE_URL) throw new Error('BASE_URL wajib diisi dengan target uji yang diotorisasi.');
const base = __ENV.BASE_URL.replace(/\/$/, '');
export const options = {
    scenarios: { browse: { executor: 'constant-vus', vus: Number(__ENV.VUS || 10), duration: __ENV.DURATION || '1m', gracefulStop: '10s' } },
    thresholds: { http_req_failed: ['rate<0.01'], http_req_duration: ['p(95)<1000'] },
};
const paths = ['/', '/open-preorder', '/oleh-oleh', '/cari-trip', '/blog'];
export default function () {
    const path = paths[Math.floor(Math.random() * paths.length)];
    const response = http.get(base + path, { tags: { name: path }, redirects: 2 });
    check(response, { 'halaman berhasil': r => r.status === 200, 'bukan halaman error Laravel': r => !r.body?.includes('Whoops, looks like something went wrong') });
    sleep(1);
}
