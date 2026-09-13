<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$getAllTrips = function (): array {
    return [
        [
            'id' => 1,
            'title' => 'Bromo Midnight Sunrise & Savana Pasir Berbisik',
            'location' => 'Probolinggo - Malang, Jawa Timur',
            'region' => 'Jawa',
            'category' => 'Alam & Gunung',
            'trip_type' => ['open', 'private'],
            'duration' => '2H 1M',
            'price' => 385000,
            'private_price' => 525000,
            'rating' => 4.9,
            'review_count' => 142,
            'slots_left' => 4,
            'quota' => 14,
            'difficulty' => 'Mudah',
            'departure_date' => '20 Sept 2026',
            'image' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df8?auto=format&fit=crop&w=800&q=80',
            'akamsi' => [
                'name' => 'Cak Dimas Bromo',
                'badge' => 'Akamsi Lokal Terverifikasi',
                'experience' => '8 Tahun Pemandu Tengger',
            ],
            'travel_partner' => [
                'name' => 'Tapak Semeru Adventure Tour',
                'logo' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?auto=format&fit=crop&w=120&q=80',
                'badge' => 'Operator Resmi Bromo Tengger Semeru',
                'license' => 'Izin TDUP No. 503/44/PAR/2023',
                'rating' => 4.9,
                'trips_completed' => '450+ Trip Sukses',
            ],
            'accommodation' => [
                'name' => 'Oemah Tengger Lodge & Homestay',
                'logo' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Mitra Paguyuban Warga Tosari (Bpk. Slamet)',
                'room_type' => 'Kamar Privat Air Hangat (Water Heater) + View Gunung',
                'facilities' => 'Water Heater, WiFi Cepat, Selimut Wol Tebal, Welcome Drink Khas Tengger',
                'description' => 'Homestay tradisional kayu hangat milik warga lereng Bromo dengan fasilitas lengkap dan pemandangan lembah langsung.',
            ],
            'vehicle' => [
                'name' => 'Jeep Toyota Hardtop 4x4 Offroad Bromo',
                'logo' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Paguyuban Jeep Wisata Tengger (Driver: Cak Bambang)',
                'type' => 'Armada 4x4 Resmi Taman Nasional Bromo Tengger Semeru',
                'capacity' => 'Maksimal 6 Orang / Mobil',
            ],
            'restaurant_umkm' => [
                'name' => 'Warung Rawon Nguling Hj. Maryam & Dapur Mak Asih',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'UMKM Kuliner Legendaris Sejak 1984',
                'specialty' => 'Rawon Daging Sapi Kluwek Otentik & Ayam Bakar Bumbu Rujak',
                'address' => 'Desa Wisata Tosari, Kawasan Bromo',
            ],
            'free_souvenir' => [
                'name' => '1 Box Keripik Apel Malang Asli & 1 Pouch Teh Daun Semanggi',
                'vendor' => 'Sentra UMKM Agro Apel Batu & Tani Herbal Tengger',
                'badge' => 'Gratis Termasuk Tanpa Add-On (Rp 0)',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
                'description' => 'Dibagikan langsung kepada setiap wisatawan pada hari kepulangan tanpa pungutan sepeser pun.',
            ],
            'gallery' => [
                'destinations' => [
                    ['url' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df8?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Golden Sunrise Penanjakan 1 Bromo', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Kawah Aktif Bromo & Pura Luhur Poten', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Bukit Teletubbies & Savana Hijau', 'category' => 'Spot Wisata'],
                ],
                'accommodation' => [
                    ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Kamar Bersih Privat dengan Water Heater Oemah Tengger', 'category' => 'Penginapan'],
                    ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Tampak Depan Homestay Menghadap Lembah Hijau', 'category' => 'Penginapan'],
                ],
                'vehicle' => [
                    ['url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Jeep Hardtop 4x4 Offroad Gagah Berstandar Resmi', 'category' => 'Kendaraan'],
                    ['url' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Armada Melintasi Lautan Pasir Berbisik', 'category' => 'Kendaraan'],
                ],
                'food' => [
                    ['url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Nasi Rawon Daging Nguling Kuah Hitam Otentik', 'category' => 'Makanan'],
                    ['url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Ayam Bakar Bumbu Rujak Khas Tengger + Sambal Cobek', 'category' => 'Makanan'],
                ],
            ],
            'meals' => [
                'included_note' => 'Termasuk 3x Makan Lokal Pilihan (Wisatawan bebas pilih tanpa biaya tambahan)',
                'options' => [
                    [
                        'session' => 'Sarapan (Breakfast)',
                        'menu_choices' => [
                            'Nasi Rawon Daging Nguling Kuah Hitam Otentik',
                            'Nasi Uduk Tengger Telur Balado & Sambal Bajak',
                            'Bubur Ayam Kampung Tradisional Hangat',
                        ],
                    ],
                    [
                        'session' => 'Makan Siang (Lunch)',
                        'menu_choices' => [
                            'Ayam Bakar Bumbu Rujak Khas Tengger + Lalap Daun Segar',
                            'Ikan Gurame Goreng Renyah Sambal Terasi Cobek',
                            'Paket Vegetarian: Sayur Asem, Tempe Bacem, & Jamur Crispy',
                        ],
                    ],
                    [
                        'session' => 'Makan Malam (Dinner)',
                        'menu_choices' => [
                            'Sop Buntut Sapi Kuah Rempah Penghangat Suhu Bromo',
                            'Nasi Goreng Jawa Kampung Telur Ceplok & Kerupuk',
                            'Tahu Telur Bumbu Petis Khas Jawa Timuran',
                        ],
                    ],
                ],
            ],
            'highlights' => ['Penanjakan 1', 'Kawah Bromo', 'Bukit Teletubbies', 'Pasir Berbisik'],
        ],
        [
            'id' => 2,
            'title' => 'Liveaboard Komodo, Padar & Pink Beach Hidden Cove',
            'location' => 'Labuan Bajo, Flores, NTT',
            'region' => 'Bali & Nusa Tenggara',
            'category' => 'Bahari & Kepulauan',
            'trip_type' => ['open', 'private'],
            'duration' => '3H 2M',
            'price' => 2450000,
            'private_price' => 3350000,
            'rating' => 5.0,
            'review_count' => 98,
            'slots_left' => 3,
            'quota' => 12,
            'difficulty' => 'Sedang',
            'departure_date' => '25 Sept 2026',
            'image' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
            'akamsi' => [
                'name' => 'Bang Mansur Bajo',
                'badge' => 'Akamsi Lokal Terverifikasi',
                'experience' => 'Penyelam & Pelaut Lokal',
            ],
            'travel_partner' => [
                'name' => 'Flores Ocean Explorer Bajo',
                'logo' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=120&q=80',
                'badge' => 'Operator Berlisensi Labuan Bajo',
                'license' => 'Izin Syahbandar & KSOP No. 102/LBJ/2022',
                'rating' => 5.0,
                'trips_completed' => '380+ Pelayaran',
            ],
            'accommodation' => [
                'name' => 'Kabin AC KM Phinisi Bajo Explorer',
                'logo' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'PT Bahari Flores Bersaudara (Capt. Herman Bajo)',
                'room_type' => 'Private/Shared Deluxe Ocean View Cabin dengan Ensuite Bathroom',
                'facilities' => 'Kamar Mandi Dalam, AC Dingin, Sundeck Rooftop, Alat Snorkeling Lengkap',
                'description' => 'Kapal Phinisi megah kayu ulin dengan kabin sejuk AC dan sundeck santai memandang laut Flores.',
            ],
            'vehicle' => [
                'name' => 'Kapal Phinisi Kayu Ulin & Sekoci Speedboat Tender',
                'logo' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Komunitas Pelaut Bahari Labuan Bajo',
                'type' => 'Phinisi Tradisional Berstandar Keselamatan Internasional',
                'capacity' => 'Maksimal 12 Peserta',
            ],
            'restaurant_umkm' => [
                'name' => 'Dapur Pelaut Kampung Ujung & Chef Phinisi Bajo',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'UMKM Sentra Seafood Kampung Ujung Bajo',
                'specialty' => 'Ikan Kerapu Bakar Dabu-Dabu & Cumi Saus Madu Flores Segar',
                'address' => 'Sentra Kuliner Pesisir Marina Labuan Bajo',
            ],
            'free_souvenir' => [
                'name' => '1 Pouch Kopi Manggarai Flores & 1 Lembar Kain Tenun Ikat Songke',
                'vendor' => 'Koperasi Pengrajin Tenun Adat Manggarai Barat',
                'badge' => 'Gratis Termasuk Tanpa Add-On (Rp 0)',
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&q=80',
                'description' => 'Bingkisan otentik tenun tangan warga desa adat Flores langsung ke tanganmu.',
            ],
            'gallery' => [
                'destinations' => [
                    ['url' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Puncak Trekking Pulau Padar Tiga Teluk', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Pink Beach Pasir Merah Muda & Coral Reef', 'category' => 'Spot Wisata'],
                ],
                'accommodation' => [
                    ['url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Kabin Nyaman AC KM Phinisi Bajo Explorer', 'category' => 'Penginapan'],
                    ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Sundeck Santai Sunset di Atas Kapal', 'category' => 'Penginapan'],
                ],
                'vehicle' => [
                    ['url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Kemegahan Kapal Phinisi Tradisional Kayu Ulin', 'category' => 'Kendaraan'],
                ],
                'food' => [
                    ['url' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Ikan Bakar Sambal Dabu-Dabu Khas NTT', 'category' => 'Makanan'],
                    ['url' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Cumi Saus Asam Manis Pedas Segar', 'category' => 'Makanan'],
                ],
            ],
            'meals' => [
                'included_note' => 'Termasuk 6x Makan Spesial Seafood Segar & Kuliner Flores (Bebas pilih varian)',
                'options' => [
                    [
                        'session' => 'Sarapan (Breakfast)',
                        'menu_choices' => [
                            'Pancake Pisang Flores & Madu Hutan Asli',
                            'Nasi Goreng Seafood Cumi Segar',
                            'Omelette Sayuran Organik & Roti Panggang',
                        ],
                    ],
                    [
                        'session' => 'Makan Siang (Lunch)',
                        'menu_choices' => [
                            'Ikan Kerapu Bakar Sambal Dabu-Dabu Khas NTT',
                            'Cumi Saus Asam Manis Pedas Tangkapan Nelayan Bajo',
                            'Menu Sehat: Capcay Seafood & Tahu Tempe Bakar Madu',
                        ],
                    ],
                    [
                        'session' => 'Makan Malam (Dinner)',
                        'menu_choices' => [
                            'Ikan Kuah Asam Segar Rempah Flores',
                            'Ayam Bakar Bumbu Taliwang Bajo',
                            'Tumis Sayur Rumpu Rampe Khas Manggarai Barat',
                        ],
                    ],
                ],
            ],
            'highlights' => ['Trekking Pulau Padar', 'Manta Point Snorkeling', 'Pink Beach', 'Sunset Pulau Kalong'],
        ],
        [
            'id' => 3,
            'title' => 'Eksotisme Budaya Toraja & Rammang-Rammang Karst',
            'location' => 'Tana Toraja & Maros, Sulawesi Selatan',
            'region' => 'Sulawesi',
            'category' => 'Budaya & Desa Adat',
            'trip_type' => ['open', 'private'],
            'duration' => '4H 3M',
            'price' => 1750000,
            'private_price' => 2400000,
            'rating' => 4.8,
            'review_count' => 64,
            'slots_left' => 6,
            'quota' => 12,
            'difficulty' => 'Sedang',
            'departure_date' => '02 Okt 2026',
            'image' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80',
            'akamsi' => [
                'name' => 'Kak Pongtorra',
                'badge' => 'Akamsi Lokal Terverifikasi',
                'experience' => 'Tetua Adat Kete Kesu',
            ],
            'travel_partner' => [
                'name' => 'Toraja Celebes Heritage Tour',
                'logo' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=120&q=80',
                'badge' => 'Operator Adat Terakreditasi',
                'license' => 'Izin TDUP No. 41/DISPAR-TORAJA/2023',
                'rating' => 4.8,
                'trips_completed' => '210+ Trip Adat',
            ],
            'accommodation' => [
                'name' => 'Tongkonan Heritage Lodge Rantepao',
                'logo' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Koperasi Adat Kete Kesu (Mama Yuliana)',
                'room_type' => 'Arsitektur Rumah Tradisional Toraja Kayu Berukir',
                'facilities' => 'Air Hangat, Balkon View Lembah Hijau, Kopi Arabika Toraja Gratis',
                'description' => 'Menginap di komplek Tongkonan sakral berukir asli dengan udara pegunungan Toraja yang syahdu.',
            ],
            'vehicle' => [
                'name' => 'Toyota HiAce Commuter VIP AC & Perahu Klotok Karst',
                'logo' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Paguyuban Transportasi Toraja - Maros (Daeng Amir)',
                'type' => 'Minibus AC Nyaman + Perahu Kayu Wisata Rammang-Rammang',
                'capacity' => 'Maksimal 12 Peserta',
            ],
            'restaurant_umkm' => [
                'name' => 'Dapur Adat Kete Kesu & Rumah Makan Pongtiku',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'UMKM Dapur Tetua Adat Toraja',
                'specialty' => 'Pa\'piong Bambu Rempah Mayana & Coto Makassar Kuah Kacang',
                'address' => 'Jalur Wisata Adat Rantepao Toraja Utara',
            ],
            'free_souvenir' => [
                'name' => '1 Pack Kopi Arabika Toraja Sapan (250g) & Kue Khas Deppa Tori',
                'vendor' => 'Koperasi Petani Kopi Sapan Toraja Utara',
                'badge' => 'Gratis Termasuk Tanpa Add-On (Rp 0)',
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&q=80',
                'description' => 'Biji kopi petik merah single origin kelas dunia langsung dari petani lokal.',
            ],
            'gallery' => [
                'destinations' => [
                    ['url' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Desa Adat Kete Kesu & Deretan Rumah Tongkonan Purba', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Gua Tebing Londa & Karst Rammang-Rammang', 'category' => 'Spot Wisata'],
                ],
                'accommodation' => [
                    ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Kamar Tidur Kayu Jati Bernuansa Tradisional Toraja', 'category' => 'Penginapan'],
                ],
                'vehicle' => [
                    ['url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Toyota HiAce VIP AC Kapasitas Longgar', 'category' => 'Kendaraan'],
                ],
                'food' => [
                    ['url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Pa\'piong Ayam Bambu Masak Rempah Mayana', 'category' => 'Makanan'],
                ],
            ],
            'meals' => [
                'included_note' => 'Termasuk 8x Makan Masakan Otentik Toraja & Makassar',
                'options' => [
                    [
                        'session' => 'Sarapan (Breakfast)',
                        'menu_choices' => [
                            'Kopi Arabika Toraja & Kue Tradisional Deppa Tori',
                            'Nasi Kuning Bugis Sambal Ikan Cakalang',
                            'Bubur Bassang Khas Sulawesi Selatan',
                        ],
                    ],
                    [
                        'session' => 'Makan Siang (Lunch)',
                        'menu_choices' => [
                            'Pa\'piong Ayam Daging Bambu Masak Rempah Mayana',
                            'Ikan Mas Bakar Danau Tempe Sambal Katokkon Toraja',
                            'Pantollo Duri Sayur Pakis Bumbu Hitam Kluwek',
                        ],
                    ],
                    [
                        'session' => 'Makan Malam (Dinner)',
                        'menu_choices' => [
                            'Coto Makassar Kuah Kacang Rempah Otentik & Ketupat',
                            'Konro Bakar Daging Sapi Empuk Saus Kacang',
                            'Sup Brenebon Kacang Merah Gurih Segar',
                        ],
                    ],
                ],
            ],
            'highlights' => ['Londa Cave', 'Kete Kesu', 'Gua Karst Purba', 'Kebun Kopi Sapan'],
        ],
        [
            'id' => 4,
            'title' => 'Tumpak Sewu Waterfall & Kawah Ijen Blue Fire Experience',
            'location' => 'Lumajang & Banyuwangi, Jawa Timur',
            'region' => 'Jawa',
            'category' => 'Alam & Gunung',
            'trip_type' => ['open', 'private'],
            'duration' => '3H 2M',
            'price' => 890000,
            'private_price' => 1250000,
            'rating' => 4.9,
            'review_count' => 110,
            'slots_left' => 2,
            'quota' => 10,
            'difficulty' => 'Menantang',
            'departure_date' => '09 Okt 2026',
            'image' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=800&q=80',
            'akamsi' => [
                'name' => 'Cak Ilham Osing',
                'badge' => 'Akamsi Lokal Terverifikasi',
                'experience' => 'Penambang & Ranger Ijen',
            ],
            'travel_partner' => [
                'name' => 'East Java Discovery Ranger Tour',
                'logo' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=120&q=80',
                'badge' => 'Sertifikasi Ranger Geopark Ijen',
                'license' => 'Izin TDUP No. 77/DISPAR-BWI/2023',
                'rating' => 4.9,
                'trips_completed' => '520+ Pendakian',
            ],
            'accommodation' => [
                'name' => 'Kampoeng Osing Resort & Glamping',
                'logo' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Kelompok Sadar Wisata Kemiren (Pak Sugeng Banyuwangi)',
                'room_type' => 'Bungalow Tradisional Suku Osing Kayu Jati',
                'facilities' => 'Water Heater, Masker Gas Respirator Standar Ijen, Kolam Alami',
                'description' => 'Resort ramah lingkungan bergaya rumah adat Osing di kaki Gunung Ijen yang sejuk asri.',
            ],
            'vehicle' => [
                'name' => 'Isuzu Elf Long Pariwisata AC & Trooper 4x4 Ijen',
                'logo' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Paguyuban Pengemudi Wisata Blambangan (Cak Wawan)',
                'type' => 'Microbus Nyaman & 4x4 Pendakian Pos Paltuding',
                'capacity' => 'Maksimal 10 Peserta',
            ],
            'restaurant_umkm' => [
                'name' => 'Warung Sego Tempong Mbok Wah & Pecel Pithik Kemiren',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'UMKM Kuliner Legendaris Kemiren',
                'specialty' => 'Pecel Pithik Kelapa Parut Osing & Sego Tempong Pedas',
                'address' => 'Desa Adat Kemiren, Glagah, Banyuwangi',
            ],
            'free_souvenir' => [
                'name' => '1 Box Kue Pia Glenmore & 1 Pouch Kopi Osing Banyuwangi',
                'vendor' => 'Sentra Oleh-Oleh Tradisional Blambangan',
                'badge' => 'Gratis Termasuk Tanpa Add-On (Rp 0)',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
                'description' => 'Pia khas Banyuwangi renyah dan kopi sangrai lokal aroma wangi mantap.',
            ],
            'gallery' => [
                'destinations' => [
                    ['url' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Tirai Megah Air Terjun Tumpak Sewu Nirwana', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df8?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Blue Fire Api Biru Langka Kawah Ijen', 'category' => 'Spot Wisata'],
                ],
                'accommodation' => [
                    ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Bungalow Kayu Jati Kampung Osing Asri', 'category' => 'Penginapan'],
                ],
                'vehicle' => [
                    ['url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Trooper 4x4 Khusus Rute Ekstrem Paltuding Ijen', 'category' => 'Kendaraan'],
                ],
                'food' => [
                    ['url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Pecel Pithik Ayam Bakar Urap Kelapa Khas Osing', 'category' => 'Makanan'],
                ],
            ],
            'meals' => [
                'included_note' => 'Termasuk 5x Makan Kuliner Khas Banyuwangi & Lumajang',
                'options' => [
                    [
                        'session' => 'Sarapan (Breakfast)',
                        'menu_choices' => [
                            'Nasi Tempong Pedas Nampol Ikan Asin & Dadar Jagung',
                            'Nasi Campur Osing Sambal Teri Pete',
                            'Bubur Madura Manis Santan Gurih',
                        ],
                    ],
                    [
                        'session' => 'Makan Siang (Lunch)',
                        'menu_choices' => [
                            'Pecel Pithik Ayam Kampung Panggang Kelapa Parut Osing',
                            'Rujak Soto Khas Banyuwangi (Perpaduan Rujak & Soto Daging)',
                            'Ayam Betutu Banyuwangi Rempah Komplit',
                        ],
                    ],
                    [
                        'session' => 'Makan Malam (Dinner)',
                        'menu_choices' => [
                            'Sego Cawuk Kuah Trancam Khas Suku Osing',
                            'Sop Iga Sapi Rempah Hangat Banyuwangi',
                            'Urap Sayur Segar & Tempe Tahu Bacem Tradisional',
                        ],
                    ],
                ],
            ],
            'highlights' => ['Coban Sewu', 'Blue Fire Fenomena', 'Kawah Asam Ijen', 'Hutan De Djawatan'],
        ],
        [
            'id' => 5,
            'title' => 'Raja Ampat Pianemo & Wayag Gugusan Karst Surga',
            'location' => 'Waisai, Raja Ampat, Papua Barat Daya',
            'region' => 'Papua & Maluku',
            'category' => 'Bahari & Kepulauan',
            'trip_type' => ['open', 'private'],
            'duration' => '5H 4M',
            'price' => 4850000,
            'private_price' => 6450000,
            'rating' => 5.0,
            'review_count' => 52,
            'slots_left' => 4,
            'quota' => 8,
            'difficulty' => 'Sedang',
            'departure_date' => '15 Okt 2026',
            'image' => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=800&q=80',
            'akamsi' => [
                'name' => 'Pace Ronald Maya',
                'badge' => 'Akamsi Lokal Terverifikasi',
                'experience' => 'Tetua Adat Suku Maya',
            ],
            'travel_partner' => [
                'name' => 'Papua Paradise Expedition Tour',
                'logo' => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=120&q=80',
                'badge' => 'Operator Berizin Kawasan Konservasi',
                'license' => 'Izin BLUD UPTD Raja Ampat No. 008/BLUD/2023',
                'rating' => 5.0,
                'trips_completed' => '160+ Ekspedisi',
            ],
            'accommodation' => [
                'name' => 'Pianemo Eco Water Bungalow di Atas Laut',
                'logo' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Masyarakat Adat Kampung Sawinggrai (Mama Ruth)',
                'room_type' => 'Kabin Terapung Tradisional Kayu Besi di Atas Terumbu Karang',
                'facilities' => 'Direct Ocean Access, Listrik Tenaga Surya, Hammock Balkon',
                'description' => 'Water cottage terapung tepat di atas terumbu karang alami, dengarkan debur ombak dan ikan berenang langsung di bawah kamar.',
            ],
            'vehicle' => [
                'name' => 'Speedboat Fiber Yamaha 2x200 PK Khusus Kepulauan',
                'logo' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Koperasi Transportasi Laut Bahari Raja Ampat',
                'type' => 'Kapal Cepat Berkanopi Nyaman & Alat Pelampung Lengkap',
                'capacity' => 'Maksimal 8 Peserta',
            ],
            'restaurant_umkm' => [
                'name' => 'Dapur Pesisir Kampung Sawinggrai & Mama Papua',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'UMKM Dapur Ibu-Ibu Adat Suku Maya',
                'specialty' => 'Ikan Tenggiri Colo-Colo Segar & Papeda Kuah Kuning Rempah',
                'address' => 'Kampung Wisata Sawinggrai Raja Ampat',
            ],
            'free_souvenir' => [
                'name' => '1 Pack Abon Gulung Tuna Papua & Noken Rajut Asli',
                'vendor' => 'Sanggar Kerajinan Noken Mama Papua',
                'badge' => 'Gratis Termasuk Tanpa Add-On (Rp 0)',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
                'description' => 'Noken warisan budaya UNESCO yang dirajut tangan penuh kasih oleh mama-mama Papua.',
            ],
            'gallery' => [
                'destinations' => [
                    ['url' => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Gugusan Bukit Karst Pianemo Raja Ampat', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Laguna Biru Toska Telaga Bintang', 'category' => 'Spot Wisata'],
                ],
                'accommodation' => [
                    ['url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Bungalow Terapung Kayu Ulin di Atas Laut', 'category' => 'Penginapan'],
                ],
                'vehicle' => [
                    ['url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Speedboat Fiber Cepat Menembus Laut Lepas', 'category' => 'Kendaraan'],
                ],
                'food' => [
                    ['url' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Ikan Bakar Colo-Colo Tangkapan Segar', 'category' => 'Makanan'],
                ],
            ],
            'meals' => [
                'included_note' => 'Termasuk 12x Makan Seafood Segar Tangkapan Hari Ini & Olahan Lokal',
                'options' => [
                    [
                        'session' => 'Sarapan (Breakfast)',
                        'menu_choices' => [
                            'Roti Bakar Selai Kelapa Segar & Kopi Robusta Papua',
                            'Nasi Goreng Ikan Tuna Asap',
                            'Pancake Buah Tropis Pepaya & Nanas Raja Ampat',
                        ],
                    ],
                    [
                        'session' => 'Makan Siang (Lunch)',
                        'menu_choices' => [
                            'Ikan Tenggiri Bakar Sambal Colo-Colo Pedas Segar',
                            'Papeda Ikan Tongkol Kuah Kuning Rempah Otentik',
                            'Cumi Bakar Kecap Bawang Cabai Rawit',
                        ],
                    ],
                    [
                        'session' => 'Makan Malam (Dinner)',
                        'menu_choices' => [
                            'Kepiting Bakau Saus Padang Manis Pedas',
                            'Ayam Woku Belanga Khas Indonesia Timur',
                            'Tumis Daun Singkong Bunga Pepaya Tidak Pahit',
                        ],
                    ],
                ],
            ],
            'highlights' => ['Pianemo Lookout', 'Telaga Bintang', 'Pasir Timbul', 'Desa Sawinggrai Cendrawasih'],
        ],
        [
            'id' => 6,
            'title' => 'Dieng Plateau Negeri di Atas Awan & Telaga Warna',
            'location' => 'Wonosobo & Banjarnegara, Jawa Tengah',
            'region' => 'Jawa',
            'category' => 'Budaya & Desa Adat',
            'trip_type' => ['open', 'private'],
            'duration' => '2H 1M',
            'price' => 420000,
            'private_price' => 580000,
            'rating' => 4.8,
            'review_count' => 86,
            'slots_left' => 5,
            'quota' => 14,
            'difficulty' => 'Mudah',
            'departure_date' => '18 Okt 2026',
            'image' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=800&q=80',
            'akamsi' => [
                'name' => 'Mas Aji Dieng',
                'badge' => 'Akamsi Lokal Terverifikasi',
                'experience' => 'Pemandu Prau & Sikunir',
            ],
            'travel_partner' => [
                'name' => 'Dieng Highland Explorer Tour',
                'logo' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=120&q=80',
                'badge' => 'Operator Resmi Pegunungan Dieng',
                'license' => 'Izin TDUP No. 19/PAR-WSB/2023',
                'rating' => 4.8,
                'trips_completed' => '640+ Trip Dieng',
            ],
            'accommodation' => [
                'name' => 'Oemah Kayu Sikunir Panorama Homestay',
                'logo' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Asosiasi Homestay Desa Sembungan (Ibu Trisna)',
                'room_type' => 'Kamar Hangat Lantai Kayu Pemandangan Telaga Cebong',
                'facilities' => 'Water Heater Gas Panas Maksimal, Perapian Kayu, Teh Purwaceng Hangat',
                'description' => 'Homestay hangat di desa tertinggi di Pulau Jawa dengan view danau dan gunung yang spektakuler.',
            ],
            'vehicle' => [
                'name' => 'Microbus Isuzu Elf Pariwisata Dieng & Shuttle Mini',
                'logo' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'Paguyuban Angkutan Wisata Dieng Wonosobo (Mas Joko)',
                'type' => 'Armada Wisata Berpenggerak Kuat Tanjakan Dieng',
                'capacity' => 'Maksimal 14 Peserta',
            ],
            'restaurant_umkm' => [
                'name' => 'Warung Mie Ongklok Pak Muh & Dapur Sikunir',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80',
                'vendor' => 'UMKM Mie Ongklok Tradisional Sejak 1978',
                'specialty' => 'Mie Ongklok Kuah Kental Ebi & Tempe Kemul Krispi Hangat',
                'address' => 'Pusat Kuliner Wonosobo & Sembungan',
            ],
            'free_souvenir' => [
                'name' => '1 Box Manisan Buah Carica Dieng Segar & 1 Pack Keripik Jamur',
                'vendor' => 'Sentra UMKM Pengolahan Carica Dieng Wonosobo',
                'badge' => 'Gratis Termasuk Tanpa Add-On (Rp 0)',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
                'description' => 'Carica buah khas dataran tinggi yang manis segar kaya vitamin siap bawa pulang.',
            ],
            'gallery' => [
                'destinations' => [
                    ['url' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Golden Sunrise Bukit Sikunir di Atas Awan', 'category' => 'Spot Wisata'],
                    ['url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Telaga Warna Berubah Tiga Warna Alami', 'category' => 'Spot Wisata'],
                ],
                'accommodation' => [
                    ['url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Kamar Tidur Hangat Dinding Kayu Panorama Desa', 'category' => 'Penginapan'],
                ],
                'vehicle' => [
                    ['url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Shuttle Wisata Elf Nyaman AC Tanjakan Dieng', 'category' => 'Kendaraan'],
                ],
                'food' => [
                    ['url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1200&q=85', 'caption' => 'Mie Ongklok Asli Wonosobo & Sate Sapi Bumbu Kacang', 'category' => 'Makanan'],
                ],
            ],
            'meals' => [
                'included_note' => 'Termasuk 3x Makan Kuliner Khas Dataran Tinggi Dieng',
                'options' => [
                    [
                        'session' => 'Sarapan (Breakfast)',
                        'menu_choices' => [
                            'Mie Ongklok Asli Wonosobo & Sate Sapi Bumbu Kacang',
                            'Nasi Megono Khas Wonosobo Telur Rebus',
                            'Bubur Ayam Kampung Hangat Suhu Pegunungan',
                        ],
                    ],
                    [
                        'session' => 'Makan Siang (Lunch)',
                        'menu_choices' => [
                            'Ayam Goreng Lengkuas Rempah & Tempe Kemul Krispi Hangat',
                            'Sop Iga Sapi Bening Wortel Kentang Dieng',
                            'Paket Sayur Jamur Tiram Dieng & Sambal Bajak Hijau',
                        ],
                    ],
                    [
                        'session' => 'Makan Malam (Dinner)',
                        'menu_choices' => [
                            'Bakso Kuah Sapi Panas Rempah Bawang Goreng',
                            'Nasi Goreng Magelangan Spesial Babat/Ayam',
                            'Wedang Ronde Jahe Hangat & Tempe Mendoan Panas',
                        ],
                    ],
                ],
            ],
            'highlights' => ['Sunrise Golden Hour Sikunir', 'Kawah Sikidang', 'Candi Arjuna', 'Telaga Warna'],
        ],
    ];
};

Route::get('/', function () use ($getAllTrips) {
    return Inertia::render('Welcome', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
        'tripCategories' => [
            'Semua Kategori',
            'Alam & Gunung',
            'Bahari & Kepulauan',
            'Budaya & Desa Adat',
            'Healing & Wellness',
        ],
        'featuredTrips' => $getAllTrips(),
        'poKulinerItems' => [
            [
                'id' => 1,
                'title' => 'Bakpia Kukus Tugu Jogja Premium Box (Isi 10)',
                'origin' => 'Yogyakarta, D.I. Yogyakarta',
                'price' => 48000,
                'rating' => 4.9,
                'sold' => '1.4k+ dipesan',
                'close_date' => 'Tutup PO 2 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80',
                'partner' => 'UMKM Dapur Malioboro',
                'badge' => 'Oleh-Oleh Terfavorit',
            ],
            [
                'id' => 2,
                'title' => 'Pie Susu Dhian Asli Bali Box 50 Pcs Fresh Baked',
                'origin' => 'Denpasar, Bali',
                'price' => 115000,
                'rating' => 5.0,
                'sold' => '3.8k+ dipesan',
                'close_date' => 'Tutup PO Besok 18:00',
                'image' => 'https://images.unsplash.com/photo-1557082673-e962804c8651?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Pabrik Pie Dhian Bali',
                'badge' => 'Langsung dari Oven',
            ],
            [
                'id' => 3,
                'title' => 'Pempek Candy Palembang Paket Lenjer & Kapal Selam + Cuko',
                'origin' => 'Palembang, Sumatera Selatan',
                'price' => 165000,
                'rating' => 4.9,
                'sold' => '890+ dipesan',
                'close_date' => 'Tutup PO 3 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Sentra Pempek Ilir',
                'badge' => 'Ikan Tenggiri Murni',
            ],
            [
                'id' => 4,
                'title' => 'Lapis Legit Harum Pontianak Wijsman Recipe',
                'origin' => 'Pontianak, Kalimantan Barat',
                'price' => 220000,
                'rating' => 4.9,
                'sold' => '420+ dipesan',
                'close_date' => 'Tutup PO 4 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Kue Tradisi Khatulistiwa',
                'badge' => 'Butter Premium',
            ],
        ],
    ]);
})->name('home');

Route::get('/gabung-mitra', function () {
    return Inertia::render('Mitra/Join', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
        'stats' => [
            'total_guides' => '540+ Akamsi',
            'total_merchants' => '1.250+ UMKM',
            'revenue_distributed' => 'Rp 4,8 Miliar+',
            'coverage_provinces' => '29 Provinsi',
        ],
        'partnerTypes' => [
            [
                'id' => 'akamsi-guide',
                'title' => 'Pemandu Lokal (Akamsi)',
                'badge' => 'Paling Populer',
                'desc' => 'Untuk pemuda desa, porter, ranger taman nasional, dan penjelajah lokal yang siap memandu open trip otentik.',
                'commission' => 'Hingga 88% hasil trip langsung ke kantongmu',
            ],
            [
                'id' => 'kuliner-merchant',
                'title' => 'Merchant Kuliner & Oleh-Oleh',
                'badge' => 'Permintaan Tinggi',
                'desc' => 'Produsen oleh-oleh, dapur rumahan legendaris, dan sentra camilan khas daerah untuk sistem Open PO titip belanja.',
                'commission' => 'Tanpa potongan listing, bayar saat pesanan lunas',
            ],
            [
                'id' => 'transport-stay',
                'title' => 'Homestay & Transportasi Lokal',
                'badge' => 'Mitra Komunitas',
                'desc' => 'Pemilik Jeep 4x4, sewa motor trail, perahu klotok/kapal nelayan, serta penginapan tradisional & glamping warga.',
                'commission' => 'Jadwal sewa terintegrasi langsung dengan open trip',
            ],
        ],
    ]);
})->name('mitra.join');

Route::get('/open-trip', function () use ($getAllTrips) {
    return Inertia::render('Trip/Index', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
        'regions' => ['Semua Wilayah', 'Jawa', 'Bali & Nusa Tenggara', 'Sulawesi', 'Papua & Maluku'],
        'trips' => $getAllTrips(),
    ]);
})->name('trip.index');

Route::get('/trip/{id}', function ($id) use ($getAllTrips) {
    $trips = $getAllTrips();
    $trip = collect($trips)->firstWhere('id', (int) $id);

    if (! $trip) {
        abort(404, 'Paket Trip tidak ditemukan.');
    }

    $relatedTrips = collect($trips)->where('id', '!=', (int) $id)->take(3)->values();

    return Inertia::render('Trip/Show', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
        'trip' => $trip,
        'relatedTrips' => $relatedTrips,
    ]);
})->name('trip.show');

Route::get('/open-po', function () {
    return Inertia::render('PoKuliner/Index', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
        'categories' => ['Semua Kategori', 'Kue & Pastry', 'Camilan Tradisional', 'Makanan Khas Siap Saji', 'Kopi & Minuman Lokal'],
        'items' => [
            [
                'id' => 1,
                'title' => 'Bakpia Kukus Tugu Jogja Premium Box (Isi 10)',
                'category' => 'Kue & Pastry',
                'origin' => 'Yogyakarta, D.I. Yogyakarta',
                'price' => 48000,
                'rating' => 4.9,
                'sold' => '1.4k+ dipesan',
                'close_date' => 'Tutup PO 2 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=600&q=80',
                'partner' => 'UMKM Dapur Malioboro',
                'badge' => 'Oleh-Oleh Terfavorit',
                'shelf_life' => '7 Hari (Suhu Ruang)',
            ],
            [
                'id' => 2,
                'title' => 'Pie Susu Dhian Asli Bali Box 50 Pcs Fresh Baked',
                'category' => 'Kue & Pastry',
                'origin' => 'Denpasar, Bali',
                'price' => 115000,
                'rating' => 5.0,
                'sold' => '3.8k+ dipesan',
                'close_date' => 'Tutup PO Besok 18:00',
                'image' => 'https://images.unsplash.com/photo-1557082673-e962804c8651?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Pabrik Pie Dhian Bali',
                'badge' => 'Langsung dari Oven',
                'shelf_life' => '14 Hari',
            ],
            [
                'id' => 3,
                'title' => 'Pempek Candy Palembang Paket Lenjer & Kapal Selam + Cuko',
                'category' => 'Makanan Khas Siap Saji',
                'origin' => 'Palembang, Sumatera Selatan',
                'price' => 165000,
                'rating' => 4.9,
                'sold' => '890+ dipesan',
                'close_date' => 'Tutup PO 3 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Sentra Pempek Ilir',
                'badge' => 'Ikan Tenggiri Murni',
                'shelf_life' => 'Vacuum Pack 3 Bulan (Beku)',
            ],
            [
                'id' => 4,
                'title' => 'Lapis Legit Harum Pontianak Wijsman Recipe',
                'category' => 'Kue & Pastry',
                'origin' => 'Pontianak, Kalimantan Barat',
                'price' => 220000,
                'rating' => 4.9,
                'sold' => '420+ dipesan',
                'close_date' => 'Tutup PO 4 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Kue Tradisi Khatulistiwa',
                'badge' => 'Butter Premium',
                'shelf_life' => '10 Hari',
            ],
            [
                'id' => 5,
                'title' => 'Kopi Arabika Toraja Sapan Single Origin Beans (250g)',
                'category' => 'Kopi & Minuman Lokal',
                'origin' => 'Toraja Utara, Sulawesi Selatan',
                'price' => 85000,
                'rating' => 5.0,
                'sold' => '650+ dipesan',
                'close_date' => 'Tutup PO 5 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Koperasi Petani Sapan',
                'badge' => 'Petik Merah Pilihan',
                'shelf_life' => '6 Bulan',
            ],
            [
                'id' => 6,
                'title' => 'Bika Ambon Zulaikha Daun Jeruk Asli Medan',
                'category' => 'Kue & Pastry',
                'origin' => 'Medan, Sumatera Utara',
                'price' => 95000,
                'rating' => 4.8,
                'sold' => '1.1k+ dipesan',
                'close_date' => 'Tutup PO 3 hari lagi',
                'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=600&q=80',
                'partner' => 'Dapur Tradisi Melayu',
                'badge' => 'Resep Turun Temurun',
                'shelf_life' => '4 Hari',
            ],
        ],
    ]);
})->name('po.index');

Route::get('/tentang-kami', function () {
    return Inertia::render('About/Index', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
        'hubs' => [
            ['city' => 'Denpasar, Bali', 'address' => 'Jl. Danau Beratan No. 11, Sanur', 'lead' => 'Gede Ariawan (Lead Nusa Tenggara)'],
            ['city' => 'Jakarta Selatan', 'address' => 'Kemang Timur Raya No. 45', 'lead' => 'Sarah Amanda (Partnership Director)'],
            ['city' => 'Yogyakarta', 'address' => 'Jl. Prawirotaman II No. 18', 'lead' => 'Bagus Wicaksono (Lead Jawa & Budaya)'],
            ['city' => 'Labuan Bajo, NTT', 'address' => 'Kompleks Marina Bajo No. 3', 'lead' => 'Bang Mansur (Lead Bahari Flores)'],
        ],
    ]);
})->name('about.index');

Route::get('/wishlist', function () {
    return Inertia::render('Wishlist/Index', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
    ]);
})->name('wishlist.index');

Route::get('/blog', function () {
    return Inertia::render('Blog/Index', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
    ]);
})->name('blog.index');

Route::get('/member-tiers', function () {
    return Inertia::render('MemberTiers/Index', [
        'appName' => config('app.name', 'TapakLokal'),
        'user' => [
            'name' => 'albnnaardy',
            'handle' => '@petualangnyasar',
            'points' => 0,
            'avatar' => null,
            'tier' => 'Petualang Perintis',
        ],
    ]);
})->name('tiers.index');
