
// k6 run tools/k6/login-and-list-users.js
import http from 'k6/http';
import { sleep, check } from 'k6';

export const options = { vus: 5, duration: '30s' };

export default function () {
  const base = __ENV.BASE_URL || 'http://localhost:8080/api';
  const res = http.post(`${base}/auth/login`, JSON.stringify({email:'demo@example.com',password:'password123'}), {
    headers: { 'Content-Type':'application/json' },
  });
  check(res, { 'login 200': (r) => r.status === 200 });
  const token = res.json('access_token');
  const list = http.get(`${base}/users?per_page=5`, { headers: { Authorization: `Bearer ${token}` } });
  check(list, { 'users 200': (r) => r.status === 200 });
  sleep(1);
}
