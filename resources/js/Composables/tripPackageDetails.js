export function tripPackageFacilities(trip) {
    const experience = trip.experience || {};
    const details = Array.isArray(experience.facilityDetails) ? experience.facilityDetails : [];
    const titles = new Set(details.map(item => item.title.trim().toLowerCase()));
    const additional = (experience.included || experience.includes || []).filter(title => !titles.has(title.trim().toLowerCase()));
    return [...details, ...additional.map(title => ({ category: 'Layanan', title, note: '' }))];
}

export function tripPackageExclusions(trip) {
    return trip.experience?.excluded || trip.experience?.excludes || [];
}
