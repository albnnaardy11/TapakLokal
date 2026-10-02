export const formatSouvenirPrice = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
export const souvenirPhotoStyle = product => product.imageUrl ? { backgroundImage: `url('${encodeURI(product.imageUrl)}')`, backgroundPosition: 'center', backgroundSize: 'cover' } : { backgroundImage: 'none', backgroundColor: '#edf2f7' };
