const STORAGE_KEY = 'oreste-static-demo';

const state = {
  original: [],
  criteria: [],
  alternatives: [],
  results: [],
  currentMode: 'ranking-low',
};

function formatNumber(value) {
  return Number(value).toFixed(2);
}

function sortAlternativesByValue(items, key) {
  return [...items].sort((a, b) => Number(b[key]) - Number(a[key]));
}

function buildRanking(alternatives, criteria) {
  const totalAlternatives = alternatives.length;
  const results = alternatives.map((item) => {
    const detail = [];
    let acumulasi = 0;

    criteria.forEach((criterion, index) => {
      const sorted = sortAlternativesByValue(alternatives, criterion.key);
      const rawRank = sorted.findIndex((entry) => entry.nama === item.nama) + 1;
      const bessonRank = rawRank;
      const normalisasi = rawRank / totalAlternatives;
      const rc = Number(criterion.rank_bobot || index + 1);
      const distance = Math.pow((0.5 * Math.pow(rc, 3) + 0.5 * Math.pow(bessonRank, 3)), 1 / 3);

      acumulasi += Number(criterion.bobot || 0) * distance;
      detail.push({
        key: criterion.key,
        label: criterion.nama,
        nilai: Number(item[criterion.key]),
        bessonRank,
        normalisasi,
        distance,
      });
    });

    return {
      nama: item.nama,
      acumulasi,
      detail,
      raw: item,
    };
  });

  results.sort((a, b) => a.acumulasi - b.acumulasi);
  results.forEach((item, index) => {
    item.ranking = index + 1;
  });

  return results;
}

function renderSummary(data) {
  const title = document.getElementById('summaryTitle');
  const stats = document.getElementById('summaryStats');
  const text = document.getElementById('summaryText');

  const totalAlternatif = data.alternatives.length;
  const totalKriteria = data.criteria.length;
  const top = data.results[0];

  title.textContent = `Total ${totalAlternatif} alternatif • ${totalKriteria} kriteria`;
  stats.innerHTML = [
    '<article class="stat-box"><strong>' + totalAlternatif + '</strong><span>Alternatif aktif</span></article>',
    '<article class="stat-box"><strong>' + totalKriteria + '</strong><span>Kriteria ORESTE</span></article>',
    '<article class="stat-box"><strong>' + (top ? top.ranking : '-') + '</strong><span>Ranking awal</span></article>',
    '<article class="stat-box"><strong>' + (top ? formatNumber(top.acumulasi) : '0.00') + '</strong><span>Akumulasi terendah</span></article>',
  ].join('');

  text.textContent = top
    ? `Alternatif paling menonjol saat ini: ${top.nama}. Nilai dihitung ulang otomatis setiap kali ada data baru.`
    : 'Belum ada data. Tambahkan alternatif untuk melihat hasil otomatis.';
}

function sortResults(results, mode = state.currentMode) {
  const items = [...results];

  switch (mode) {
    case 'ranking-high':
      return items.sort((a, b) => b.ranking - a.ranking);
    case 'c1-low':
      return items.sort((a, b) => Number(a.raw.c1) - Number(b.raw.c1) || a.ranking - b.ranking);
    case 'c1-high':
      return items.sort((a, b) => Number(b.raw.c1) - Number(a.raw.c1) || a.ranking - b.ranking);
    case 'c2-low':
      return items.sort((a, b) => Number(a.raw.c2) - Number(b.raw.c2) || a.ranking - b.ranking);
    case 'c2-high':
      return items.sort((a, b) => Number(b.raw.c2) - Number(a.raw.c2) || a.ranking - b.ranking);
    case 'c3-low':
      return items.sort((a, b) => Number(a.raw.c3) - Number(b.raw.c3) || a.ranking - b.ranking);
    case 'c3-high':
      return items.sort((a, b) => Number(b.raw.c3) - Number(a.raw.c3) || a.ranking - b.ranking);
    case 'c4-low':
      return items.sort((a, b) => Number(a.raw.c4) - Number(b.raw.c4) || a.ranking - b.ranking);
    case 'c4-high':
      return items.sort((a, b) => Number(b.raw.c4) - Number(a.raw.c4) || a.ranking - b.ranking);
    case 'c5-low':
      return items.sort((a, b) => Number(a.raw.c5) - Number(b.raw.c5) || a.ranking - b.ranking);
    case 'c5-high':
      return items.sort((a, b) => Number(b.raw.c5) - Number(a.raw.c5) || a.ranking - b.ranking);
    case 'ranking-low':
    default:
      return items.sort((a, b) => a.ranking - b.ranking);
  }
}

function renderRankingTable(results, targetId = 'rankingTableBody') {
  const body = document.getElementById(targetId);
  if (!body) return;

  if (!results.length) {
    body.innerHTML = '<tr><td colspan="8" class="muted">Belum ada data.</td></tr>';
    return;
  }

  body.innerHTML = results.map((item) => {
    const values = item.raw;
    return `
      <tr>
        <td><span class="rank-badge">${item.ranking}</span></td>
        <td>${item.nama}</td>
        <td>${formatNumber(item.acumulasi)}</td>
        <td>${values.c1}</td>
        <td>${values.c2}</td>
        <td>${values.c3}</td>
        <td>${values.c4}</td>
        <td>${values.c5}</td>
      </tr>`;
  }).join('');
}

function refreshRankingViews(results) {
  const dashboardFilter = document.getElementById('dashboardFilter');
  const homeFilter = document.getElementById('rankingFilter');

  if (dashboardFilter) {
    const mode = dashboardFilter.value;
    renderRankingTable(sortResults(results, mode), 'rankingTableBody');
  }

  if (homeFilter) {
    const mode = homeFilter.value;
    renderRankingTable(sortResults(results, mode), 'homeRankingTableBody');
  }
}

function showStatus(message, type = 'success') {
  const el = document.getElementById('statusMessage');
  el.className = `status-box ${type}`;
  el.textContent = message;
}

function saveDemoData(data) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function loadStoredData() {
  const raw = localStorage.getItem(STORAGE_KEY);
  return raw ? JSON.parse(raw) : null;
}

function applyInputData(baseAlternatives, baseCriteria) {
  const stored = loadStoredData();
  const allAlternatives = stored ? stored.alternatives : baseAlternatives;
  const criteria = stored ? stored.criteria : baseCriteria;
  const results = buildRanking(allAlternatives, criteria);

  state.original = baseAlternatives;
  state.criteria = criteria;
  state.alternatives = allAlternatives;

  renderSummary({ alternatives: allAlternatives, criteria, results });
  renderRankingTable(results);
}

async function loadData() {
  try {
    const response = await fetch('oreste_data.json');
    if (!response.ok) throw new Error('Gagal memuat data JSON.');

    const data = await response.json();
    const baseAlternatives = [...data.alternatif];
    const criteria = data.bobot.map((item) => ({
      nama: item.nama,
      key: `c${item.no}`,
      bobot: Number(item.bobot),
      rank_bobot: Number(item.rank_bobot),
    }));

    state.original = baseAlternatives;
    state.criteria = criteria;
    state.alternatives = baseAlternatives;

    const stored = loadStoredData();
    if (stored && Array.isArray(stored.alternatives) && stored.alternatives.length > 0) {
      state.alternatives = stored.alternatives;
      showStatus('Data tambahan berhasil dimuat dari browser Anda.', 'success');
    }

    state.results = buildRanking(state.alternatives, criteria);
    const results = state.results;

    renderSummary({ alternatives: state.alternatives, criteria, results });
    refreshRankingViews(results);
  } catch (error) {
    console.error(error);
    showStatus('Tidak dapat memuat data ORESTE. Pastikan file orest_data.json ada di repositori.', 'error');
  }
}

function addAlternative(event) {
  event.preventDefault();

  const form = event.currentTarget;
  const nama = document.getElementById('nama').value.trim();
  const values = ['c1', 'c2', 'c3', 'c4', 'c5'].map((key) => Number(document.getElementById(key).value));

  if (!nama) {
    showStatus('Nama alternatif wajib diisi.', 'error');
    return;
  }

  if (values.some((value) => Number.isNaN(value) || value < 0 || value > 100)) {
    showStatus('Nilai C1–C5 harus berupa angka 0–100.', 'error');
    return;
  }

  const newItem = { nama, ...Object.fromEntries(['c1', 'c2', 'c3', 'c4', 'c5'].map((key, idx) => [key, values[idx]])) };

  const nextAlternatives = [...state.alternatives, newItem];
  saveDemoData({ alternatives: nextAlternatives, criteria: state.criteria });
  state.alternatives = nextAlternatives;

  const results = buildRanking(nextAlternatives, state.criteria);
  state.results = results;

  renderSummary({ alternatives: nextAlternatives, criteria: state.criteria, results });
  refreshRankingViews(results);

  showStatus(`Data ${nama} berhasil ditambahkan. Sistem menghitung ulang secara otomatis.`, 'success');
  form.reset();
}

function fillSampleData() {
  document.getElementById('nama').value = 'Remaja 41 (Demo)';
  document.getElementById('c1').value = 88;
  document.getElementById('c2').value = 62;
  document.getElementById('c3').value = 74;
  document.getElementById('c4').value = 54;
  document.getElementById('c5').value = 68;
}

function resetDemo() {
  localStorage.removeItem(STORAGE_KEY);
  state.alternatives = state.original;
  const results = buildRanking(state.alternatives, state.criteria);
  state.results = results;

  renderSummary({ alternatives: state.alternatives, criteria: state.criteria, results });
  refreshRankingViews(results);

  showStatus('Data demo berhasil dikembalikan ke data awal.', 'success');
}

window.addEventListener('DOMContentLoaded', () => {
  const addForm = document.getElementById('addForm');
  if (addForm) addForm.addEventListener('submit', addAlternative);

  const sampleBtn = document.getElementById('sampleBtn');
  if (sampleBtn) sampleBtn.addEventListener('click', fillSampleData);

  const resetBtn = document.getElementById('resetBtn');
  if (resetBtn) resetBtn.addEventListener('click', resetDemo);

  const dashboardFilter = document.getElementById('dashboardFilter');
  if (dashboardFilter) {
    dashboardFilter.addEventListener('change', (event) => {
      state.currentMode = event.target.value;
      if (state.results.length) {
        renderRankingTable(sortResults(state.results, state.currentMode), 'rankingTableBody');
      }
    });
  }

  const filter = document.getElementById('rankingFilter');
  if (filter) {
    filter.addEventListener('change', (event) => {
      state.currentMode = event.target.value;
      if (state.results.length) {
        renderRankingTable(sortResults(state.results, state.currentMode), 'homeRankingTableBody');
      }
    });
  }

  loadData();
});
