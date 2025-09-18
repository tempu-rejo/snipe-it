/**
 * Custom Asset Image Formatter dengan Category Fallback
 * Formatter khusus untuk menampilkan gambar asset dengan default berdasarkan kategori
 */

/**
 * Asset Image Formatter dengan category fallback
 * @param {string} value - URL gambar asset
 * @param {object} row - Data row dari bootstrap table
 * @param {number} index - Index row
 * @returns {string} HTML img tag
 */
function assetImageFormatter(value, row, index) {
    // Ambil kategori dari model.category.name atau fallback
    let categoryName = '';
    if (row.model && row.model.category && row.model.category.name) {
        categoryName = row.model.category.name;
    } else if (row.category && row.category.name) {
        categoryName = row.category.name;
    }
    
    let imageUrl = value;
    let isDefaultImage = false;
    
    // Fungsi untuk check apakah URL gambar valid
    function isValidImageValue(val) {
        if (!val || val === '' || val === null || val === 'null' || val === 'undefined') {
            return false;
        }
        // Check jika mengandung ekstensi gambar atau protocol
        return val.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?.*)?$/i) || 
               val.startsWith('http') || 
               val.startsWith('//') ||
               val.startsWith('data:image/');
    }
    
    // Hanya gunakan default jika benar-benar tidak ada gambar valid
    if (!isValidImageValue(value)) {
        imageUrl = getDefaultImageForCategory(categoryName);
        isDefaultImage = true;
    } else {
        // Buat URL lengkap jika relatif, tapi pertahankan yang sudah valid
        if (value && !value.startsWith('http') && !value.startsWith('//') && !value.startsWith('data:')) {
            imageUrl = window.App.baseUrl + '/' + value.replace(/^\/+/, '');
        }
    }
    
    // Generate img tag dengan data-category untuk fallback handler
    return `<img src="${imageUrl}" 
                 alt="Asset Image" 
                 class="hw-image" 
                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" 
                 data-category="${categoryName}"
                 data-is-default-image="${isDefaultImage}"
                 data-original-value="${value || ''}"
                 onerror="handleImageError(this, '${categoryName}')"
                 />`;
}

/**
 * Hardware Link Formatter yang sudah ada di sistem
 * Update untuk menggunakan serial text formatter
 */
function hardwareLinkFormatterFixed(value, row, index) {
    if (row.deleted_at) {
        return '<del>' + row.name + '</del>';
    } else if ((row.status_label) && (row.status_label.status_type=='undeployable')) {
        return '<strike>' + row.name + '</strike>';
    } else if ((row.status_label) && (row.status_label.status_type=='archived')) {
        return '<i class="fas fa-archive"></i> ' + row.name;
    }
    
    if (row.name) {
        return '<a href="hardware/' + row.id + '" data-tooltip="true" title="Asset Tag: ' + row.asset_tag + '">' + row.name + '</a>';
    }
    
    return value;
}

/**
 * Serial Number Formatter - pastikan ditampilkan sebagai text, bukan scientific notation
 */
function serialNumberFormatter(value, row, index) {
    if (!value) return '';
    
    // Konversi ke string dan pastikan ditampilkan penuh
    return '<span class="serial-number" style="font-family: monospace;">' + String(value) + '</span>';
}

// Make formatters available globally
window.assetImageFormatter = assetImageFormatter;
window.hardwareLinkFormatterFixed = hardwareLinkFormatterFixed;
window.serialNumberFormatter = serialNumberFormatter;