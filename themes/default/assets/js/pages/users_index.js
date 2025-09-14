import { request } from '../app/api.js';

async function fetchUsers(ctx){
    ctx.loading = true; ctx.error = '';
    try {
        const res = await request(`/v1/users?page=${ctx.page}&limit=${ctx.limit}`);
        const j = await res.json();
        if(!res.ok){ ctx.error = j?.message || 'Ошибка загрузки'; ctx.loading = false; return; }
        ctx.items = (j?.data?.items) || [];
    } catch(e){
        ctx.error = e?.message || 'Сеть недоступна';
    } finally {
        ctx.loading = false;
    }
}

document.addEventListener('alpine:init', () => {
    // Use event to trigger fetch to avoid inline expressions complexity
    document.addEventListener('users:fetch', (e) => {
        const root = e.target.closest('section');
        if (!root || !root.__x) return;
        fetchUsers(root.__x.$data);
    });
});

window.addEventListener('load', () => {
    // Initial fetch after Alpine initializes components
    const root = document.querySelector('section[x-data]');
    if (root && root.__x) fetchUsers(root.__x.$data);
});


