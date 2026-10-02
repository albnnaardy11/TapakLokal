<?php

namespace App\Services;

class HelpCenterService
{
    /**
     * Get all product categories for the Help Center.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCategories(): array
    {
        return [
            [
                'id' => 'general-info',
                'slug' => 'general-info',
                'aliases' => ['informasi-umum', 'general', 'info-umum'],
                'name' => 'Informasi Umum',
                'shortName' => 'Informasi Umum',
                'icon' => 'Info',
                'colorClass' => 'bg-cyan-50 text-cyan-600 border-cyan-100 group-hover:bg-cyan-600 group-hover:text-white',
                'activeClass' => 'bg-cyan-600 text-white ring-4 ring-cyan-100 shadow-md',
                'badge' => 'Panduan Dasar',
                'description' => 'Pertanyaan umum seputar layanan TapakLokal, cara pemesanan e-tiket, asuransi, dan kebijakan platform.',
            ],
            [
                'id' => 'open-trip',
                'slug' => 'open-trip',
                'aliases' => ['trip-gabungan', 'open'],
                'name' => 'Open Trip',
                'shortName' => 'Open Trip',
                'icon' => 'TentTree',
                'colorClass' => 'bg-sky-50 text-[#0088ff] border-sky-100 group-hover:bg-[#0088ff] group-hover:text-white',
                'activeClass' => 'bg-[#0088ff] text-white ring-4 ring-sky-100 shadow-md',
                'badge' => 'Trip Gabungan',
                'description' => 'Panduan kuota minimum peserta, solo traveler, pembagian kamar, dan titik kumpul meeting point.',
            ],
            [
                'id' => 'private-trip',
                'slug' => 'private-trip',
                'aliases' => ['trip-eksklusif', 'private'],
                'name' => 'Private Trip',
                'shortName' => 'Private Trip',
                'icon' => 'Crown',
                'colorClass' => 'bg-emerald-50 text-emerald-600 border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white',
                'activeClass' => 'bg-emerald-600 text-white ring-4 ring-emerald-100 shadow-md',
                'badge' => 'Trip Eksklusif',
                'description' => 'Panduan kustomisasi rute, tanggal bebas, kapasitas rombongan, dan penjemputan door-to-door.',
            ],
            [
                'id' => 'culinary-po',
                'slug' => 'culinary-po',
                'aliases' => ['po-oleh-oleh', 'oleh-oleh', 'kuliner-lokal'],
                'name' => 'PO Oleh-Oleh & Kuliner',
                'shortName' => 'PO Oleh-Oleh',
                'icon' => 'Utensils',
                'colorClass' => 'bg-amber-50 text-amber-600 border-amber-100 group-hover:bg-amber-500 group-hover:text-white',
                'activeClass' => 'bg-amber-500 text-white ring-4 ring-amber-100 shadow-md',
                'badge' => 'Kuliner Daerah',
                'description' => 'Sistem titip beli kuliner legendaris, jaminan kesegaran vacuum pack, kurir kilat, dan garansi retur.',
            ],
            [
                'id' => 'payment-methods',
                'slug' => 'payment-methods',
                'aliases' => ['metode-pembayaran', 'payment', 'pembayaran'],
                'name' => 'Metode Pembayaran',
                'shortName' => 'Metode Pembayaran',
                'icon' => 'CreditCard',
                'colorClass' => 'bg-violet-50 text-violet-600 border-violet-100 group-hover:bg-violet-600 group-hover:text-white',
                'activeClass' => 'bg-violet-600 text-white ring-4 ring-violet-100 shadow-md',
                'badge' => 'QRIS & Transfer',
                'description' => 'Metode pembayaran QRIS, Virtual Account bank, minimarket, kendala verifikasi, dan voucher promo.',
            ],
            [
                'id' => 'wallet',
                'slug' => 'wallet',
                'aliases' => ['tapakwallet', 'saldo-refund'],
                'name' => 'TapakWallet & Refund',
                'shortName' => 'TapakWallet',
                'icon' => 'Wallet',
                'colorClass' => 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white',
                'activeClass' => 'bg-indigo-600 text-white ring-4 ring-indigo-100 shadow-md',
                'badge' => 'Transaksi Aman',
                'description' => 'Status fitur saldo akun, cara mengajukan refund, dan memantau pengembalian dana.',
            ],
            [
                'id' => 'profile-security',
                'slug' => 'profile-security',
                'aliases' => ['akun-kyc', 'profil-keamanan', 'profile'],
                'name' => 'Profil & Keamanan KYC',
                'shortName' => 'Akun & KYC',
                'icon' => 'UserCheck',
                'colorClass' => 'bg-blue-50 text-blue-600 border-blue-100 group-hover:bg-blue-600 group-hover:text-white',
                'activeClass' => 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md',
                'badge' => 'Data Wisatawan',
                'description' => 'Verifikasi identitas KTP/Paspor untuk polis asuransi dan izin masuk kawasan konservasi alam.',
            ],
            [
                'id' => 'anti-pungli',
                'slug' => 'anti-pungli',
                'aliases' => ['lapor-pungli', 'transparansi-harga'],
                'name' => 'Lapor Pungli & Standar',
                'shortName' => 'Lapor Pungli',
                'icon' => 'ShieldAlert',
                'colorClass' => 'bg-rose-50 text-rose-600 border-rose-100 group-hover:bg-rose-600 group-hover:text-white',
                'activeClass' => 'bg-rose-600 text-white ring-4 ring-rose-100 shadow-md',
                'badge' => 'Anti Biaya Siluman',
                'description' => 'Standar harga All-In transparan tanpa biaya siluman dan kanal pelaporan cepat pungutan liar.',
            ],
            [
                'id' => 'partners-guide',
                'slug' => 'partners-guide',
                'aliases' => ['mitra-guide', 'mitra-pemandu', 'partners'],
                'name' => 'Mitra & Pemandu Lokal',
                'shortName' => 'Mitra & Guide',
                'icon' => 'UsersRound',
                'colorClass' => 'bg-teal-50 text-teal-600 border-teal-100 group-hover:bg-teal-600 group-hover:text-white',
                'activeClass' => 'bg-teal-600 text-white ring-4 ring-teal-100 shadow-md',
                'badge' => 'Lisensi HPI',
                'description' => 'Standar legalitas operator trip, sertifikasi pemandu wisata HPI/BNSP, dan kelaikan armada pariwisata.',
            ],
            [
                'id' => 'all',
                'slug' => 'all',
                'aliases' => ['semua-kategori', 'semua-produk'],
                'name' => 'Semua Kategori',
                'shortName' => 'Semua Produk',
                'icon' => 'LayoutGrid',
                'colorClass' => 'bg-slate-100 text-slate-700 border-slate-200 group-hover:bg-slate-800 group-hover:text-white',
                'activeClass' => 'bg-slate-800 text-white ring-4 ring-slate-200 shadow-md',
                'badge' => 'Pusat Bantuan',
                'description' => 'Eksplorasi seluruh panduan dan pertanyaan umum mengenai semua produk TapakLokal.',
            ],
        ];
    }

    /**
     * Find category by slug or alias.
     *
     * @return array<string, mixed>|null
     */
    public function getCategory(string $slug): ?array
    {
        $categories = $this->getCategories();
        foreach ($categories as $cat) {
            if ($cat['slug'] === $slug || $cat['id'] === $slug || in_array($slug, $cat['aliases'] ?? [])) {
                return $cat;
            }
        }

        return null;
    }

    /**
     * Get all structured articles.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllArticles(): array
    {
        return [
            // 0. INFORMASI UMUM (GENERAL INFO)
            [
                'id' => 'general-about-platform',
                'slug' => 'apa-itu-platform-tapaklokal-dan-keunggulannya',
                'category' => 'general-info',
                'categoryLabel' => 'Informasi Umum',
                'subcategory' => 'Kebijakan Platform',
                'title' => 'Apa itu platform TapakLokal dan apa saja layanan utamanya?',
                'summary' => 'Platform all-in-one penjelajahan wisata alam lokal terkurasi (Open & Private Trip) dan titip beli oleh-oleh khas UMKM.',
                'content' => "### Tentang TapakLokal\n\n**TapakLokal** adalah platform ekosistem pariwisata lokal nomor 1 di Indonesia yang menghubungkan wisatawan dengan pemandu wisata lokal berlisensi dan pelaku UMKM daerah secara aman, transparan, dan terstandar.\n\n### 3 Layanan Utama TapakLokal:\n1. **Open Trip**: Perjalanan alam gabungan ekonomis & ramah solo traveler dengan jadwal rutin ke destinasi eksotis Indonesia.\n2. **Private Trip**: Paket liburan privat eksklusif untuk keluarga, rombongan teman, atau instansi dengan armada terstandar dan jadwal fleksibel.\n3. **Open PO Oleh-Oleh & Kuliner Khas**: Layanan jastip resmi produk UMKM khas daerah langsung dari pengrajin & produsen terpercaya saat Anda berwisata.\n\n### Standar Keamanan & Kepercayaan TapakLokal:\n- **Harga All-In Transparan**: Bebas dari pungutan liar (pungli) dan biaya tersembunyi.\n- **Pemandu Berlisensi Resmi**: Semua mitra tour leader dan guide telah terdaftar dan memiliki lisensi HPI / sertifikasi BNSP.\n- **Proteksi Asuransi Resmi**: Setiap tiket perjalanan otomatis dilindungi oleh asuransi keselamatan jiwa resmi.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['informasi umum', 'tapaklokal', 'tentang', 'layanan', 'keunggulan', 'panduan'],
            ],
            [
                'id' => 'general-how-to-book',
                'slug' => 'cara-pemesanan-trip-dan-e-tiket-tapaklokal',
                'category' => 'general-info',
                'categoryLabel' => 'Informasi Umum',
                'subcategory' => 'Pemesanan & E-Tiket',
                'title' => 'Bagaimana cara melakukan pemesanan trip dan mendapatkan E-Tiket di TapakLokal?',
                'summary' => 'Panduan langkah mudah mulai dari memilih destinasi, tanggal, pengisian data manifest KTP, hingga konfirmasi e-tiket instan.',
                'content' => "Proses pemesanan di TapakLokal dirancang sangat praktis dan aman:\n\n1. **Pilih Destinasi & Kategori Trip**:\n   Cari paket Open Trip atau Private Trip yang Anda inginkan melalui menu pencarian atau halaman katalog.\n2. **Tentukan Tanggal & Jumlah Peserta**:\n   Pilih tanggal keberangkatan yang tersedia dan tentukan titik kumpul (*Meeting Point*) yang Anda inginkan.\n3. **Lengkapi Data Manifest Peserta (KYC)**:\n   Isi nama lengkap sesuai KTP/Paspor, nomor NIK, dan nomor WhatsApp aktif untuk keperluan asuransi dan izin masuk kawasan konservasi.\n4. **Pilih Metode Pembayaran**:\n   Selesaikan pembayaran menggunakan QRIS, Virtual Account Bank, TapakWallet, atau gerai minimarket sebelum batas waktu habis.\n5. **E-Tiket & Grup Koordinasi**:\n   Setelah pembayaran terverifikasi otomatis, E-Tiket resmi dapat diunduh di menu **Pesanan Saya**, dan Tour Leader akan mengundang Anda ke grup koordinasi WhatsApp H-1 keberangkatan.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['cara pesan', 'pemesanan', 'booking', 'tiket', 'e-tiket', 'panduan'],
            ],
            [
                'id' => 'general-travel-insurance',
                'slug' => 'apakah-setiap-trip-dilindungi-asuransi-perjalanan',
                'category' => 'general-info',
                'categoryLabel' => 'Informasi Umum',
                'subcategory' => 'Asuransi & Proteksi',
                'title' => 'Apakah setiap perjalanan di TapakLokal dilindungi oleh asuransi perjalanan?',
                'summary' => 'Ya! Semua paket Open Trip dan Private Trip di TapakLokal sudah mencakup perlindungan asuransi keselamatan jiwa.',
                'content' => "Keselamatan dan kenyamanan Anda adalah prioritas mutlak kami.\n\n### Ketentuan Perlindungan Asuransi:\n- **Otomatis Aktif**: Polis asuransi otomatis aktif sejak waktu keberangkatan di titik kumpul hingga trip selesai.\n- **Cakupan Proteksi**: Meliputi biaya pertolongan pertama darurat, santunan perawatan medis kecelakaan, hingga evakuasi darurat di medan alam bebas.\n- **Syarat Validitas**: Pastikan data nama dan NIK yang Anda isi saat pemesanan sesuai dengan identitas resmi KTP/Paspor Anda agar proses klaim valid dan tidak terkendala.",
                'isPopular' => false,
                'updatedAt' => '2026-09-27',
                'tags' => ['asuransi', 'keselamatan', 'proteksi', 'klaim', 'jaminan'],
            ],

            // 1. OPEN TRIP
            [
                'id' => 'open-trip-quota',
                'slug' => 'ketentuan-kuota-minimum-open-trip-dan-kebijakan-refund',
                'category' => 'open-trip',
                'categoryLabel' => 'Open Trip',
                'subcategory' => 'Ketentuan Kuota & Jadwal',
                'title' => 'Bagaimana jika kuota minimum peserta Open Trip tidak terpenuhi hingga batas waktu?',
                'summary' => 'Garansi 100% uang kembali (Full Refund) atau pilihan reschedule & penyesuaian rombongan kecil tanpa ribet.',
                'content' => "Setiap paket **Open Trip** di TapakLokal memiliki kuota minimum peserta (biasanya 5–10 orang) agar perjalanan dapat berlangsung optimal dan ekonomis.\n\nJika hingga batas waktu konfirmasi (**H-3 sebelum keberangkatan**) kuota minimum belum tercapai, sistem TapakLokal dan mitra operator resmi akan memberikan **3 Solusi Terbaik**:\n\n1. **Garansi 100% Pengembalian Dana (Full Refund)**:\n   Dana Anda akan dikembalikan secara penuh 100% tanpa potongan biaya administrasi apa pun ke saldo TapakWallet Anda (instan) atau rekening bank asal (1–2 hari kerja).\n2. **Reschedule Tanggal Bebas Biaya**:\n   Anda dapat memilih tanggal keberangkatan berikutnya pada destinasi yang sama tanpa dikenakan biaya perubahan jadwal.\n3. **Penyesuaian Biaya Rombongan Kecil (Small Group Option)**:\n   Jika seluruh peserta yang sudah mendaftar sepakat untuk tetap berangkat walau kuota kurang, mitra dapat menawarkan penyesuaian biaya minim agar trip tetap jalan sesuai rencana.\n\n> **Tips:** Anda akan menerima notifikasi otomatis via WhatsApp dan Email terkait status kuota Open Trip Anda secara berkala.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['kuota', 'batal', 'refund', 'open trip', 'peserta'],
            ],
            [
                'id' => 'open-trip-solo-traveler',
                'slug' => 'panduan-solo-traveler-dan-pembagian-kamar-open-trip',
                'category' => 'open-trip',
                'categoryLabel' => 'Open Trip',
                'subcategory' => 'Solo Traveler & Kamar',
                'title' => 'Apakah peserta Solo Traveler (sendirian) bisa ikut Open Trip dan bagaimana pembagian kamarnya?',
                'summary' => 'Bisa banget! Lebih dari 65% peserta Open Trip TapakLokal adalah solo traveler dengan pembagian kamar terpisah gender.',
                'content' => "Sangat bisa! Konsep utama **Open Trip TapakLokal** adalah menghubungkan para penjelajah lokal dari berbagai daerah: *\"Berangkat gak kenal, pulang jadi saudara\"*.\n\n- **Pembagian Kamar / Tenda**: Pembagian kamar hotel atau tenda glamping akan dipisahkan secara ketat sesuai jenis kelamin (Pria dengan Pria, Wanita dengan Wanita).\n- **Solo Traveler Room Upgrade**: Jika Anda menginginkan privasi satu kamar sendiri *(Single Supplement)*, Anda dapat memilih opsi upgrade kamar saat proses pemesanan.\n- **Teman & Dokumentasi**: Tour Leader kami selalu siap membantu dokumentasi foto & video estetik selama perjalanan agar solo traveler tetap memiliki kenangan liburan terbaik.",
                'isPopular' => false,
                'updatedAt' => '2026-09-27',
                'tags' => ['solo traveler', 'kamar', 'tenda', 'teman baru', 'open trip'],
            ],
            [
                'id' => 'open-trip-meeting-point',
                'slug' => 'penentuan-titik-kumpul-meeting-point-dan-grup-koordinasi',
                'category' => 'open-trip',
                'categoryLabel' => 'Open Trip',
                'subcategory' => 'Meeting Point & Lapangan',
                'title' => 'Bagaimana cara menentukan Titik Kumpul (Meeting Point) dan kapan grup koordinasi dibuat?',
                'summary' => 'Titik kumpul terpusat di lokasi strategis seperti stasiun/bandara dan grup WhatsApp dibuat H-1 keberangkatan.',
                'content' => "Setiap paket Open Trip memiliki pilihan **Titik Kumpul Resmi (Meeting Point)** yang tercantum jelas di halaman detail trip:\n\n1. **Pilihan Titik Kumpul**:\n   Biasanya berlokasi di titik transportasi utama (misal: Stasiun Pasar Senen, Bandara Juanda Surabaya, Terminal Baranangsiang Bogor, atau Rest Area KM 57).\n2. **Grup Koordinasi WhatsApp**:\n   Koordinator tim lapangan / Tour Leader resmi akan mengundang Anda ke grup WhatsApp koordinasi selambat-lambatnya **H-1 pukul 12.00 WIB**.\n3. **Ketepatan Waktu**:\n   Peserta diharapkan tiba di titik kumpul maksimal 30 menit sebelum jam keberangkatan yang telah ditentukan.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['titik kumpul', 'meeting point', 'whatsapp', 'jadwal', 'stasiun'],
            ],

            // 2. PRIVATE TRIP
            [
                'id' => 'private-trip-custom',
                'slug' => 'cara-kustomisasi-rute-dan-tanggal-private-trip',
                'category' => 'private-trip',
                'categoryLabel' => 'Private Trip',
                'subcategory' => 'Kustomisasi Rute & Armada',
                'title' => 'Bagaimana cara mengajukan custom rute, tanggal keberangkatan, dan titik jemput untuk Private Trip?',
                'summary' => 'Fleksibilitas penuh untuk liburan keluarga, rombongan teman, atau outing kantor dengan penjemputan door-to-door.',
                'content' => "Paket **Private Trip** di TapakLokal dirancang khusus untuk Anda yang menginginkan kebebasan dan privasi maksimal bersama rombongan pribadi.\n\n### Keunggulan Private Trip TapakLokal:\n- **Bebas Tentukan Tanggal**: Berangkat kapan saja setiap hari tanpa menunggu kuota peserta lain.\n- **Titik Jemput Fleksibel (*Door-to-Door*)**: Penjemputan langsung di rumah, hotel, stasiun, atau kantor Anda di area penjemputan mitra.\n- **Kustomisasi Itinerary & Durasi**: Ingin menambah spot sunset, durasi santai di kafe lokal, atau memilih resto kuliner khas tertentu? Diskusikan langsung dengan tim kami!\n\n### Cara Mengajukan Custom Private Trip:\n1. Buka halaman katalog dan pilih paket berlabel **Private Trip**.\n2. Klik tombol **\"Chat Admin / Kustomisasi Trip\"** pada profil mitra.\n3. Sebutkan jumlah peserta, tanggal yang diinginkan, dan preferensi destinasi Anda.\n4. Tim mitra resmi TapakLokal akan menerbitkan rincian invoice khusus ke akun TapakLokal Anda.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['private trip', 'custom', 'keluarga', 'door to door', 'jadwal'],
            ],
            [
                'id' => 'private-trip-capacity',
                'slug' => 'kapasitas-jumlah-peserta-dan-armada-private-trip',
                'category' => 'private-trip',
                'categoryLabel' => 'Private Trip',
                'subcategory' => 'Fasilitas & Kapasitas',
                'title' => 'Berapa batas minimal dan maksimal jumlah peserta untuk memesan Private Trip?',
                'summary' => 'Mulai dari 2 orang hingga gathering rombongan besar (50+ orang) dengan armada eksekutif berstandar pariwisata.',
                'content' => "Private Trip di TapakLokal tidak memiliki batasan kaku:\n- **Private Couple / Family (2–6 Orang)**: Menggunakan armada nyaman seperti Toyota Innova Reborn, Avanza Veloz, atau HiAce Commuter.\n- **Medium Group (7–15 Orang)**: Menggunakan Toyota HiAce Premio atau Isuzu Elf Long Luxury.\n- **Corporate & Community Gathering (20–100+ Orang)**: Menggunakan Medium Bus hingga Big Bus Pariwisata HDD/SHD dengan fasilitas audio karaoke, charging port, dan asuransi penuh.",
                'isPopular' => false,
                'updatedAt' => '2026-09-27',
                'tags' => ['kapasitas', 'jumlah orang', 'bus', 'hiace', 'private trip'],
            ],

            // 3. PO OLEH-OLEH & KULINER
            [
                'id' => 'culinary-po-how-it-works',
                'slug' => 'alur-pemesanan-dan-pengiriman-open-po-oleh-oleh',
                'category' => 'culinary-po',
                'categoryLabel' => 'PO Oleh-Oleh',
                'subcategory' => 'Alur Pemesanan & Jastip',
                'title' => 'Bagaimana sistem pemesanan & pengiriman Open PO Oleh-Oleh & Kuliner Khas Daerah?',
                'summary' => 'Titip beli kuliner legendaris dan suvenir autentik langsung dari UMKM lokal destinasi trip, dijamin asli dan fresh.',
                'content' => "Fitur **Open PO Oleh-Oleh & Kuliner Khas** di TapakLokal menghubungkan traveler dan pecinta kuliner nusantara langsung dengan UMKM lokal terbaik di setiap daerah tujuan trip (cth: Peuyeum Bandung, Keripik Tempe Malang, Bakpia Kukus Jogja, Kopi Ijen, Sambal Roa, Kain Tenun Tradisional).\n\n### Alur Pemesanan:\n1. **Pilih Produk PO**: Buka menu *Kuliner Lokal* atau tab *Open PO Oleh-Oleh* pada destinasi trip yang Anda pilih.\n2. **Pilih Metode Penerimaan**:\n   - **Diserahkan saat Trip**: Jika Anda juga memesan trip, pesanan oleh-oleh Anda akan dibawakan langsung oleh Tour Leader saat trip berakhir.\n   - **Kirim ke Rumah (Jastip Pengiriman)**: Pesanan dikirim langsung ke alamat rumah Anda melalui ekspedisi kurir kilat terpercaya.\n3. **Pembayaran Aman**: Dana Anda ditahan aman di sistem TapakLokal dan baru diteruskan ke UMKM setelah produk terkonfirmasi diterima dengan baik.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['open po', 'oleh-oleh', 'kuliner', 'jastip', 'makanan khas'],
            ],
            [
                'id' => 'culinary-po-freshness-guarantee',
                'slug' => 'jaminan-kesegaran-makanan-dan-kebijakan-retur-oleh-oleh',
                'category' => 'culinary-po',
                'categoryLabel' => 'PO Oleh-Oleh',
                'subcategory' => 'Jaminan Mutu & Kesegaran',
                'title' => 'Bagaimana jaminan kesegaran makanan dan kebijakan retur jika oleh-oleh rusak saat diterima?',
                'summary' => 'Standar kemasan vacuum/insulasi khusus dan Garansi 100% Uang Kembali jika produk diterima rusak atau basi.',
                'content' => "Kualitas dan keaslian produk adalah prioritas utama TapakLokal:\n- **Standar Kemasan Makanan Basah**: Makanan basah khas daerah dikemas menggunakan teknologi *vacuum pack* dan insulasi *thermal bag* dengan *ice gel* untuk menjaga suhu dan ketahanan rasa.\n- **Produk UMKM Terverifikasi**: Hanya UMKM terdaftar yang memiliki izin edar P-IRT / Sertifikasi Halal yang dapat menjual produk pangan di TapakLokal.\n- **Garansi 100% Ganti Baru / Refund**: Jika paket makanan sampai dalam kondisi rusak, pecah, atau basi akibat keterlambatan, sertakan video *unboxing* dalam waktu 1x24 jam untuk klaim penggantian dana penuh 100%.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['kesegaran', 'garansi', 'rusak', 'retur', 'umkm'],
            ],

            // 4. METODE PEMBAYARAN
            [
                'id' => 'payment-available-methods',
                'slug' => 'pilihan-metode-pembayaran-resmi-tapaklokal',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Panduan Umum Pembayaran',
                'title' => 'Apa saja metode pembayaran resmi yang tersedia di TapakLokal?',
                'summary' => 'Dukungan QRIS instan, Virtual Account semua bank (BCA, Mandiri, BRI, BNI), E-Wallet, dan gerai minimarket.',
                'content' => 'Pilih metode pembayaran yang tersedia pada checkout. Pilihan yang dapat diaktifkan mencakup GoPay, OVO melalui QRIS, BCA Virtual Account, Mandiri, Alfamart/Alfamidi, dan Indomaret. Ketersediaan mengikuti konfigurasi layanan pembayaran. Instruksi atau kode pembayaran muncul setelah kamu menekan tombol bayar. Status lunas ditetapkan berdasarkan konfirmasi penyedia pembayaran. Saldo TapakWallet belum dapat digunakan.',
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['metode pembayaran', 'pembayaran', 'qris', 'transfer bank', 'virtual account', 'indomaret', 'alfamart'],
            ],
            [
                'id' => 'payment-bca-va',
                'slug' => 'cara-bayar-bca-virtual-account',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Virtual Account Bank',
                'title' => 'Cara Pembayaran dengan BCA Virtual Account',
                'summary' => 'Panduan transfer Virtual Account BCA melalui BCA Mobile (m-BCA), myBCA, KlikBCA, dan ATM BCA.',
                'content' => "> **Penting:** Nomor BCA Virtual Account bersifat unik untuk setiap transaksi dan terverifikasi otomatis 24 jam tanpa perlu upload bukti transfer.\n\nSetelah memilih **BCA Virtual Account** pada halaman checkout, ikuti langkah-langkah pembayaran berikut:\n\n### Via BCA Mobile (m-BCA)\n1. Buka aplikasi **BCA mobile**, pilih **m-BCA** dan masukkan Kode Akses.\n2. Pilih menu **m-Transfer** > **BCA Virtual Account**.\n3. Masukkan **Nomor BCA Virtual Account** pesanan Anda, lalu klik **Send**.\n4. Periksa ringkasan pembayaran di layar (Nama Penerima: **TapakLokal** dan jumlah tagihan).\n5. Masukkan PIN m-BCA Anda untuk menyelesaikan pembayaran.\n6. Transaksi berhasil dan E-Tiket langsung terbit di menu Pesanan Saya.\n\n### Via myBCA / KlikBCA\n1. Login ke aplikasi **myBCA** atau web **KlikBCA Individu**.\n2. Pilih menu **Transfer Dana** > **Transfer ke BCA Virtual Account**.\n3. Masukkan nomor Virtual Account dan konfirmasi jumlah nominal tagihan.\n4. Masukkan PIN / respon KeyBCA untuk memproses transaksi.\n\n### Via ATM BCA\n1. Masukkan kartu ATM BCA dan 6 digit PIN.\n2. Pilih menu **Transaksi Lainnya** > **Transfer** > **ke Rekening BCA Virtual Account**.\n3. Masukkan nomor BCA Virtual Account Anda lalu tekan **Benar**.\n4. Periksa detail tagihan di layar ATM, lalu tekan **Ya** untuk membayar.\n5. Simpan struk pembayaran ATM sebagai bukti transaksi sah.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['bca', 'virtual account', 'mbca', 'klikbca', 'atm bca', 'transfer bca'],
            ],
            [
                'id' => 'payment-mandiri-va',
                'slug' => 'cara-bayar-mandiri-virtual-account',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Virtual Account Bank',
                'title' => 'Cara Pembayaran dengan Mandiri Virtual Account',
                'summary' => 'Panduan lengkap bayar pesanan TapakLokal via Livin by Mandiri (Aplikasi Mobile) dan ATM Mandiri.',
                'content' => "> **Penting:** Pastikan melakukan transfer sebelum batas waktu pembayaran habis agar pesanan tidak dibatalkan otomatis oleh sistem.\n\nSetelah memilih metode pembayaran **Mandiri Virtual Account**, ikuti langkah-langkah berikut:\n\n### Via Livin' by Mandiri (Aplikasi Mobile)\n1. Buka aplikasi **Livin' by Mandiri**, lalu login dengan akun Anda.\n2. Pilih menu **Transfer Rupiah** > **Transfer ke Penerima Baru**.\n3. Masukkan **Nomor Mandiri Virtual Account** Anda lalu klik **Lanjutkan**.\n4. Pada layar konfirmasi ringkasan pembayaran, pastikan detail transaksi sudah benar (Nama Merchant: **TapakLokal** dan total nominal tagihan).\n5. Masukkan PIN Livin' Mandiri Anda.\n6. Transaksi selesai dan status pesanan otomatis terverifikasi lunas. Simpan bukti transfer Anda.\n\n### Via ATM Mandiri\n1. Masukkan kartu ATM Mandiri dan 6 digit PIN Anda.\n2. Pilih menu **Bayar/Beli** > **Multi Payment**.\n3. Masukkan Kode Perusahaan TapakLokal atau pilih penyedia jasa.\n4. Masukkan **Nomor Mandiri Virtual Account** yang tertera pada invoice Anda.\n5. Pada layar konfirmasi, pastikan nama dan total tagihan sesuai, lalu tekan **1** (Ya).\n6. Selesaikan pembayaran dan simpan struk bukti transaksi ATM Anda.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['mandiri', 'virtual account', 'livin', 'atm mandiri', 'transfer mandiri'],
            ],
            [
                'id' => 'payment-gopay',
                'slug' => 'cara-bayar-tapaklokal-lewat-gopay',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Uang Elektronik (E-Wallet)',
                'title' => 'Cara Bayar Transaksi TapakLokal dengan GoPay / GoPay Later',
                'summary' => 'Langkah bayar instan menggunakan GoPay di aplikasi Gojek atau QRIS tanpa biaya tambahan.',
                'content' => "> **Penting:** Pastikan saldo GoPay atau limit GoPay Later Anda mencukupi sebelum melanjutkan pembayaran.\n\nPanduan pembayaran via GoPay:\n\n1. Pilih metode pembayaran **GoPay** di halaman checkout TapakLokal.\n2. Jika menggunakan ponsel, Anda akan otomatis diarahkan ke aplikasi Gojek. Jika menggunakan desktop/komputer, scan kode QRIS GoPay yang muncul di layar menggunakan aplikasi Gojek / m-banking apa pun.\n3. Pada layar konfirmasi Gojek, periksa nama merchant (**TapakLokal**) dan jumlah tagihan.\n4. Pilih sumber dana (Saldo GoPay, GoPay Coins, atau GoPay Later).\n5. Klik **Konfirmasi & Bayar**, lalu masukkan 6 digit PIN GoPay Anda.\n6. Pembayaran langsung selesai secara instan dan status pesanan otomatis terverifikasi.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['gopay', 'gojek', 'e-wallet', 'qris', 'gopay later'],
            ],
            [
                'id' => 'payment-ovo',
                'slug' => 'cara-bayar-tapaklokal-lewat-ovo',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Uang Elektronik (E-Wallet)',
                'title' => 'Cara Bayar Transaksi TapakLokal dengan OVO',
                'summary' => 'Panduan konfirmasi pembayaran via aplikasi OVO dan verifikasi real-time.',
                'content' => "> **Penting:** Selesaikan konfirmasi di aplikasi OVO dalam waktu 60 detik setelah menekan tombol bayar.\n\nPanduan pembayaran via OVO:\n\n1. Pilih metode pembayaran **OVO** pada halaman checkout pesanan TapakLokal.\n2. Masukkan nomor handphone yang terdaftar di akun OVO Anda.\n3. Klik tombol **Bayar Sekarang**.\n4. Buka aplikasi OVO di smartphone Anda atau tap notifikasi push pembayaran yang masuk.\n5. Periksa ringkasan tagihan dari **TapakLokal** dan pilih pembayaran dengan OVO Cash atau OVO Points.\n6. Klik tombol **Bayar**, lalu masukkan Security Code (PIN) OVO Anda.\n7. Transaksi berhasil seketika dan E-Tiket pesanan Anda langsung aktif.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['ovo', 'e-wallet', 'dompet digital', 'ovo cash', 'ovo points'],
            ],
            [
                'id' => 'payment-indomaret',
                'slug' => 'cara-bayar-tapaklokal-lewat-indomaret',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Gerai Minimarket',
                'title' => 'Cara Bayar Transaksi TapakLokal lewat Gerai Indomaret',
                'summary' => 'Langkah bayar tunai di kasir Indomaret dengan menunjukkan kode pembayaran transaksi.',
                'content' => "> **Penting:** Biaya administrasi gerai retail mungkin berlaku sesuai kebijakan kasir dan tidak termasuk dalam potongan promo voucher.\n\nBerikut cara bayar transaksi TapakLokal via Indomaret:\n\n1. Pilih opsi pembayaran **\"Indomaret\"** pada halaman checkout transaksi.\n2. Masukkan Kode Promo atau Voucher jika Anda memilikinya.\n3. Lanjutkan dengan menekan tombol **\"Bayar Sekarang\"** untuk mendapatkan Kode Pembayaran Indomaret.\n4. Kunjungi gerai Indomaret terdekat dan informasikan kepada kasir bahwa Anda ingin melakukan pembayaran untuk transaksi **TapakLokal**.\n5. Tunjukkan **Kode Pembayaran** (bukan ID booking) kepada kasir.\n6. Kasir akan memverifikasi nama akun dan total tagihan yang harus dibayarkan.\n7. Lakukan pembayaran tunai atau debit sesuai nominal yang disebutkan oleh kasir.\n8. Pastikan Anda menerima dan menyimpan struk bukti pembayaran resmi dari kasir.\n9. Setelah langkah-langkah di atas selesai, status transaksi Anda otomatis terkonfirmasi lunas dan E-Tiket Anda aktif.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['indomaret', 'minimarket', 'kasir', 'tunai', 'gerai retail'],
            ],
            [
                'id' => 'payment-alfamart',
                'slug' => 'cara-bayar-tapaklokal-lewat-alfamart',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Gerai Minimarket',
                'title' => 'Cara Bayar Transaksi TapakLokal lewat Gerai Alfamart',
                'summary' => 'Panduan pembayaran di kasir Alfamart, Alfamidi, Lawson, dan Dan+Dan di seluruh Indonesia.',
                'content' => "> **Penting:** Pembayaran dapat dilakukan di seluruh jaringan Alfamart, Alfamidi, Lawson, dan Dan+Dan di seluruh Indonesia.\n\nBerikut panduan cara bayar transaksi TapakLokal via Alfamart:\n\n1. Pilih opsi pembayaran **\"Alfamart\"** pada halaman transaksi.\n2. Catat atau screenshot **Kode Pembayaran Alfamart** beserta batas waktu pembayaran yang tertera.\n3. Datangi gerai Alfamart / Alfamidi terdekat dan sampaikan kepada kasir bahwa Anda ingin membayar tagihan **TapakLokal**.\n4. Sebutkan **Kode Pembayaran** kepada kasir.\n5. Periksa kesesuaian nama dan nominal transaksi di layar kasir.\n6. Lakukan pembayaran tunai atau debit sesuai total tagihan.\n7. Simpan struk pembayaran yang dicetak kasir sebagai bukti sah.\n8. Status pembayaran Anda akan terupdate otomatis dalam waktu 1–3 menit.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['alfamart', 'alfamidi', 'minimarket', 'kasir', 'tunai'],
            ],
            [
                'id' => 'payment-unverified-troubleshoot',
                'slug' => 'solusi-pembayaran-sudah-transfer-tapi-status-belum-update',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Bantuan Kendala Bayar',
                'title' => 'Bagaimana jika saya sudah transfer pembayaran tetapi status pesanan belum terupdate?',
                'summary' => 'Langkah verifikasi transaksi, waktu sinkronisasi sistem perbankan, dan solusi konfirmasi ke Customer Service.',
                'content' => "Sistem pembayaran TapakLokal memverifikasi transaksi secara otomatis dalam waktu 1–3 menit. Jika status pesanan Anda belum berubah menjadi **\"Lunas / Terkonfirmasi\"**:\n\n1. **Periksa Waktu Transaksi & Nomor Virtual Account**:\n   Pastikan Anda mentransfer sesuai nominal yang tertera ke nomor Virtual Account yang tepat sebelum batas waktu (*expiry time*) berakhir.\n2. **Cek Riwayat Mutasi Rekening**:\n   Pastikan saldo pada rekening bank / e-wallet Anda telah berhasil terpotong.\n3. **Muat Ulang Halaman Pesanan**:\n   Tekan tombol **\"Cek Status Pembayaran\"** pada halaman rincian tagihan Anda.\n4. **Hubungi Customer Service Kami**:\n   Jika dalam 10 menit status belum terupdate, klik menu **\"Contact Us / Chat WhatsApp\"** dengan melampirkan Kode Pesanan (contoh: #TPL-2026-XXXX) dan foto bukti mutasi untuk verifikasi manual instan oleh tim finance kami.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['status pembayaran', 'pembayaran belum masuk', 'kendala transfer', 'verifikasi', 'bantuan bayar'],
            ],
            [
                'id' => 'payment-voucher-discount',
                'slug' => 'cara-menggunakan-voucher-promo-dan-diskon-tapaklokal',
                'category' => 'payment-methods',
                'categoryLabel' => 'Metode Pembayaran',
                'subcategory' => 'Voucher & Promo Diskon',
                'title' => 'Bagaimana cara menggunakan voucher promo atau potongan diskon saat checkout?',
                'summary' => 'Gunakan kode promo pada kolom voucher di halaman pembayaran untuk mendapatkan potongan harga instan.',
                'content' => "Nikmati diskon dan promo menarik di TapakLokal dengan langkah berikut:\n\n1. Pilih paket trip atau produk Open PO Oleh-Oleh yang Anda inginkan.\n2. Pada halaman ringkasan checkout, temukan kolom **\"Punya Kode Promo / Voucher?\"**.\n3. Masukkan kode promo yang valid atau pilih dari daftar voucher yang tersedia di akun Anda, lalu klik **\"Terapkan\"**.\n4. Total tagihan akan otomatis terpotong sesuai nilai diskon voucher sebelum Anda memilih metode pembayaran.\n\n> **Catatan:** Pastikan membaca syarat & ketentuan masing-masing voucher seperti batas minimum transaksi dan periode keberangkatan trip.",
                'isPopular' => false,
                'updatedAt' => '2026-09-27',
                'tags' => ['voucher', 'promo', 'diskon', 'potongan harga', 'hemat'],
            ],

            // 5. TAPAKWALLET
            [
                'id' => 'wallet-benefits',
                'slug' => 'keuntungan-tapakwallet-dan-cara-top-up-saldo',
                'category' => 'wallet',
                'categoryLabel' => 'TapakWallet & Refund',
                'subcategory' => 'Fitur Saldo & Top Up',
                'title' => 'Apa keuntungan menggunakan TapakWallet dan bagaimana cara top up saldo?',
                'summary' => 'Saldo TapakWallet, top up, dan penarikan dana belum tersedia. Gunakan metode pembayaran yang aktif di checkout.',
                'content' => 'Saldo TapakWallet, top up, dan penarikan dana belum tersedia. Pilih metode pembayaran yang aktif pada halaman checkout. Preferensi metode pembayaran dapat disimpan di akun traveler; preferensi tersebut bukan saldo uang.',
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['tapakwallet', 'saldo', 'topup', 'keuntungan', 'tarik dana'],
            ],
            [
                'id' => 'wallet-refund-flow',
                'slug' => 'alur-proses-dan-estimasi-waktu-pencairan-refund',
                'category' => 'wallet',
                'categoryLabel' => 'TapakWallet & Refund',
                'subcategory' => 'Alur & Estimasi Refund',
                'title' => 'Bagaimana alur dan estimasi waktu proses pengembalian dana (Refund)?',
                'summary' => 'Ajukan refund dari detail pesanan. Persetujuan admin dan pengembalian dana merupakan tahap yang berbeda.',
                'content' => 'Buka detail pesanan untuk mengajukan refund dan sertakan alasan. Tim operasional meninjau pengajuan sesuai ketentuan pesanan. Status disetujui berarti pengajuan telah diterima, bukan bukti dana sudah kembali. Pengembalian dana dikonfirmasi melalui penyedia pembayaran dan rekonsiliasi keuangan. Pantau status pesanan atau hubungi bantuan dengan nomor referensi pesanan. Waktu penyelesaian bergantung pada hasil peninjauan dan metode pembayaran; refund ke saldo TapakWallet belum tersedia.',
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['refund', 'pengembalian dana', 'saldo', 'rekening', 'estimasi'],
            ],

            // 6. PROFIL & KEAMANAN KYC
            [
                'id' => 'profile-kyc-importance',
                'slug' => 'mengapa-perlu-verifikasi-identitas-kyc-di-tapaklokal',
                'category' => 'profile-security',
                'categoryLabel' => 'Akun & KYC',
                'subcategory' => 'Verifikasi Identitas & NIK',
                'title' => 'Mengapa saya perlu melakukan Verifikasi Identitas (KYC) di akun TapakLokal?',
                'summary' => 'Diperlukan untuk aktivasi asuransi keselamatan jiwa resmi dan syarat manifest izin masuk kawasan wisata alam.',
                'content' => "Verifikasi Identitas (*Know Your Customer / KYC*) di TapakLokal bukan sekadar formalitas, melainkan syarat krusial untuk keselamatan Anda:\n\n1. **Aktivasi Asuransi Jiwa & Kecelakaan**:\n   Setiap tiket trip di TapakLokal terhubung dengan polis asuransi perjalanan resmi yang membutuhkan data nama sesuai KTP/Paspor dan NIK yang valid.\n2. **Izin Masuk Kawasan Konservasi & Taman Nasional**:\n   Destinasi seperti Gunung Bromo, Taman Nasional Komodo, Ujung Kulon, dan Dieng mewajibkan data manifest pengunjung resmi sebelum memasuki pos perizinan.\n3. **Keamanan Transaksi & Saldo**:\n   Mencegah penyalahgunaan akun, pencurian poin voucher, dan penipuan digital. Data pribadi Anda dienkripsi ketat sesuai standar UU Perlindungan Data Pribadi (PDP).",
                'isPopular' => false,
                'updatedAt' => '2026-09-27',
                'tags' => ['kyc', 'ktp', 'asuransi', 'keamanan akun', 'manifest'],
            ],

            // 7. ANTI-PUNGLI
            [
                'id' => 'anti-pungli-policy',
                'slug' => 'komitmen-transparansi-harga-dan-anti-pungli-tapaklokal',
                'category' => 'anti-pungli',
                'categoryLabel' => 'Lapor Pungli',
                'subcategory' => 'Transparansi All-In',
                'title' => 'Bagaimana komitmen TapakLokal terhadap transparansi harga dan pencegahan Pungutan Liar (Pungli)?',
                'summary' => 'Semua harga paket adalah harga All-In transparan. Tidak ada biaya siluman atau pungutan liar tak berizin.',
                'content' => "TapakLokal didirikan dengan komitmen kuat menciptakan ekosistem pariwisata lokal yang beretika, transparan, dan terpercaya:\n\n- **Kebijakan Harga All-In Transparan**:\n  Semua biaya tercantum jelas: tiket masuk resmi destinasi wisata, retribusi daerah, biaya parkir, biaya kapal penyeberangan, dan tips pemandu telah diatur di dalam rincian paket tanpa ada pungutan misterius di lokasi.\n- **Kanal Lapor Pungli Cepat**:\n  Jika Anda menemukan oknum atau pihak di lapangan yang meminta pungutan liar di luar rincian *Exclude* paket, Anda dapat langsung menekan tombol **\"Lapor Pungli\"** di aplikasi atau menghubungi Hotline Darurat TapakLokal.\n- **Sanksi Mitra & Kompensasi**:\n  Mitra yang terbukti melakukan pungli akan dikenai sanksi pemutusan kemitraan, dan peserta berhak menerima kompensasi penggantian dana.",
                'isPopular' => true,
                'updatedAt' => '2026-09-27',
                'tags' => ['anti pungli', 'pungli', 'transparansi harga', 'lapor', 'biaya siluman'],
            ],

            // 8. MITRA & PEMANDU LOKAL
            [
                'id' => 'partners-guide-standards',
                'slug' => 'standar-verifikasi-pemandu-wisata-dan-operator-trip',
                'category' => 'partners-guide',
                'categoryLabel' => 'Mitra & Guide',
                'subcategory' => 'Lisensi HPI & Standar BNSP',
                'title' => 'Apa standar verifikasi bagi Pemandu Wisata dan Operator Trip yang terdaftar di TapakLokal?',
                'summary' => 'Semua mitra wajib berbadan hukum legal, memiliki lisensi resmi HPI, dan armada lulus uji kelaikan jalan.',
                'content' => "Kami menerapkan proses kurasi dan sertifikasi ketat sebelum mitra operator dapat membuka open trip maupun private trip di platform TapakLokal:\n\n1. **Legalitas Usaha**: Wajib berbadan hukum resmi (PT/CV/Koperasi Wisata) dengan NIB dan TDUP pariwisata yang aktif.\n2. **Sertifikasi Pemandu (Tour Leader & Guide)**: Wajib memiliki sertifikat kompetensi pemandu wisata dari BNSP / lisensi HPI (Himpunan Pramuwisata Indonesia).\n3. **Standar Armada Pariwisata**: Kendaraan pariwisata wajib memiliki izin trayek pariwisata, uji berkala KIR aktif, sabuk pengaman berfungsi, serta supir berlisensi SIM pariwisata.\n4. **Pelatihan *First Aid* (P3K)**: Pemandu dibekali keterampilan tanggap darurat pertolongan pertama di alam bebas (laut dan gunung).",
                'isPopular' => false,
                'updatedAt' => '2026-09-27',
                'tags' => ['mitra', 'pemandu', 'guide', 'hpi', 'legalitas'],
            ],
        ];
    }

    /**
     * Get articles filtered by category and search.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getArticles(?string $categorySlug = null, ?string $search = null): array
    {
        $articles = $this->getAllArticles();

        if ($categorySlug && $categorySlug !== 'all') {
            $cat = $this->getCategory($categorySlug);
            $catId = $cat['id'] ?? $categorySlug;
            $aliases = $cat['aliases'] ?? [];

            $articles = array_values(array_filter($articles, function ($a) use ($catId, $categorySlug, $aliases) {
                return $a['category'] === $catId || $a['category'] === $categorySlug || in_array($a['category'], $aliases);
            }));
        }

        if ($search && trim($search) !== '') {
            $q = strtolower(trim($search));
            $articles = array_values(array_filter($articles, function ($a) use ($q) {
                if (str_contains(strtolower($a['title']), $q)) {
                    return true;
                }
                if (str_contains(strtolower($a['summary']), $q)) {
                    return true;
                }
                if (str_contains(strtolower($a['content']), $q)) {
                    return true;
                }
                if (str_contains(strtolower($a['categoryLabel']), $q)) {
                    return true;
                }
                foreach ($a['tags'] ?? [] as $tag) {
                    if (str_contains(strtolower($tag), $q)) {
                        return true;
                    }
                }

                return false;
            }));
        }

        return $articles;
    }

    /**
     * Get popular articles.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPopularArticles(?string $categorySlug = null, int $limit = 5): array
    {
        $articles = $this->getArticles($categorySlug);
        $popular = array_values(array_filter($articles, fn ($a) => ! empty($a['isPopular'])));

        if (empty($popular)) {
            return array_slice($articles, 0, $limit);
        }

        return array_slice($popular, 0, $limit);
    }

    /**
     * Find single article.
     *
     * @return array<string, mixed>|null
     */
    public function getArticle(string $categorySlug, string $articleSlug): ?array
    {
        $cat = $this->getCategory($categorySlug);
        $catId = $cat['id'] ?? $categorySlug;
        $aliases = $cat['aliases'] ?? [];

        foreach ($this->getAllArticles() as $a) {
            $matchesCat = ($categorySlug === 'all' || $a['category'] === $catId || $a['category'] === $categorySlug || in_array($a['category'], $aliases));
            $matchesSlug = ($a['slug'] === $articleSlug || $a['id'] === $articleSlug);

            if ($matchesCat && $matchesSlug) {
                return $a;
            }
        }

        // Fallback search only by slug if category moved
        foreach ($this->getAllArticles() as $a) {
            if ($a['slug'] === $articleSlug || $a['id'] === $articleSlug) {
                return $a;
            }
        }

        return null;
    }

    /**
     * Get related articles in same category.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRelatedArticles(string $categorySlug, string $articleSlug, int $limit = 4): array
    {
        $articles = $this->getArticles($categorySlug);
        $filtered = array_values(array_filter($articles, fn ($a) => $a['slug'] !== $articleSlug && $a['id'] !== $articleSlug));

        return array_slice($filtered, 0, $limit);
    }

    /**
     * Get articles grouped by subcategory for a category page.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getArticlesGroupedBySubcategory(string $categorySlug): array
    {
        $articles = $this->getArticles($categorySlug);
        $grouped = [];

        foreach ($articles as $a) {
            $sub = $a['subcategory'] ?? 'Informasi Lainnya';
            $grouped[$sub][] = $a;
        }

        return $grouped;
    }
}
