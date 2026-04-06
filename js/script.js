// js/script.js

// ---- Toggle tab Login / Register ----
function showTab(tab) {
    document.getElementById('form-login').style.display    = (tab === 'login')    ? 'block' : 'none';
    document.getElementById('form-register').style.display = (tab === 'register') ? 'block' : 'none';
    document.querySelectorAll('.login-tabs button').forEach(b => b.classList.remove('active'));
    document.querySelector(`.login-tabs button[onclick="showTab('${tab}')"]`).classList.add('active');
}

// ---- Konfirmasi hapus ----
function konfirmasiHapus(url, nama) {
    if (confirm('Yakin ingin menghapus "' + nama + '"? Tindakan ini tidak bisa dibatalkan.')) {
        window.location.href = url;
    }
}

// ---- Update total keranjang (real-time) ----
function updateTotal() {
    var rows  = document.querySelectorAll('.cart-row');
    var total = 0;
    rows.forEach(function(row) {
        var harga  = parseFloat(row.dataset.harga) || 0;
        var jumlah = parseInt(row.querySelector('.qty-input').value) || 0;
        var sub    = harga * jumlah;
        row.querySelector('.subtotal').textContent = formatRupiah(sub);
        total += sub;
    });
    var el = document.getElementById('grand-total');
    if (el) el.textContent = formatRupiah(total);
}

function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

// ---- Auto-hide alert ----
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(a) {
        a.style.transition = 'opacity 0.5s';
        a.style.opacity = '0';
        setTimeout(function() { a.remove(); }, 500);
    });
}, 3500);

// ---- Validasi form tambah/edit produk ----
function validasiFormProduk() {
    var nama  = document.getElementById('nama_produk');
    var harga = document.getElementById('harga');
    var stok  = document.getElementById('stok');
    if (!nama || nama.value.trim() === '') {
        alert('Nama produk tidak boleh kosong!'); return false;
    }
    if (!harga || parseFloat(harga.value) <= 0) {
        alert('Harga harus lebih dari 0!'); return false;
    }
    if (!stok || parseInt(stok.value) < 0) {
        alert('Stok tidak boleh negatif!'); return false;
    }
    return true;
}

// ---- Preview gambar sebelum upload ----
function previewGambar(input) {
    var preview = document.getElementById('img-preview');
    if (!preview) return;
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
