# System Design Document: Sistem Informasi Inventaris & Monitoring Fasilitas Ruangan Puskesmas

Dokumen ini menjelaskan arsitektur, desain basis data, pola keamanan, serta alur bisnis yang diterapkan dalam pengembangan Sistem Informasi Inventaris internal Puskesmas.

---

## 🧠 1. Arsitektur Sistem (High-Level Architecture)

Aplikasi ini dibangun menggunakan framework **Laravel 11** dengan menerapkan pola arsitektur **MVC (Model-View-Controller)**. Sistem ini bersifat tertutup (Internal-only) dan menggunakan **RBAC (Role-Based Access Control)** untuk membatasi hak akses user.

* **Presentation Layer**: Menggunakan Laravel Blade Engine yang dikombinasikan dengan Tailwind CSS untuk antarmuka dashboard yang responsif dan modern.
* **Business Logic Layer**: Diatur oleh Controllers dengan pemisahan validasi menggunakan Laravel Form Requests.
* **Data Access Layer**: Menggunakan Eloquent ORM untuk abstraksi database MySQL.

---

## 🗄️ 2. Desain Basis Data (Database Schema)

Sistem menggunakan database relasional MySQL dengan struktur tabel yang dinormalisasi untuk menjaga integritas data aset dan riwayat transaksi peminjaman.

### Data Dictionary & Relasi Tabel

#### 1. Tabel: `roles`
Menyimpan level hak akses dalam sistem.
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID unik role |
| `name` | String | Unique | Identifikasi sistem (`admin`, `petugas`) |
| `display_name` | String | - | Nama yang muncul di UI (e.g., Administrator) |

#### 2. Tabel: `rooms`
Menyimpan data 20 ruangan fasilitas Puskesmas.
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID unik ruangan |
| `name` | String | Unique | Nama ruangan (e.g., IGD, Poli Gigi) |
| `user_id` | BigInt | FK, Nullable | Menunjuk ke `users.id` (Penanggung Jawab) |
| `location_floor`| String | Default: '1' | Lokasi lantai ruangan |

#### 3. Tabel: `users`
Menyimpan data pengguna internal sistem.
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID unik user |
| `role_id` | BigInt | FK | Menunjuk ke `roles.id` |
| `room_id` | BigInt | FK, Nullable | Menunjuk ke `rooms.id` (Kamar tugas petugas) |
| `name` | String | - | Nama lengkap pegawai |
| `email` | String | Unique | Email internal login |
| `password` | String | - | Hashed password |

#### 4. Tabel: `items`
Menyimpan status terkini dari setiap barang/fasilitas.
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID unik barang |
| `item_code` | String | Unique | Kode inventaris otomatis (e.g., INV-ALM-2931) |
| `name` | String | - | Nama fasilitas/alat |
| `category` | String | - | Kategori (Alat Medis, Elektronik, Mebel) |
| `room_id` | BigInt | FK | Menunjuk ke `rooms.id` (Lokasi barang) |
| `qty` | Integer | Min: 0 | Jumlah stok barang di ruangan tersebut |
| `status` | Enum | - | `Baik`, `Rusak`, `Tidak Tersedia`, `Digunakan` |

#### 5. Tabel: `item_logs`
*Audit Trail* untuk memonitor perubahan kondisi barang secara real-time.
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID unik log |
| `item_id` | BigInt | FK | Menunjuk ke `items.id` (Cascade delete) |
| `user_id` | BigInt | FK | Menunjuk ke `users.id` (Aktor pengubah) |
| `action` | String | - | Jenis aktivitas (e.g., "Update Status Ke Rusak") |
| `old_values` | Text | Nullable | State JSON data sebelum diubah |
| `new_values` | Text | Nullable | State JSON data sesudah diubah |

#### 6. Tabel: `borrowings`
Menyimpan master data transaksi peminjaman antar-ruangan.
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID transaksi |
| `borrow_code` | String | Unique | Kode peminjaman (e.g., REQ-1698213) |
| `requester_id`| BigInt | FK | Menunjuk ke `users.id` (Petugas pemohon) |
| `from_room_id`| BigInt | FK | Menunjuk ke `rooms.id` (Ruangan asal barang) |
| `to_room_id` | BigInt | FK | Menunjuk ke `rooms.id` (Ruangan tujuan) |
| `borrow_date` | Date | - | Tanggal peminjaman |
| `return_date` | Date | Nullable | Tanggal pengembalian aktual |
| `status` | Enum | Default: 'Pending'| `Pending`, `Approved`, `Rejected`, `Returned` |
| `approved_by` | BigInt | FK, Nullable | Menunjuk ke `users.id` (Admin penindak) |
| `notes` | Text | Nullable | Alasan peminjaman / keterangan |

#### 7. Tabel: `borrowing_details`
Tabel pivot pendukung untuk menyimpan item apa saja yang dipinjam dalam satu transaksi (*Many-to-Many Bridge*).
| Field | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID detail |
| `borrowing_id`| BigInt | FK | Menunjuk ke `borrowings.id` (Cascade) |
| `item_id` | BigInt | FK | Menunjuk ke `items.id` |
| `qty` | Integer | Min: 1 | Jumlah barang yang dipinjam |

---

## 👥 3. Matriks Hak Akses (Role-Based Access Control)

Sistem memisahkan *privilege* secara tegas menggunakan custom Middleware (`RoleMiddleware`) di level routing backend:

| Fitur / Modul | Admin | Petugas Ruangan | Keterangan |
| :--- | :---: | :---: | :--- |
| **Dashboard Statistics** |  |  | Semua bisa melihat, namun angka di-scope berdasarkan hak akses. |
| **CRUD Master Ruangan** |  | ❌ | Hanya Admin yang bisa menambah/mengubah daftar 20 ruangan & PJ. |
| **Lihat Semua Inventaris**|  | ❌ | Petugas hanya bisa melihat inventaris di ruangannya sendiri. |
| **Tambah/Edit Barang** |  |  | Petugas terbatas pada ruangan tugasnya; Admin bebas di semua ruang. |
| **Request Peminjaman** | ❌ |  | Diinisiasi oleh petugas ruangan yang membutuhkan alat tambahan. |
| **Approval Peminjaman** |  | ❌ | Otoritas penuh ada di Admin untuk menyetujui/menolak mutasi barang. |
| **Export Laporan (CSV)** |  |  | Admin mengeksport semua data; Petugas mengeksport ruangannya sendiri. |

---

## 🔄 4. Alur Bisnis Inti (Core Workflows)

### A. Alur Pembaruan Kondisi Barang (Real-Time Monitoring)
1. Petugas Ruangan memeriksa fisik fasilitas di ruangannya (misal: AC Split di Poli Gigi rusak).
2. Petugas masuk ke menu **Inventaris Barang**, memilih AC Split, lalu menekan tombol **Edit Status**.
3. Status diubah dari `Baik` menjadi `Rusak`.
4. Sistem menjalankan `DB Transaction`:
   * Mengupdate field `status` pada tabel `items`.
   * Menulis rekaman baru ke tabel `item_logs` yang berisi *snapshot* perubahan data.
5. Halaman Dashboard Admin langsung merefleksikan penambahan angka pada metrik **"Barang Rusak"** secara akurat via visual bar.

### B. Alur Peminjaman Barang Antar Ruangan
1. Petugas Poli KIA membutuhkan tambahan *Tensimeter Digital* dan melihat bahwa Gudang Logistik memilikinya dengan kondisi `Baik`.
2. Petugas Poli KIA membuat **Request Peminjaman** baru dengan memilih barang tersebut dan menentukan kuantitasnya.
3. Status transaksi berstatus `Pending`. Stok barang di ruangan asal belum berkurang.
4. Admin menerima notifikasi di dashboard, lalu meninjau permohonan tersebut:
   * **Jika Di-reject**: Status berubah menjadi `Rejected`, selesai.
   * **Jika Di-approve**: Status berubah menjadi `Approved`. Sistem memotong jumlah (`qty`) barang di ruangan asal secara otomatis, lalu menambah/membuat record `qty` baru di ruangan tujuan dengan status status terkait (`Digunakan`).

---

## 🔐 5. Strategi Keamanan & Validasi Data

Untuk memastikan aplikasi aman dari serangan siber umum dan menjaga keaslian data internal, teknik perlindungan berikut wajib diimplementasikan:

1.  **Authentication & Session Guard**: Seluruh rute aplikasi (kecuali halaman login) dibungkus oleh middleware `auth` untuk mencegah *unauthenticated users* mengintip endpoint internal.
2.  **Cross-Site Request Forgery (CSRF)**: Semua form input (*POST/PUT/DELETE*) wajib menyertakan direktif `@csrf` token bawaan Laravel untuk mencegah eksploitasi session dari luar aplikasi.
3.  **Data Validation & Sanitization**: 
    * Validasi ketat di sisi server via Eloquent Controller (`required`, `integer`, `exists`, `in:enum_fields`).
    * Mencegah *SQL Injection* dengan menggunakan *Parameterized Queries* bawaan Eloquent ORM secara default.
4.  **Cross-Site Scripting (XSS) Protection**: Seluruh output variabel pada Blade View menggunakan kurung kurawal ganda `{{ $variable }}` yang secara otomatis menerapkan fungsi `htmlspecialchars` PHP untuk menetralkan script berbahaya.