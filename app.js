const STORAGE_KEY = 'oreste-static-demo';

const state = {
  original: [],
  criteria: [],
  alternatives: [],
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

function renderRankingTable(results) {
  const body = document.getElementById('rankingTableBody');
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

    const results = buildRanking(state.alternatives, criteria);
    renderSummary({ alternatives: state.alternatives, criteria, results });
    renderRankingTable(results);
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
  renderSummary({ alternatives: nextAlternatives, criteria: state.criteria, results });
  renderRankingTable(results);
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
  renderSummary({ alternatives: state.alternatives, criteria: state.criteria, results });
  renderRankingTable(results);
  showStatus('Data demo berhasil dikembalikan ke data awal.', 'success');
}

window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('addForm').addEventListener('submit', addAlternative);
  document.getElementById('sampleBtn').addEventListener('click', fillSampleData);
  document.getElementById('resetBtn').addEventListener('click', resetDemo);
  loadData();
});
