// Configuration
const API_BASE_URL = window.location.origin + '/api/v1/scanner';
const APP_URL = window.location.origin;

// State Management
let authToken = localStorage.getItem('authToken') || null;
let currentUser = JSON.parse(localStorage.getItem('currentUser') || 'null');
let html5QrCode = null;

// DOM Elements
const pages = {
    login: document.getElementById('loginPage'),
    scanner: document.getElementById('scannerPage'),
    detail: document.getElementById('detailPage')
};

// Utility Functions
function showPage(pageName) {
    Object.values(pages).forEach(page => page.classList.remove('active'));
    pages[pageName].classList.add('active');
}

function showLoading(show = true) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.remove('d-none');
    } else {
        overlay.classList.add('d-none');
    }
}

function showError(elementId, message) {
    const element = document.getElementById(elementId);
    element.textContent = message;
    element.classList.remove('d-none');
    setTimeout(() => {
        element.classList.add('d-none');
    }, 5000);
}

function showToast(message, type = 'info') {
    // Simple alert for now, can be replaced with better toast library
    alert(message);
}

// API Functions
async function apiCall(endpoint, method = 'GET', data = null, requiresAuth = false) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    if (requiresAuth && authToken) {
        headers['Authorization'] = `Bearer ${authToken}`;
    }

    const options = {
        method,
        headers
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(API_BASE_URL + endpoint, options);
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'API request failed');
        }

        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Authentication Functions
async function loadDepartments() {
    try {
        const result = await apiCall('/departments');
        const select = document.getElementById('department');
        select.innerHTML = '<option value="">Pilih Department...</option>';
        
        result.data.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Failed to load departments:', error);
    }
}

async function login(username, password, departmentId) {
    try {
        showLoading(true);
        const result = await apiCall('/login', 'POST', {
            username,
            password,
            department_id: departmentId
        });

        if (result.success) {
            authToken = result.data.token;
            currentUser = result.data.user;
            
            localStorage.setItem('authToken', authToken);
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            
            initScannerPage();
            showPage('scanner');
        } else {
            throw new Error(result.message || 'Login failed');
        }
    } catch (error) {
        showError('loginError', error.message);
    } finally {
        showLoading(false);
    }
}

function logout() {
    authToken = null;
    currentUser = null;
    localStorage.removeItem('authToken');
    localStorage.removeItem('currentUser');
    
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop();
    }
    
    showPage('login');
}

// Scanner Functions
async function scanAsset(code) {
    try {
        showLoading(true);
        const result = await apiCall('/scan', 'POST', { code }, true);

        if (result.success) {
            displayAssetDetails(result.data);
            showPage('detail');
        } else {
            throw new Error(result.message || 'Asset not found');
        }
    } catch (error) {
        showToast('Asset tidak ditemukan: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

function startQRScanner() {
    const readerElement = document.getElementById('reader');
    const cameraPreview = document.getElementById('cameraPreview');
    
    cameraPreview.classList.remove('d-none');
    document.getElementById('startScanBtn').classList.add('d-none');

    html5QrCode = new Html5Qrcode("reader");
    
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 }
    };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        (decodedText, decodedResult) => {
            // Stop scanning
            html5QrCode.stop().then(() => {
                cameraPreview.classList.add('d-none');
                document.getElementById('startScanBtn').classList.remove('d-none');
            });
            
            // Scan the asset
            scanAsset(decodedText);
        },
        (errorMessage) => {
            // Handle scan error silently
        }
    ).catch(err => {
        console.error('Unable to start scanner:', err);
        showToast('Tidak dapat mengakses kamera', 'error');
        cameraPreview.classList.add('d-none');
        document.getElementById('startScanBtn').classList.remove('d-none');
    });
}

function stopQRScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            document.getElementById('cameraPreview').classList.add('d-none');
            document.getElementById('startScanBtn').classList.remove('d-none');
        });
    }
}

// Display Functions
function displayAssetDetails(asset) {
    const container = document.getElementById('assetDetails');
    
    const html = `
        <div class="detail-header">
            <h3>${asset.name || 'N/A'}</h3>
            <span class="badge bg-light text-dark">${asset.inv_tag || 'No Tag'}</span>
        </div>
        
        ${asset.image ? `
        <div class="detail-image">
            <img src="${asset.image}" alt="Asset Image" onerror="this.style.display='none'">
        </div>
        ` : ''}
        
        <div class="detail-card">
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-tag"></i> Inv. Tag
                </div>
                <div class="detail-value">${asset.inv_tag || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-barcode"></i> Serial
                </div>
                <div class="detail-value">${asset.serial || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-box"></i> Name
                </div>
                <div class="detail-value">${asset.name || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-list"></i> Category
                </div>
                <div class="detail-value">${asset.category || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-laptop"></i> Type
                </div>
                <div class="detail-value">${asset.type || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-trademark"></i> Brand
                </div>
                <div class="detail-value">${asset.brand || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-cube"></i> Model
                </div>
                <div class="detail-value">${asset.model || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-warehouse"></i> WH / Office
                </div>
                <div class="detail-value">${asset.warehouse_office || '<span class="empty">N/A</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-user"></i> User
                </div>
                <div class="detail-value">${asset.user_name || '<span class="empty">Unassigned</span>'}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-info-circle"></i> Status
                </div>
                <div class="detail-value">${asset.status || '<span class="empty">N/A</span>'}</div>
            </div>
            
            ${asset.purchase_date ? `
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-calendar"></i> Purchase Date
                </div>
                <div class="detail-value">${asset.purchase_date}</div>
            </div>
            ` : ''}
            
            ${asset.notes ? `
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-sticky-note"></i> Notes
                </div>
                <div class="detail-value">${asset.notes}</div>
            </div>
            ` : ''}
        </div>
        
        <div class="text-center mt-3">
            <button class="btn btn-primary btn-lg" onclick="goBackToScanner()">
                <i class="fas fa-qrcode"></i> Scan Lagi
            </button>
        </div>
    `;
    
    container.innerHTML = html;
}

function goBackToScanner() {
    showPage('scanner');
}

function initScannerPage() {
    if (currentUser) {
        const userName = `${currentUser.first_name || ''} ${currentUser.last_name || ''}`.trim() || currentUser.username;
        document.getElementById('userName').textContent = userName;
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Check if already logged in
    if (authToken && currentUser) {
        initScannerPage();
        showPage('scanner');
    } else {
        showPage('login');
        loadDepartments();
    }

    // Login Form
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const department = document.getElementById('department').value;
        
        if (!username || !password || !department) {
            showError('loginError', 'Semua field harus diisi');
            return;
        }
        
        login(username, password, parseInt(department));
    });

    // Logout Buttons
    document.getElementById('logoutBtn').addEventListener('click', logout);
    document.getElementById('logoutBtn2').addEventListener('click', logout);

    // Scanner Buttons
    document.getElementById('startScanBtn').addEventListener('click', startQRScanner);
    document.getElementById('stopScanBtn').addEventListener('click', stopQRScanner);

    // Manual Scan
    document.getElementById('manualScanBtn').addEventListener('click', function() {
        const code = document.getElementById('manualCode').value.trim();
        if (code) {
            scanAsset(code);
            document.getElementById('manualCode').value = '';
        } else {
            showToast('Masukkan kode asset', 'error');
        }
    });

    // Manual Code Enter Key
    document.getElementById('manualCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('manualScanBtn').click();
        }
    });

    // Back to Scanner
    document.getElementById('backToScanner').addEventListener('click', goBackToScanner);
});

// Make goBackToScanner globally available for inline onclick
window.goBackToScanner = goBackToScanner;
