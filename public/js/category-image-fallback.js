/**
 * Category Image Fallback Handler
 * Handles default images for hardware categories when image fails to load
 */

const imageBaseUrl = () => (window.App && window.App.baseUrl) || window.location.origin;

/**
 * Get the shared default image URL.
 * @param {string} categoryName
 * @returns {string}
 */
function getDefaultImageForCategory(categoryName) {
    return imageBaseUrl().replace(/\/+$/, '') + '/img/asset-no-image.svg';
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
    
    const currentSrc = img.src;
    const isAlreadyDefault = currentSrc.includes('/img/asset-no-image.svg');

    if (!isAlreadyDefault) {
        img.dataset.fallbackApplied = 'true';
        img.src = getDefaultImageForCategory(categoryName);
        img.alt = 'Default image for ' + (categoryName || 'unknown category');
        img.dataset.isDefaultImage = 'true';
    } else {
        img.onerror = null;
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
    return currentSrc.includes('/img/asset-no-image.svg') ||
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