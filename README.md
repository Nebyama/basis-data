# ORESTE GitHub Pages

Versi statis dari aplikasi ORESTE yang dapat dipublikasikan ke GitHub Pages tanpa PHP atau database.

## Fitur
- Tampilan statis berbasis HTML/CSS/JavaScript
- Input data baru dengan perhitungan otomatis
- Menggunakan data ORESTE yang sudah ada dari file orest_data.json

## Cara publish ke GitHub Pages
1. Commit semua file di folder ini.
2. Buka Settings > Pages di GitHub.
3. Pilih branch main/master, folder /root.
4. Simpan dan tunggu deploy selesai.

## Jalankan lokal
Gunakan server statis sederhana, misalnya:

python -m http.server 8000

Lalu buka http://localhost:8000/
