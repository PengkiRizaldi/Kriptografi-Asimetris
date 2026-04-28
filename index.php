<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Aplikasi Enkripsi &amp; Dekripsi XOR</title>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet"/>
<style>
  :root {
    --bg:        #0a0d12;
    --panel:     #0f1520;
    --border:    #1b2d4a;
    --accent:    #00d4ff;
    --accent2:   #00ff9d;
    --accent3:   #ff4f7b;
    --text:      #cdd9ee;
    --muted:     #4a6080;
    --glow:      0 0 12px #00d4ff66;
    --glow2:     0 0 12px #00ff9d66;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Rajdhani', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 16px 60px;
    overflow-x: hidden;
    position: relative;
  }

  /* ── Animated grid background ── */
  body::before {
    content: '';
    position: fixed; inset: 0; z-index: -1;
    background-image:
      linear-gradient(rgba(0,212,255,.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(0,212,255,.04) 1px, transparent 1px);
    background-size: 40px 40px;
    animation: gridShift 20s linear infinite;
  }
  @keyframes gridShift {
    from { background-position: 0 0; }
    to   { background-position: 40px 40px; }
  }

  /* ── Scan line ── */
  body::after {
    content: '';
    position: fixed; inset: 0; z-index: -1; pointer-events: none;
    background: linear-gradient(transparent 50%, rgba(0,0,0,.03) 50%);
    background-size: 100% 4px;
  }

  /* ── Corner decorations ── */
  .corner { position: fixed; width: 60px; height: 60px; }
  .corner.tl { top: 16px; left: 16px; border-top: 2px solid var(--accent); border-left: 2px solid var(--accent); }
  .corner.tr { top: 16px; right: 16px; border-top: 2px solid var(--accent); border-right: 2px solid var(--accent); }
  .corner.bl { bottom: 16px; left: 16px; border-bottom: 2px solid var(--accent); border-left: 2px solid var(--accent); }
  .corner.br { bottom: 16px; right: 16px; border-bottom: 2px solid var(--accent); border-right: 2px solid var(--accent); }

  /* ── Header ── */
  header {
    text-align: center;
    margin-bottom: 40px;
    animation: fadeDown .8s ease both;
  }
  .badge {
    display: inline-block;
    font-family: 'Share Tech Mono', monospace;
    font-size: 11px;
    letter-spacing: 3px;
    color: var(--accent);
    border: 1px solid var(--accent);
    padding: 4px 12px;
    margin-bottom: 12px;
    text-transform: uppercase;
    box-shadow: var(--glow);
  }
  h1 {
    font-family: 'Orbitron', monospace;
    font-size: clamp(1.4rem, 5vw, 2.2rem);
    font-weight: 900;
    letter-spacing: 2px;
    line-height: 1.2;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 0 20px #00d4ff55);
  }
  .sub {
    margin-top: 8px;
    font-size: 13px;
    letter-spacing: 4px;
    color: var(--muted);
    text-transform: uppercase;
    font-family: 'Share Tech Mono', monospace;
  }

  /* ── Card ── */
  .card {
    width: 100%; max-width: 560px;
    background: var(--panel);
    border: 1px solid var(--border);
    padding: 36px 32px;
    position: relative;
    animation: fadeUp .9s ease both .1s;
  }
  .card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--accent), var(--accent2), var(--accent3));
  }
  /* tiny corner marks */
  .card::after {
    content: '';
    position: absolute; bottom: -1px; right: -1px;
    width: 16px; height: 16px;
    border-bottom: 2px solid var(--accent2);
    border-right: 2px solid var(--accent2);
  }

  /* ── Form groups ── */
  .group { margin-bottom: 24px; }
  label {
    display: block;
    font-size: 11px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--accent);
    font-family: 'Share Tech Mono', monospace;
    margin-bottom: 8px;
  }
  label .req { color: var(--accent3); }

  input[type="text"],
  textarea,
  select {
    width: 100%;
    background: #070a10;
    border: 1px solid var(--border);
    color: var(--text);
    font-family: 'Share Tech Mono', monospace;
    font-size: 14px;
    padding: 12px 14px;
    outline: none;
    transition: border-color .25s, box-shadow .25s;
    -webkit-appearance: none;
    appearance: none;
    border-radius: 0;
  }
  input[type="text"]:focus,
  textarea:focus,
  select:focus {
    border-color: var(--accent);
    box-shadow: var(--glow), inset 0 0 20px #00d4ff08;
  }
  textarea { resize: vertical; min-height: 90px; line-height: 1.6; }

  /* custom select wrapper */
  .select-wrap { position: relative; }
  .select-wrap::after {
    content: '▾';
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    color: var(--accent); pointer-events: none; font-size: 14px;
  }
  select { padding-right: 38px; cursor: pointer; }

  /* ── Button ── */
  .btn-wrap { margin-top: 28px; }
  button {
    width: 100%;
    padding: 14px;
    font-family: 'Orbitron', monospace;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    cursor: pointer;
    border: none;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
    color: #000;
    position: relative;
    overflow: hidden;
    transition: opacity .2s, transform .15s;
  }
  button::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.25) 50%, transparent 100%);
    transform: translateX(-100%);
    transition: transform .5s ease;
  }
  button:hover::before { transform: translateX(100%); }
  button:hover { opacity: .9; }
  button:active { transform: scale(.98); }

  /* ── Divider ── */
  .divider {
    display: flex; align-items: center; gap: 12px;
    margin: 28px 0;
    color: var(--muted); font-size: 11px; letter-spacing: 3px;
    font-family: 'Share Tech Mono', monospace;
  }
  .divider::before, .divider::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(90deg, transparent, var(--border));
  }
  .divider::after { background: linear-gradient(270deg, transparent, var(--border)); }

  /* ── Result panel ── */
  .result-panel {
    background: #060911;
    border: 1px solid var(--border);
    border-left: 3px solid var(--accent2);
    padding: 20px;
    min-height: 72px;
    position: relative;
    transition: border-color .3s;
  }
  .result-panel.active { border-left-color: var(--accent2); box-shadow: var(--glow2); }
  .result-label {
    font-family: 'Share Tech Mono', monospace;
    font-size: 10px; letter-spacing: 3px;
    color: var(--accent2); text-transform: uppercase;
    margin-bottom: 10px;
  }
  #output {
    font-family: 'Share Tech Mono', monospace;
    font-size: 15px;
    color: var(--accent2);
    word-break: break-all;
    line-height: 1.7;
    min-height: 24px;
  }
  #output.error { color: var(--accent3); }

  /* copy button */
  #btnCopy {
    display: none;
    width: auto;
    padding: 6px 14px;
    font-size: 10px;
    font-family: 'Share Tech Mono', monospace;
    font-weight: 400;
    letter-spacing: 2px;
    background: transparent;
    border: 1px solid var(--accent2);
    color: var(--accent2);
    margin-top: 14px;
    cursor: pointer;
  }
  #btnCopy:hover { background: #00ff9d1a; }

  /* ── Status ticker ── */
  .ticker {
    margin-top: 32px;
    font-family: 'Share Tech Mono', monospace;
    font-size: 11px;
    color: var(--muted);
    letter-spacing: 2px;
    text-align: center;
    animation: fadeUp 1s ease both .4s;
  }
  .ticker span { color: var(--accent); }

  /* ── Animations ── */
  @keyframes fadeDown {
    from { opacity:0; transform: translateY(-20px); }
    to   { opacity:1; transform: translateY(0); }
  }
  @keyframes fadeUp {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: translateY(0); }
  }
  @keyframes pulse {
    0%,100% { opacity: 1; }
    50%      { opacity: 0; }
  }
  .cursor {
    display: inline-block;
    width: 9px; height: 15px;
    background: var(--accent2);
    vertical-align: middle;
    margin-left: 3px;
    animation: pulse .9s step-end infinite;
  }
</style>
</head>
<body>
  <!-- Corner decorations -->
  <div class="corner tl"></div>
  <div class="corner tr"></div>
  <div class="corner bl"></div>
  <div class="corner br"></div>

  <!-- Header -->
  <header>
    <div class="badge">// Cryptographic System v1.0</div>
    <h1>Enkripsi &amp; Dekripsi XOR</h1>
    <p class="sub">Aplikasi Kriptografi · One-Time Pad</p>
  </header>

  <!-- Card -->
  <div class="card">

    <!-- Input Pesan -->
    <div class="group">
      <label>Masukkan Pesan <span class="req">/ Teks Asli atau Cipher</span></label>
      <textarea id="inputPesan" placeholder="Ketik pesan di sini..."></textarea>
    </div>

    <!-- Kata Kunci -->
    <div class="group">
      <label>Kata Kunci <span class="req">/ Key</span></label>
      <input type="text" id="inputKey" placeholder="Contoh: UMPONTIANAK" />
    </div>

    <!-- Pilih Aksi -->
    <div class="group">
      <label>Pilih Aksi</label>
      <div class="select-wrap">
        <select id="selectAksi">
          <option value="enkripsi">Enkripsi (Plain → Cipher)</option>
          <option value="dekripsi">Dekripsi (Cipher → Plain)</option>
        </select>
      </div>
    </div>

    <!-- Button -->
    <div class="btn-wrap">
      <button onclick="prosesKriptografi()">⟳ Proses Kriptografi</button>
    </div>

    <!-- Divider -->
    <div class="divider">HASIL PROSES</div>

    <!-- Result -->
    <div class="result-panel" id="resultPanel">
      <div class="result-label">Output</div>
      <div id="output">—<span class="cursor"></span></div>
      <button id="btnCopy" onclick="salinHasil()">⎘ SALIN</button>
    </div>

  </div><!-- /card -->

  <div class="ticker">
    STATUS: <span id="statusTicker">SIAP</span> · XOR CIPHER · SYMMETRIC KEY
  </div>

<script>
/* ─────────────────────────────────────────
   XOR Encryption / Decryption
   Karena XOR simetris, enkripsi == dekripsi
   Hanya bedanya:
     Enkripsi : input teks biasa  → hex string
     Dekripsi : input hex string  → teks biasa
───────────────────────────────────────────*/

function xorBytes(text, key) {
  // XOR setiap karakter pesan dengan karakter kunci (berulang)
  let result = '';
  for (let i = 0; i < text.length; i++) {
    const charCode = text.charCodeAt(i) ^ key.charCodeAt(i % key.length);
    result += String.fromCharCode(charCode);
  }
  return result;
}

function toHex(str) {
  return Array.from(str).map(c =>
    c.charCodeAt(0).toString(16).padStart(2, '0')
  ).join('');
}

function fromHex(hex) {
  hex = hex.replace(/\s+/g, '');
  if (hex.length % 2 !== 0) throw new Error('Hex string tidak valid.');
  let str = '';
  for (let i = 0; i < hex.length; i += 2) {
    str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
  }
  return str;
}

function setStatus(msg) {
  document.getElementById('statusTicker').textContent = msg;
}

function prosesKriptografi() {
  const pesan  = document.getElementById('inputPesan').value.trim();
  const key    = document.getElementById('inputKey').value.trim();
  const aksi   = document.getElementById('selectAksi').value;
  const outEl  = document.getElementById('output');
  const panel  = document.getElementById('resultPanel');
  const btnCpy = document.getElementById('btnCopy');

  outEl.className = '';

  // Validasi
  if (!pesan) {
    tampilError('⚠ Pesan tidak boleh kosong.');
    return;
  }
  if (!key) {
    tampilError('⚠ Kata kunci (key) tidak boleh kosong.');
    return;
  }

  try {
    let hasil;

    if (aksi === 'enkripsi') {
      /* ENKRIPSI: plaintext → XOR → hex */
      const xored = xorBytes(pesan, key);
      hasil = toHex(xored);
      setStatus('ENKRIPSI BERHASIL');
    } else {
      /* DEKRIPSI: hex → XOR → plaintext */
      const binStr = fromHex(pesan);
      hasil = xorBytes(binStr, key);
      setStatus('DEKRIPSI BERHASIL');
    }

    // Tampilkan hasil
    outEl.textContent = '';
    panel.classList.add('active');
    btnCpy.style.display = 'block';

    // Efek ketik satu per satu
    let i = 0;
    const speed = Math.max(8, Math.min(40, 1000 / hasil.length));
    const interval = setInterval(() => {
      if (i < hasil.length) {
        outEl.textContent += hasil[i++];
      } else {
        clearInterval(interval);
      }
    }, speed);

    outEl.dataset.full = hasil; // simpan untuk copy

  } catch (err) {
    tampilError('⚠ ' + err.message);
  }
}

function tampilError(msg) {
  const outEl  = document.getElementById('output');
  const btnCpy = document.getElementById('btnCopy');
  outEl.textContent = msg;
  outEl.className   = 'error';
  btnCpy.style.display = 'none';
  setStatus('ERROR');
}

function salinHasil() {
  const outEl = document.getElementById('output');
  const teks  = outEl.dataset.full || outEl.textContent;
  navigator.clipboard.writeText(teks).then(() => {
    const btn = document.getElementById('btnCopy');
    btn.textContent = '✓ TERSALIN!';
    setTimeout(() => btn.textContent = '⎘ SALIN', 2000);
  });
}

// Enter key shortcut di input
document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && e.ctrlKey) prosesKriptografi();
});
</script>
</body>
</html>