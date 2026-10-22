<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Panel de pruebas · Seguridad API</title>
<style>
    :root {
        --bg: #f4f5f7; --card: #fff; --text: #1f2328; --muted: #656d76; --border: #d8dee4;
        --primary: #2563eb; --ok: #16a34a; --warn: #d97706; --err: #dc2626;
    }
    * { box-sizing: border-box; }
    body { margin: 0; font: 14px/1.45 system-ui, -apple-system, "Segoe UI", sans-serif; background: var(--bg); color: var(--text); }
    header { background: #111827; color: #fff; padding: 14px 24px; display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; }
    header h1 { font-size: 18px; margin: 0; }
    header span { color: #9ca3af; font-size: 13px; }
    main { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 16px; padding: 16px 24px; max-width: 1400px; margin: 0 auto; }
    @media (max-width: 900px) { main { grid-template-columns: 1fr; padding: 16px; } }
    .col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
    section { background: var(--card); border: 1px solid var(--border); border-radius: 8px; padding: 14px 16px; }
    section h2 { font-size: 15px; margin: 0 0 10px; display: flex; justify-content: space-between; align-items: center; gap: 8px; }
    .hint { color: var(--muted); font-size: 12px; margin: 0 0 10px; }
    .row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
    .row > * { flex: 1 1 140px; min-width: 0; }
    label { font-size: 12px; color: var(--muted); display: block; margin-bottom: 2px; }
    input, select, textarea { width: 100%; padding: 6px 8px; border: 1px solid var(--border); border-radius: 6px; font: inherit; background: #fff; }
    textarea { font-family: ui-monospace, Consolas, monospace; font-size: 12px; min-height: 70px; resize: vertical; }
    button { padding: 6px 12px; border: 1px solid var(--primary); background: var(--primary); color: #fff; border-radius: 6px; font: inherit; cursor: pointer; }
    button.sec { background: #fff; color: var(--primary); }
    button.danger { background: #fff; color: var(--err); border-color: var(--err); }
    button.small { padding: 2px 8px; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { text-align: left; padding: 5px 6px; border-bottom: 1px solid var(--border); vertical-align: top; }
    th { color: var(--muted); font-weight: 600; font-size: 12px; }
    td.acc { white-space: nowrap; text-align: right; }
    .table-wrap { overflow-x: auto; max-height: 260px; overflow-y: auto; }
    .done { text-decoration: line-through; color: var(--muted); }
    #log { display: flex; flex-direction: column; gap: 8px; max-height: 70vh; overflow-y: auto; }
    .entry { border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
    .entry .head { display: flex; gap: 8px; align-items: center; padding: 6px 8px; background: #f6f8fa; font-family: ui-monospace, Consolas, monospace; font-size: 12px; flex-wrap: wrap; }
    .badge { padding: 1px 6px; border-radius: 4px; color: #fff; font-weight: 600; }
    .s2 { background: var(--ok); } .s4 { background: var(--warn); } .s5, .s0 { background: var(--err); }
    .entry pre { margin: 0; padding: 8px; font-size: 12px; max-height: 220px; overflow: auto; white-space: pre-wrap; word-break: break-word; }
    .entry .sent { color: var(--muted); border-bottom: 1px dashed var(--border); }
    .muted { color: var(--muted); }
    #burstResult { font-family: ui-monospace, Consolas, monospace; font-size: 12px; margin-top: 6px; }
</style>
</head>
<body>
<header>
    <h1>Panel de pruebas · Seguridad API</h1>
    <span>Base: <code id="baseUrl"></code></span>
</header>

<main>
<div class="col">
    <section>
        <h2>Credenciales enviadas en cada petición</h2>
        <p class="hint">Déjalas vacías mientras la API no tenga seguridad. Se irán usando en cada paso (API key, JWT). Se olvidan al cerrar la pestaña.</p>
        <div class="row">
            <div><label for="apiKey">X-API-KEY</label><input id="apiKey" placeholder="(vacío)"></div>
            <div><label for="token">Authorization: Bearer</label><input id="token" placeholder="(vacío)"></div>
        </div>
    </section>

    <section>
        <h2>Sesión (JWT)
            <span>
                <button class="sec small" data-action="me">¿Quién soy?</button>
                <button class="sec small" data-action="refresh">Refrescar token</button>
                <button class="danger small" data-action="logout">Logout</button>
            </span>
        </h2>
        <p class="hint">Login y registro guardan el token en el campo "Authorization: Bearer" automáticamente.</p>
        <form id="loginForm" class="row">
            <div><label>Email</label><input name="email" type="email" required></div>
            <div><label>Contraseña</label><input name="password" type="password" required></div>
            <div style="flex:0 0 auto;align-self:end"><button>Login</button></div>
        </form>
        <form id="registerForm" class="row">
            <div><label>Nombre</label><input name="name" required></div>
            <div><label>Email</label><input name="email" type="email" required></div>
            <div><label>Contraseña</label><input name="password" type="password" required></div>
            <div style="flex:0 0 auto;align-self:end"><button class="sec">Registrar</button></div>
        </form>
        <div id="sessionInfo" class="hint" style="margin:6px 0 0">Sin token.</div>
        <pre id="jwtPayload" class="hint" style="margin:6px 0 0;white-space:pre-wrap;word-break:break-all" hidden></pre>
    </section>

    <section>
        <h2>Usuarios <button class="sec small" data-action="loadUsers">Recargar</button></h2>
        <div class="table-wrap">
            <table><thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th></th></tr></thead><tbody id="users"></tbody></table>
        </div>
    </section>

    <section>
        <h2>Tareas <button class="sec small" data-action="loadTareas">Recargar</button></h2>
        <form id="tareaForm" class="row">
            <div style="flex:0 1 90px"><label>user_id</label><input name="user_id" type="number" required></div>
            <div><label>Título</label><input name="titulo" required></div>
            <div><label>Descripción</label><input name="descripcion"></div>
            <div style="flex:0 0 auto;align-self:end"><button>Crear</button></div>
        </form>
        <div class="table-wrap">
            <table><thead><tr><th>ID</th><th>user_id</th><th>Título</th><th></th></tr></thead><tbody id="tareas"></tbody></table>
        </div>
    </section>

    <section>
        <h2>Petición libre</h2>
        <p class="hint">Para probar ataques a mano: pedir recursos de otro usuario, mandar datos extra, etc.</p>
        <form id="freeForm">
            <div class="row">
                <div style="flex:0 0 110px"><label>Método</label>
                    <select name="method"><option>GET</option><option>POST</option><option>PUT</option><option>PATCH</option><option>DELETE</option></select>
                </div>
                <div><label>Ruta</label><input name="path" value="/api/tareas/1"></div>
            </div>
            <label>Body JSON (opcional)</label>
            <textarea name="body" placeholder='{"titulo": "hackeado"}'></textarea>
            <div class="row" style="margin-top:8px"><div style="flex:0 0 auto"><button>Enviar</button></div></div>
        </form>
    </section>

    <section>
        <h2>Ráfaga de peticiones</h2>
        <p class="hint">Manda muchas peticiones seguidas y cuenta los códigos de respuesta (para probar el rate limiter).</p>
        <form id="burstForm" class="row">
            <div style="flex:0 0 110px"><label>Método</label><select name="method"><option>GET</option><option>POST</option></select></div>
            <div><label>Ruta</label><input name="path" value="/api/tareas"></div>
            <div style="flex:0 0 90px"><label>Cantidad</label><input name="count" type="number" value="20" min="1" max="500"></div>
            <div style="flex:0 0 auto;align-self:end"><button>Lanzar</button></div>
        </form>
        <div id="burstResult" class="muted"></div>
    </section>
</div>

<div class="col">
    <section>
        <h2>Registro de respuestas <button class="sec small" data-action="clearLog">Limpiar</button></h2>
        <div id="log"><p class="muted">Aquí aparecerá cada petición con su código y respuesta.</p></div>
    </section>
</div>
</main>

@verbatim
<script>
const BASE = window.location.origin;
document.getElementById('baseUrl').textContent = BASE + '/api';

const $ = (id) => document.getElementById(id);
// sessionStorage: las credenciales se borran al cerrar la pestaña (localStorage las dejaría en disco para siempre).
const store = {
    get(k) { try { localStorage.removeItem(k); return sessionStorage.getItem(k) || ''; } catch { return ''; } },
    set(k, v) { try { sessionStorage.setItem(k, v); } catch {} },
};
['apiKey', 'token'].forEach((id) => {
    $(id).value = store.get(id);
    $(id).addEventListener('input', () => { store.set(id, $(id).value.trim()); if (id === 'token') showToken(); });
});

function setToken(token) {
    $('token').value = token || '';
    store.set('token', token || '');
    showToken();
}

// Decodifica el payload del JWT (la parte del medio). Se puede leer sin la clave: no está cifrado, solo firmado.
function showToken() {
    const token = $('token').value.trim();
    const info = $('sessionInfo');
    const pre = $('jwtPayload');
    if (!token) { info.textContent = 'Sin token.'; pre.hidden = true; return; }
    try {
        const b64 = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
        const payload = JSON.parse(atob(b64.padEnd(b64.length + (4 - b64.length % 4) % 4, '=')));
        const exp = payload.exp ? new Date(payload.exp * 1000) : null;
        const vencido = exp && exp < new Date();
        info.textContent = `Token de usuario #${payload.sub}` + (exp ? ` · caduca ${exp.toLocaleTimeString()}${vencido ? ' (VENCIDO)' : ''}` : '');
        pre.textContent = 'Payload: ' + JSON.stringify(payload, null, 2);
        pre.hidden = false;
    } catch {
        info.textContent = 'El token no tiene formato JWT.';
        pre.hidden = true;
    }
}
showToken();

function headers(hasBody) {
    const h = { Accept: 'application/json' };
    if (hasBody) h['Content-Type'] = 'application/json';
    if ($('apiKey').value.trim()) h['X-API-KEY'] = $('apiKey').value.trim();
    if ($('token').value.trim()) h['Authorization'] = 'Bearer ' + $('token').value.trim();
    return h;
}

async function api(method, path, body, { log = true } = {}) {
    const started = performance.now();
    let status = 0, data = null, text = '';
    try {
        const res = await fetch(BASE + path, {
            method,
            headers: headers(body !== undefined),
            body: body !== undefined ? JSON.stringify(body) : undefined,
        });
        status = res.status;
        text = await res.text();
        try { data = text ? JSON.parse(text) : null; } catch { data = text; }
    } catch (e) {
        data = 'Error de red: ' + e.message;
    }
    if (log) addLog(method, path, body, status, data, Math.round(performance.now() - started));
    return { status, data, ok: status >= 200 && status < 300 };
}

function addLog(method, path, body, status, data, ms) {
    const log = $('log');
    if (log.querySelector('p.muted')) log.innerHTML = '';
    const el = document.createElement('div');
    el.className = 'entry';
    const head = document.createElement('div');
    head.className = 'head';
    head.innerHTML = `<span class="badge s${String(status)[0]}">${status || 'ERR'}</span>`;
    head.append(`${method} ${path}`);
    const time = document.createElement('span');
    time.className = 'muted';
    time.style.marginLeft = 'auto';
    time.textContent = `${ms} ms · ${new Date().toLocaleTimeString()}`;
    head.append(time);
    el.append(head);
    if (body !== undefined) {
        const sent = document.createElement('pre');
        sent.className = 'sent';
        sent.textContent = '→ ' + JSON.stringify(body);
        el.append(sent);
    }
    const pre = document.createElement('pre');
    pre.textContent = data === null ? '(sin contenido)' : (typeof data === 'string' ? data : JSON.stringify(data, null, 2));
    el.append(pre);
    log.prepend(el);
}

function cell(text, cls) {
    const td = document.createElement('td');
    td.textContent = text ?? '';
    if (cls) td.className = cls;
    return td;
}
function btn(label, cls, onClick) {
    const b = document.createElement('button');
    b.className = 'small ' + cls;
    b.textContent = label;
    b.addEventListener('click', onClick);
    return b;
}
function emptyRow(tbody, cols, msg) {
    const tr = document.createElement('tr');
    const td = cell(msg, 'muted');
    td.colSpan = cols;
    tr.append(td);
    tbody.append(tr);
}

async function loadUsers() {
    const { ok, data } = await api('GET', '/api/users');
    const tbody = $('users');
    tbody.innerHTML = '';
    if (!ok || !Array.isArray(data)) return emptyRow(tbody, 4, 'No se pudo cargar (mira el registro).');
    if (!data.length) return emptyRow(tbody, 4, 'Sin usuarios.');
    data.forEach((u) => {
        const tr = document.createElement('tr');
        const acc = cell('', 'acc');
        acc.append(
            btn('Ver', 'sec', () => api('GET', `/api/users/${u.id}`)),
            ' ',
            btn('Borrar', 'danger', async () => { await api('DELETE', `/api/users/${u.id}`); loadUsers(); loadTareas(); }),
        );
        tr.append(cell(u.id), cell(u.name), cell(u.email), acc);
        tbody.append(tr);
    });
}

async function loadTareas() {
    const { ok, data } = await api('GET', '/api/tareas');
    const tbody = $('tareas');
    tbody.innerHTML = '';
    if (!ok || !Array.isArray(data)) return emptyRow(tbody, 4, 'No se pudo cargar (mira el registro).');
    if (!data.length) return emptyRow(tbody, 4, 'Sin tareas.');
    data.forEach((t) => {
        const tr = document.createElement('tr');
        const acc = cell('', 'acc');
        acc.append(
            btn(t.completada ? 'Reabrir' : 'Completar', 'sec', async () => {
                await api('PATCH', `/api/tareas/${t.id}`, { completada: !t.completada });
                loadTareas();
            }),
            ' ',
            btn('Borrar', 'danger', async () => { await api('DELETE', `/api/tareas/${t.id}`); loadTareas(); }),
        );
        tr.append(cell(t.id), cell(t.user_id), cell(t.titulo, t.completada ? 'done' : ''), acc);
        tbody.append(tr);
    });
}

function formData(form) {
    const data = {};
    new FormData(form).forEach((v, k) => { if (v !== '') data[k] = v; });
    return data;
}

$('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const { ok, data } = await api('POST', '/api/login', formData(e.target));
    if (ok) { setToken(data.token); e.target.reset(); loadUsers(); loadTareas(); }
});

$('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const { ok, data } = await api('POST', '/api/register', formData(e.target));
    if (ok) { setToken(data.token); e.target.reset(); loadUsers(); loadTareas(); }
});

async function me() { await api('GET', '/api/me'); }

async function refresh() {
    const { ok, data } = await api('POST', '/api/refresh');
    if (ok) setToken(data.token);
}

async function logout() {
    await api('POST', '/api/logout');
    // Se borra solo del panel. Si guardaste una copia del token, prueba a usarla: la API la rechaza (lista negra).
    setToken('');
    loadUsers();
    loadTareas();
}

$('tareaForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = formData(e.target);
    data.user_id = Number(data.user_id);
    const { ok } = await api('POST', '/api/tareas', data);
    if (ok) { e.target.reset(); loadTareas(); }
});

$('freeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const { method, path, body } = Object.fromEntries(new FormData(e.target));
    let parsed;
    if (body.trim()) {
        try { parsed = JSON.parse(body); } catch { return addLog(method, path, undefined, 0, 'El body no es JSON válido.', 0); }
    }
    await api(method, path.startsWith('/') ? path : '/' + path, parsed);
});

$('burstForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const { method, path, count } = Object.fromEntries(new FormData(e.target));
    const n = Math.min(Math.max(Number(count) || 1, 1), 500);
    const out = $('burstResult');
    out.textContent = `Enviando ${n} peticiones…`;
    const results = await Promise.all(Array.from({ length: n }, () => api(method, path, method === 'POST' ? {} : undefined, { log: false })));
    const counts = {};
    results.forEach((r) => { counts[r.status || 'ERR'] = (counts[r.status || 'ERR'] || 0) + 1; });
    out.textContent = `${n} peticiones → ` + Object.entries(counts).map(([s, c]) => `${s}: ${c}`).join(' · ');
    addLog(method, `${path}  ×${n}`, undefined, results[results.length - 1].status, counts, 0);
});

document.querySelectorAll('[data-action]').forEach((b) => {
    const fn = { loadUsers, loadTareas, me, refresh, logout, clearLog: () => { $('log').innerHTML = '<p class="muted">Registro limpio.</p>'; } }[b.dataset.action];
    b.addEventListener('click', fn);
});

loadUsers();
loadTareas();
</script>
@endverbatim
</body>
</html>
