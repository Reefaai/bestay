# 🤝 Panduan Kontribusi

Terima kasih sudah tertarik untuk berkontribusi di **Bestay**! Berikut panduan agar proses kontribusi berjalan lancar.

---

## 📋 Sebelum Mulai

1. Pastikan kamu sudah membaca [README.md](README.md) dan berhasil menjalankan project secara lokal
2. Cek [Issues](../../issues) untuk melihat task yang tersedia atau bug yang perlu diperbaiki
3. Jika ingin menambah fitur baru, buat Issue terlebih dahulu untuk diskusi

---

## 🔧 Setup Development

```bash
# Clone fork kamu
git clone https://github.com/USERNAME/bestay.git
cd bestay

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Jalankan development server
composer dev
```

---

## 📝 Konvensi Commit

Gunakan format [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <deskripsi singkat>
```

### Tipe yang digunakan:

| Type | Deskripsi |
|------|-----------|
| `feat` | Fitur baru |
| `fix` | Perbaikan bug |
| `docs` | Perubahan dokumentasi |
| `style` | Formatting, semicolons, dll (bukan perubahan logic) |
| `refactor` | Refactoring code tanpa mengubah behavior |
| `test` | Menambah atau memperbaiki test |
| `chore` | Maintenance (dependencies, config, dll) |

### Contoh:

```bash
git commit -m "feat(payment): tambah retry mechanism untuk pembayaran gagal"
git commit -m "fix(booking): perbaiki validasi tanggal check-in"
git commit -m "docs: update API documentation di README"
```

---

## 🌿 Branching Strategy

- `main` — Branch production, selalu dalam keadaan stabil
- `feature/*` — Branch untuk fitur baru
- `fix/*` — Branch untuk perbaikan bug
- `docs/*` — Branch untuk perubahan dokumentasi

```bash
# Buat branch baru dari main
git checkout main
git pull origin main
git checkout -b feature/nama-fitur
```

---

## 🔄 Pull Request

### Checklist sebelum submit PR:

- [ ] Code sudah di-test secara lokal
- [ ] Tidak ada error dari `php artisan test`
- [ ] Code style konsisten (jalankan `composer pint` untuk auto-fix)
- [ ] Migrasi database berjalan tanpa error
- [ ] README/docs sudah di-update jika ada perubahan API atau fitur

### Format PR:

```markdown
## Deskripsi
Jelaskan perubahan yang dilakukan.

## Tipe Perubahan
- [ ] Bug fix
- [ ] Fitur baru
- [ ] Breaking change
- [ ] Dokumentasi

## Testing
Jelaskan bagaimana kamu menguji perubahan ini.

## Screenshots (jika ada perubahan UI)
```

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=NamaTest

# Code style check & auto-fix
./vendor/bin/pint
```

### Menulis Test

- Letakkan test di folder `tests/Feature/` atau `tests/Unit/`
- Gunakan naming convention: `NamaFiturTest.php`
- Setiap fitur baru **wajib** disertai test

---

## 🎨 Code Style

Project ini menggunakan [Laravel Pint](https://laravel.com/docs/pint) untuk code formatting:

```bash
# Check code style
./vendor/bin/pint --test

# Auto-fix code style
./vendor/bin/pint
```

### Aturan umum:

- Gunakan **PSR-12** coding standard
- Gunakan **type hints** di parameter dan return type
- Gunakan **Form Request** untuk validasi (bukan validasi di controller)
- Business logic di **Service layer** (`app/Services/`), bukan di controller
- Gunakan **Policy** untuk authorization

---

## 📁 Arsitektur

```
Controller → Service → Model
     ↓
Form Request (validasi)
     ↓
Policy (authorization)
```

- **Controller**: Handle HTTP request/response, delegasi ke Service
- **Service**: Business logic, orchestration
- **Model**: Data access, relationships, scopes
- **Form Request**: Input validation rules
- **Policy**: Authorization rules (who can do what)

---

## ❓ Butuh Bantuan?

Jika ada pertanyaan, silakan buat [Issue](../../issues) dengan label `question`.

---

Terima kasih sudah berkontribusi! 🙏
