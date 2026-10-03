export function tripPackageFacilities(trip, isPrivateTrip = trip.type === 'private') {
    const defaults = isPrivateTrip ? [
    { category: 'Transportasi', title: 'Kapal privat selama perjalanan', note: 'Sesuai rute dan jumlah peserta yang dipilih.' },
    { category: 'Transportasi', title: 'Penjemputan dari hotel area Labuan Bajo', note: 'Untuk lokasi yang tercakup dalam area layanan.' },
    { category: 'Akomodasi', title: 'Kabin kapal atau homestay', note: 'Sesuai pilihan paket perjalanan.' },
    { category: 'Makan', title: 'Makan sesuai itinerary', note: 'Sarapan, makan siang, dan makan malam sesuai program.' },
    { category: 'Aktivitas', title: 'Tiket aktivitas utama', note: 'Termasuk aktivitas yang tercantum pada itinerary.' },
    { category: 'Aktivitas', title: 'Alat snorkeling', note: 'Masker, snorkel, dan pelampung tersedia.' },
    { category: 'Layanan', title: 'Pemandu lokal berpengalaman', note: 'Mendampingi rombongan selama aktivitas utama.' },
    { category: 'Layanan', title: 'Dokumentasi perjalanan', note: 'Dokumentasi dasar untuk momen pilihan trip.' },
] : [
    { category: 'Transportasi', title: 'Penyeberangan kapal pulang-pergi', note: 'Dermaga Kaliadem menuju Kepulauan Seribu.' },
    { category: 'Transportasi', title: 'Transportasi lokal sesuai rute', note: 'Untuk perpindahan yang tercantum pada itinerary.' },
    { category: 'Akomodasi', title: 'Homestay selama 1 malam', note: 'Kamar dan fasilitas dasar sesuai paket.' },
    { category: 'Makan', title: 'Makan 3 kali', note: 'Makan siang, makan malam, dan sarapan.' },
    { category: 'Makan', title: 'Air mineral selama perjalanan', note: 'Tersedia pada aktivitas utama.' },
    { category: 'Aktivitas', title: 'Kapal hopping island & snorkeling', note: 'Termasuk perjalanan menuju spot aktivitas.' },
    { category: 'Aktivitas', title: 'Alat snorkeling', note: 'Masker, snorkel, dan pelampung tersedia.' },
    { category: 'Layanan', title: 'Guide lokal dan P3K', note: 'Pendampingan dan perlengkapan pertolongan pertama.' },
    { category: 'Layanan', title: 'Dokumentasi eksklusif', note: 'Dokumentasi pilihan selama trip berlangsung.' },
];
    if (Array.isArray(trip.experience?.included) && trip.experience.included.length) {
        return trip.experience.included.map(title => ({ category: 'Layanan', title, note: '' }));
    }
    return defaults;
}

export function tripPackageExclusions(trip) {
    return Array.isArray(trip.experience?.excluded) && trip.experience.excluded.length
        ? trip.experience.excluded
        : ['Transportasi menuju meeting point', 'Pengeluaran pribadi', 'Makan di luar program', 'Tiket aktivitas opsional', 'Asuransi perjalanan pribadi'];
}
