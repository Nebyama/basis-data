const STORAGE_KEY = 'oreste-static-demo';
const DATA_PATH = window.location.pathname.includes('/pages/') ? '../oreste_data.json' : 'oreste_data.json';

let baseAlternatives = [];
let criteria = [];
let currentAlternatives = [];

function formatNumber(value) {
  return Number(value || 0).toFixed(2);
}

function sortByCriterion(items, key) {
  return [...items].sort((a, b) => Number(b[key]) - Number(a[key]));
}

function buildRanking(alternatives, criteriaList) {
  const total = alternatives.length || 1;
  return alternatives
    .map((item) => {
      let acumulasi = 0;
      criteriaList.forEach((criterion, index) => {
        const ranked = sortByCriterion(alternatives, criterion.key);
        const rawRank = ranked.findIndex((entry) => entry.nama === item.nama) + 1;
        const bessonRank = rawRank;
        const normalisasi = rawRank / total;
        const rc = Number(criterion.rank_bobot || index + 1);
        const distance = Math.pow(0.5 * Math.pow(rc, 3) + 0.5 * Math.pow(bessonRank, 3), 1 / 3);
        acumulasi += Number(criterion.bobot || 0) * distance;
      });
      return { ...item, acumulasi };
    })
    .sort((a, b) => a.acumulasi - b.acumulasi)
    .map((item, index) => ({ ...item, ranking: index + 1 }));
}

function saveAlternatives(items) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

function loadAlternatives() {
  const stored = localStorage.getItem(STORAGE_KEY);
  return stored ? JSON.parse(stored) : null;
}

async function loadData() {
  const dataUrl = window.location.pathname.includes('/pages/') ? '../oreste_data.json' : 'oreste_data.json';
  const response = await fetch(dataUrl);
  const data = await response.json();
  baseAlternatives = data.alternatif;
  criteria = data.bobot.map((item) => ({
    nama: item.nama,
    key: 'c' + item.no,
    bobot: Number(item.bobot),
    rank_bobot: Number(item.rank_bobot),
  }));

  const stored = loadAlternatives();
  currentAlternatives = stored && Array.isArray(stored) && stored.length ? stored : baseAlternatives;

  renderAll();
}

function renderAll() {
  const ranking = buildRanking(currentAlternatives, criteria);

  const title = document.getElementById('summaryTitle');
  if (title) {
    title.textContent = `Total ${currentAlternatives.length} alternatif • ${criteria.length} kriteria`;
  }

  const summary = document.getElementById('summaryStats');
  if (summary) {
    const top = ranking[0];
    summary.innerHTML = [
      '<article class="stat-box"><strong>' + currentAlternatives.length + '</strong><span>Alternatif aktif</span></article>',
      '<article class="stat-box"><strong>' + criteria.length + '</strong><span>Kriteria ORESTE</span></article>',
      '<article class="stat-box"><strong>' + (top ? top.ranking : '-') + '</strong><span>Ranking terendah</span></article>',
      '<article class="stat-box"><strong>' + (top ? formatNumber(top.acumulasi) : '0.00') + '</strong><span>Akumulasi</span></article>',
    ].join('');
  }

  const table = document.getElementById('rankingTableBody');
  if (table) {
    table.innerHTML = ranking
      .map((item) => `
        <tr>
          <td><span class="rank-badge">${item.ranking}</span></td>
          <td>${item.nama}</td>
          <td>${formatNumber(item.acumulasi)}</td>
          <td>${item.c1}</td>
          <td>${item.c2}</td>
          <td>${item.c3}</td>
          <td>${item.c4}</td>
          <td>${item.c5}</td>
        </tr>
      `)
      .join('');
  }

  const list = document.getElementById('alternatifList');
  if (list) {
    list.innerHTML = currentAlternatives
      .map((item, index) => `
        <li class="chip-row">
          <span>${index + 1}. ${item.nama} — C1 ${item.c1} | C2 ${item.c2} | C3 ${item.c3} | C4 ${item.c4} | C5 ${item.c5}</span>
          <button type="button" data-delete="${item.nama}" class="btn btn-secondary small">Hapus</button>
        </li>
      `)
      .join('');
  }

  const status = document.getElementById('statusMessage');
  if (status) {
    status.textContent = 'Perhitungan otomatis akan dijalankan setiap kali data baru ditambahkan atau dihapus.';
    status.className = 'status-box';
  }
}

function handleAdd(event) {
  event.preventDefault();
  const form = event.currentTarget;
  const nama = document.getElementById('nama').value.trim();
  const values = ['c1', 'c2', 'c3', 'c4', 'c5'].map((key) => Number(document.getElementById(key).value));

  if (!nama) {
    document.getElementById('statusMessage').textContent = 'Nama alternatif wajib diisi.';
    return;
  }

  if (values.some((value) => Number.isNaN(value) || value < 0 || value > 100)) {
    document.getElementById('statusMessage').textContent = 'Nilai C1–C5 harus 0–100.';
    return;
  }

  const newItem = { nama, c1: values[0], c2: values[1], c3: values[2], c4: values[3], c5: values[4] };
  currentAlternatives = [...currentAlternatives, newItem];
  saveAlternatives(currentAlternatives);
  renderAll();
  form.reset();
  document.getElementById('statusMessage').textContent = `Data ${nama} ditambahkan. Nilai baru sudah dihitung otomatis.`;
}

function handleDelete(event) {
  const button = event.target.closest('[data-delete]');
  if (!button) return;
  const nama = button.getAttribute('data-delete');
  currentAlternatives = currentAlternatives.filter((item) => item.nama !== nama);
  saveAlternatives(currentAlternatives);
  renderAll();
  const status = document.getElementById('statusMessage');
  if (status) status.textContent = `Data ${nama} dihapus. Ranking otomatis diperbarui.`;
}

function fillSampleData() {
  document.getElementById('nama').value = 'Remaja 41 (Demo)';
  document.getElementById('c1').value = 88;
  document.getElementById('c2').value = 62;
  document.getElementById('c3').value = 74;
  document.getElementById('c4').value = 54;
  document.getElementById('c5').value = 68;
}

window.addEventListener('DOMContentLoaded', async () => {
  await loadData();

  const form = document.getElementById('addForm');
  if (form) form.addEventListener('submit', handleAdd);

  const sampleBtn = document.getElementById('sampleBtn');
  if (sampleBtn) sampleBtn.addEventListener('click', fillSampleData);

  document.addEventListener('click', handleDelete);

  const resetBtn = document.getElementById('resetBtn');
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      localStorage.removeItem(STORAGE_KEY);
      currentAlternatives = baseAlternatives;
      renderAll();
      document.getElementById('statusMessage').textContent = 'Data demo dikembalikan ke data awal.';
    });
  }
});
