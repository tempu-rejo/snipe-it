/**
 * Category Image Fallback Handler
 * Handles default images for hardware categories when image fails to load
 */

// Category default images mapping
const categoryDefaultImages = {
    'Harddisk': 'https://uitrackin.ultid.com/uploads/accessories/accessory-image--Ki5TZDyrwS.jpg',
    'Storage Disk': 'https://uitrackin.ultid.com/uploads/accessories/accessory-image--Ki5TZDyrwS.jpg',
    'ViConf': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741668846.jpg',
    'GPS': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1747119928.jpg',
    'Raspberry': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742530358.jpg',
    'Checkpoint': 'https://uitrackin.ultid.com/uploads/assets/asset-image--3WtuuExFmD.jpg',
    'UPS Server': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1745820756.jpg',
    'Proyektor': 'https://uitrackin.ultid.com/uploads/assets/asset-image-4623-VF9Vn6ef1R.jpg',
    'DVR': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742530956.jpg',
    'Server': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742536188.png',
    'Weight Scale': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742528190.jpg',
    'Switch Managed': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742961430.jpg',
    'Handphone': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741576949.jpg',
    'Tablet': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742438431.jpg',
    'Mesin Finger': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741593252.jpg',
    'Access Point': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741837676.jpg',
    'Monitor/TV': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741594391.jpg',
    'PC Desktop': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1742960723.jpg',
    'Printer': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741668586.jpg',
    'Laptop': 'https://uitrackin.ultid.com/uploads/assets/device_image_upload1741658688.jpg'
};

/**
 * Get default image URL for category
 * @param {string} categoryName 
 * @returns {string}
 */
function getDefaultImageForCategory(categoryName) {
    if (!categoryName) {
        return window.App.baseUrl + '/uploads/assets/default.png';
    }
    
    // Exact match first
    if (categoryDefaultImages[categoryName]) {
        return categoryDefaultImages[categoryName];
    }
    
    // Partial match (case-insensitive)
    const categoryLower = categoryName.toLowerCase();
    for (const [category, imageUrl] of Object.entries(categoryDefaultImages)) {
        if (categoryLower.includes(category.toLowerCase()) || 
            category.toLowerCase().includes(categoryLower)) {
            return imageUrl;
        }
    }
    
    // Fallback to default
    return window.App.baseUrl + '/uploads/assets/default.png';
}

/**
 * Check if image URL is valid/exists
 * @param {string} url 
 * @returns {boolean}
 */
function isValidImageUrl(url) {
    if (!url || url === '' || url === 'null' || url === 'undefined') {
        return false;
    }
    
    // Check if it's a data URL
    if (url.startsWith('data:image/')) {
        return true;
    }
    
    // Check if it's a valid URL format
    try {
        new URL(url);
        return true;
    } catch (e) {
        // If not absolute URL, check if it's a valid relative path
        return url.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?.*)?$/i) !== null;
    }
}

/**
 * Handle image error and set default based on category
 * @param {HTMLImageElement} img 
 * @param {string} categoryName 
 */
function handleImageError(img, categoryName) {
    // Prevent infinite loop
    if (img.dataset.fallbackApplied === 'true') {
        return;
    }
    
    // Jangan replace jika ini sudah gambar default
    const currentSrc = img.src;
    const isAlreadyDefault = currentSrc.includes('/uploads/assets/default.png') ||
                           Object.values(categoryDefaultImages).some(url => currentSrc === url);
    
    if (!isAlreadyDefault) {
        img.dataset.fallbackApplied = 'true';
        img.src = getDefaultImageForCategory(categoryName);
        img.alt = 'Default image for ' + (categoryName || 'unknown category');
        img.dataset.isDefaultImage = 'true';
        
        console.log(`Image error for ${img.dataset.originalValue || 'unknown'}, falling back to category default: ${categoryName}`);
    }
}

/**
 * Set default image if src is empty
 * @param {HTMLImageElement} img 
 * @param {string} categoryName 
 */
function setDefaultImageIfEmpty(img, categoryName) {
    // Hanya set default jika benar-benar kosong atau invalid
    const isEmpty = !img.src || 
                   img.src === '' || 
                   img.src === window.location.href ||
                   img.src.endsWith('/') ||
                   img.src === 'null' ||
                   img.src === 'undefined' ||
                   !isValidImageUrl(img.src);
                   
    if (isEmpty) {
        img.src = getDefaultImageForCategory(categoryName);
        img.alt = 'Default image for ' + (categoryName || 'unknown category');
        img.dataset.isDefaultImage = 'true';
    }
}

/**
 * Initialize category image fallback for bootstrap table
 */
function initCategoryImageFallback() {
    // Handle existing images
    document.querySelectorAll('img[data-category]').forEach(img => {
        const category = img.dataset.category;
        
        // Set default if empty
        setDefaultImageIfEmpty(img, category);
        
        // Add error handler
        img.onerror = function() {
            handleImageError(this, category);
        };
    });
    
    // Handle dynamically loaded images (for bootstrap-table)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    const images = node.querySelectorAll ? node.querySelectorAll('img[data-category]') : [];
                    images.forEach(img => {
                        const category = img.dataset.category;
                        setDefaultImageIfEmpty(img, category);
                        img.onerror = function() {
                            handleImageError(this, category);
                        };
                    });
                }
            });
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategoryImageFallback);
} else {
    initCategoryImageFallback();
}

// Make functions available globally
window.getDefaultImageForCategory = getDefaultImageForCategory;
window.handleImageError = handleImageError;
window.setDefaultImageIfEmpty = setDefaultImageIfEmpty;
window.isValidImageUrl = isValidImageUrl;

/**
 * Utility function to check if current image is a default/fallback image
 * @param {HTMLImageElement} img 
 * @returns {boolean}
 */
window.isDefaultImage = function(img) {
    const currentSrc = img.src;
    return currentSrc.includes('/uploads/assets/default.png') ||
           Object.values(categoryDefaultImages).some(url => currentSrc === url) ||
           img.dataset.isDefaultImage === 'true';
};

/**
 * Utility function to restore original image if available
 * @param {HTMLImageElement} img 
 */
window.restoreOriginalImage = function(img) {
    const originalValue = img.dataset.originalValue;
    if (originalValue && originalValue !== '' && originalValue !== 'null') {
        img.src = originalValue.startsWith('http') ? originalValue : window.App.baseUrl + '/' + originalValue.replace(/^\/+/, '');
        img.dataset.isDefaultImage = 'false';
        img.dataset.fallbackApplied = 'false';
    }
};