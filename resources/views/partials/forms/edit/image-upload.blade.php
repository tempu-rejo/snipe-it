<!-- Image stuff - kept in /resources/views/partials/forms/edit/image-upload.blade.php -->
<!-- Image Delete -->
@if (isset($item) && ($item->{($fieldname ?? 'image')}))
    <div class="form-group{{ $errors->has('image_delete') ? ' has-error' : '' }}">
        <div class="col-md-9 col-md-offset-3">
            <label class="form-control">
                <input type="checkbox" name="image_delete" value="1" @checked(old('image_delete')) aria-label="image_delete">
                {{ trans('general.image_delete') }}
                {!! $errors->first('image_delete', '<span class="alert-msg">:message</span>') !!}
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-9 col-md-offset-3">
            <img src="{{ Storage::disk('public')->url($image_path.e($item->{($fieldname ?? 'image')})) }}" class="img-responsive">
            {!! $errors->first('image_delete', '<span class="alert-msg">:message</span>') !!}
        </div>
    </div>
@endif

<!-- Image Upload and preview -->

<div class="form-group {{ $errors->has((isset($fieldname) ? $fieldname : 'image')) ? 'has-error' : '' }}">
    <label class="col-md-3 control-label" for="{{ (isset($fieldname) ? $fieldname : 'image') }}">{{ trans('general.image_upload') }}</label>
    <div class="col-md-9">

        <input type="file" id="{{ (isset($fieldname) ? $fieldname : 'image') }}" name="{{ (isset($fieldname) ? $fieldname : 'image') }}" aria-label="{{ (isset($fieldname) ? $fieldname : 'image') }}" class="sr-only">

        <label class="btn btn-default" aria-hidden="true">
            {{ trans('button.select_file')  }}
            <input type="file" name="{{ (isset($fieldname) ? $fieldname : 'image') }}" class="js-uploadFile" id="uploadFile" data-maxsize="{{ Helper::file_upload_max_size() }}" accept="image/gif,image/jpeg,image/webp,image/png,image/svg,image/svg+xml,image/avif" style="display:none; max-width: 90%" aria-label="{{ (isset($fieldname) ? $fieldname : 'image') }}" aria-hidden="true">
        </label>
        
        <button type="button" class="btn btn-info" id="cameraBtn" onclick="openCamera()">
            <i class="fas fa-camera"></i> {{ trans('button.camera') ?? 'Kamera' }}
        </button>
        
        <button type="button" class="btn btn-success" onclick="openWindowsCamera()">
            <i class="fas fa-camera-retro"></i> Windows Camera
        </button>
        
        <!-- <button type="button" class="btn btn-primary" onclick="showCameraAlternatives()">
            <i class="fas fa-mobile-alt"></i> Alternatif Kamera
        </button>
        
        <button type="button" class="btn btn-danger" onclick="emergencyDiagnostic()">
            <i class="fas fa-exclamation-triangle"></i> Emergency Fix
        </button>
        
        <button type="button" class="btn btn-success" onclick="quickAlternatives()">
            <i class="fas fa-magic"></i> Quick Solutions
        </button> -->
        
        <span class='label label-default' id="uploadFile-info"></span>

        <!-- Emergency Camera Failure Banner -->
        <div id="emergencyBanner" class="alert alert-danger" style="display: none; margin-top: 15px;">
            <h4><i class="fas fa-exclamation-triangle"></i> <strong>Camera Access Blocked!</strong></h4>
            <p><strong>Don't worry - you have alternatives:</strong></p>
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-warning btn-block" onclick="openWindowsCamera()">
                        <i class="fas fa-camera-retro"></i> Use Windows Camera App
                    </button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-info btn-block" onclick="showQRCode()">
                        <i class="fas fa-qrcode"></i> Upload from Phone
                    </button>
                </div>
            </div>
            <div class="text-center" style="margin-top: 10px;">
                <button type="button" class="btn btn-danger btn-sm" onclick="emergencyDiagnostic()">
                    <i class="fas fa-cog"></i> Emergency Fix Guide
                </button>
                <button type="button" class="btn btn-default btn-sm" onclick="hideEmergencyBanner()">
                    <i class="fas fa-times"></i> Hide
                </button>
            </div>
        </div>

        <p class="help-block" id="uploadFile-status">{{ trans('general.image_filetypes_help', ['size' => Helper::file_upload_max_size_readable()]) }} {{ $help_text ?? '' }}</p>



        {!! $errors->first('image', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
    </div>
    <div class="col-md-4 col-md-offset-3" aria-hidden="true">
        <img id="uploadFile-imagePreview" style="max-width: 300px; display: none;" alt="{{ trans('general.alt_uploaded_image_thumbnail') }}">
    </div>
</div>

<!-- Camera Test Area -->
<div class="form-group" id="cameraTestArea" style="display: none;">
    <label class="col-md-3 control-label">Camera Test</label>
    <div class="col-md-9">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-video"></i> Live Camera Test
                    <button type="button" class="btn btn-xs btn-danger pull-right" onclick="stopCameraTest()">
                        <i class="fas fa-times"></i> Stop
                    </button>
                </h4>
            </div>
            <div class="panel-body text-center">
                <video id="testCamera" width="100%" height="auto" autoplay style="max-width: 400px; border: 2px solid #5bc0de; border-radius: 8px;"></video>
                <div id="testCameraStatus" class="alert alert-info" style="margin-top: 10px;">
                    <strong>Status:</strong> <span id="testCameraMsg">Memulai test kamera...</span>
                </div>
                <div id="testCameraError" class="alert alert-danger" style="display: none; margin-top: 10px;">
                    <strong>Error:</strong> <span id="testCameraErrorMsg"></span>
                </div>
                <div id="permissionInstructions" class="alert alert-warning" style="display: none; margin-top: 10px;">
                    <strong>Instruksi Permission:</strong>
                    <ol style="text-align: left; margin-top: 10px;">
                        <li>Klik tombol <strong>"Request Permission"</strong> terlebih dahulu</li>
                        <li>Saat popup muncul, klik <strong>"Allow"</strong> atau <strong>"Izinkan"</strong></li>
                        <li>Jika masih gagal, periksa address bar browser (biasanya ada ikon kamera yang dicoret)</li>
                        <li>Klik ikon tersebut dan pilih <strong>"Always allow"</strong></li>
                        <li>Refresh halaman (F5) setelah mengubah permission</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Request Area -->
<div class="form-group" id="permissionArea" style="display: none;">
    <label class="col-md-3 control-label">Camera Permission</label>
    <div class="col-md-9">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-unlock"></i> Permission Request Status
                </h4>
            </div>
            <div class="panel-body">
                <div id="permissionStatus" class="alert alert-info">
                    <strong>Status:</strong> <span id="permissionMsg">Meminta izin akses kamera...</span>
                </div>
                <div id="permissionResult" class="alert" style="display: none;">
                    <span id="permissionResultMsg"></span>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-primary" onclick="checkDetailedPermission()">
                        <i class="fas fa-search"></i> Check Permission Details
                    </button>
                    <button type="button" class="btn btn-warning" onclick="clearPermissionAndRetry()">
                        <i class="fas fa-refresh"></i> Clear & Retry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Reset Instructions -->
<div class="form-group" id="permissionResetArea" style="display: none;">
    <label class="col-md-3 control-label">Reset Permission</label>
    <div class="col-md-9">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-exclamation-triangle"></i> Permission Ditolak Secara Permanen
                    <button type="button" class="btn btn-xs btn-default pull-right" onclick="hidePermissionReset()">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </h4>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger">
                    <strong>Status:</strong> Browser telah menyimpan pengaturan "deny" untuk kamera di situs ini.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5><strong>Chrome/Edge:</strong></h5>
                        <ol>
                            <li>Klik ikon <strong>🔒 (gembok)</strong> di address bar</li>
                            <li>Cari bagian <strong>"Camera"</strong></li>
                            <li>Ubah dari <span class="text-danger">"Block"</span> ke <span class="text-success">"Allow"</span></li>
                            <li>Klik <strong>"Reload"</strong> atau refresh halaman (F5)</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <strong>Alternatif Chrome:</strong><br>
                            1. Buka <code>chrome://settings/content/camera</code><br>
                            2. Cari URL situs ini di "Block"<br>
                            3. Pindahkan ke "Allow"
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5><strong>Firefox:</strong></h5>
                        <ol>
                            <li>Klik ikon <strong>🛡️ (shield)</strong> di address bar</li>
                            <li>Klik <strong>"Permissions"</strong></li>
                            <li>Cari <strong>"Use the Camera"</strong></li>
                            <li>Ubah ke <span class="text-success">"Allow"</span></li>
                            <li>Refresh halaman (F5)</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <strong>Alternatif Firefox:</strong><br>
                            1. Buka <code>about:preferences#privacy</code><br>
                            2. Scroll ke "Permissions"<br>
                            3. Klik "Settings" pada Camera<br>
                            4. Hapus atau ubah pengaturan situs ini
                        </div>
                    </div>
                </div>
                
                <div class="text-center" style="margin-top: 15px;">
                    <button type="button" class="btn btn-success" onclick="testAfterReset()">
                        <i class="fas fa-check"></i> Saya Sudah Reset - Test Lagi
                    </button>
                    <button type="button" class="btn btn-warning" onclick="openChromeSettings()">
                        <i class="fas fa-cog"></i> Buka Chrome Camera Settings
                    </button>
                </div>
                
                <div class="alert alert-warning" style="margin-top: 15px;">
                    <strong>Jika masih bermasalah:</strong><br>
                    1. <strong>Restart browser</strong> sepenuhnya<br>
                    2. <strong>Clear browser cache</strong> dan cookies<br>
                    3. Coba <strong>Incognito/Private mode</strong><br>
                    4. Periksa <strong>Windows Camera Privacy Settings</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-labelledby="cameraModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="cameraModalLabel">
                    <i class="fas fa-camera"></i> {{ trans('button.camera') ?? 'Ambil Foto' }}
                </h4>
            </div>
            <div class="modal-body text-center">
                <video id="camera" width="100%" height="auto" autoplay style="max-width: 500px; border: 2px solid #ddd; border-radius: 8px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div class="camera-controls" style="margin-top: 15px;">
                    <button type="button" class="btn btn-success" id="captureBtn">
                        <i class="fas fa-camera"></i> {{ trans('button.capture') ?? 'Ambil Foto' }}
                    </button>
                    <button type="button" class="btn btn-warning" id="retakeBtn" style="display: none;">
                        <i class="fas fa-redo"></i> {{ trans('button.retake') ?? 'Ulangi' }}
                    </button>
                    <button type="button" class="btn btn-primary" id="usePhotoBtn" style="display: none;">
                        <i class="fas fa-check"></i> {{ trans('button.use_photo') ?? 'Gunakan Foto' }}
                    </button>
                </div>
                <div id="cameraError" class="alert alert-danger" style="display: none; margin-top: 15px;">
                    <strong>Error:</strong> <span id="cameraErrorMsg"></span>
                </div>
                <div id="cameraPermission" class="alert alert-info" style="display: none; margin-top: 15px;">
                    <strong>Info:</strong> <span id="cameraPermissionMsg"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="retryPermissionBtn" style="display: none;">
                    <i class="fas fa-redo"></i> {{ trans('button.retry') ?? 'Coba Lagi' }}
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ trans('button.cancel') ?? 'Batal' }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alternative Camera Instructions -->
<div class="form-group" id="alternativeCameraArea" style="display: none;">
    <label class="col-md-3 control-label">Alternatif Kamera</label>
    <div class="col-md-9">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-camera-retro"></i> Cara Menggunakan Kamera Tanpa Browser
                    <button type="button" class="btn btn-xs btn-default pull-right" onclick="hideAlternativeCamera()">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-desktop"></i> <strong>Windows Camera App</strong></h5>
                        <ol>
                            <li><strong>Klik "Buka Windows Camera"</strong> di atas</li>
                            <li>Ambil foto dengan aplikasi Camera Windows</li>
                            <li>Foto tersimpan di <code>Pictures/Camera Roll</code></li>
                            <li>Kembali ke sini dan klik <strong>"Select File"</strong> di bawah</li>
                            <li>Pilih foto yang baru diambil</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <strong>Lokasi foto:</strong><br>
                            <code>C:\Users\[Username]\Pictures\Camera Roll\</code><br>
                            <small>Atau bisa dicari di File Explorer > Pictures</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5><i class="fas fa-mobile-alt"></i> <strong>Smartphone/Mobile</strong></h5>
                        <ol>
                            <li>Ambil foto dengan <strong>smartphone</strong></li>
                            <li>Transfer ke komputer via:
                                <ul>
                                    <li>USB Cable</li>
                                    <li>WhatsApp Web</li>
                                    <li>Google Drive/OneDrive</li>
                                    <li>Email</li>
                                </ul>
                            </li>
                            <li>Klik <strong>"Select File"</strong> dan pilih foto</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <strong>QR Code Scanner:</strong><br>
                            <button type="button" class="btn btn-sm btn-success" onclick="showQRCode()">
                                <i class="fas fa-qrcode"></i> Generate QR untuk Mobile Upload
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <h5><i class="fas fa-external-link-alt"></i> <strong>Aplikasi Kamera Eksternal</strong></h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info" onclick="openExternalCamera('snap')">
                                <i class="fas fa-camera"></i> Snap Camera
                            </button>
                            <button type="button" class="btn btn-info" onclick="openExternalCamera('obs')">
                                <i class="fas fa-video"></i> OBS Virtual Camera
                            </button>
                            <button type="button" class="btn btn-info" onclick="openExternalCamera('cheese')">
                                <i class="fas fa-camera"></i> Aplikasi Kamera Lain
                            </button>
                        </div>
                        
                        <div class="alert alert-warning" style="margin-top: 10px;">
                            <strong>Tip:</strong> Jika menggunakan aplikasi kamera eksternal, ambil foto lalu save ke folder Pictures, kemudian upload melalui "Select File".
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Ask Notification -->
<div class="form-group" id="permissionAskNotification" style="display: none;">
    <label class="col-md-3 control-label">Camera Permission</label>
    <div class="col-md-9">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-exclamation-triangle"></i> Izin Kamera Diperlukan
                </h4>
            </div>
            <div class="panel-body">
                <div class="alert alert-warning">
                    <strong>⚠️ Perhatian:</strong> Sistem akan meminta izin akses kamera Anda.
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <h5><strong>Yang akan terjadi:</strong></h5>
                        <ul>
                            <li>Browser akan menampilkan popup izin kamera</li>
                            <li>Pilih <strong>"Allow"</strong> atau <strong>"Izinkan"</strong> untuk melanjutkan</li>
                            <li>Kamera akan diaktifkan untuk mengambil foto</li>
                            <li>Data kamera tidak akan disimpan atau dibagikan</li>
                        </ul>
                        
                        <div class="alert alert-info" style="margin-top: 15px;">
                            <strong>Tips:</strong><br>
                            • Pastikan tidak ada aplikasi lain yang menggunakan kamera<br>
                            • Jika popup tidak muncul, periksa ikon kamera di address bar<br>
                            • Untuk browser mobile, izin mungkin diminta otomatis
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-center">
                        <div class="well">
                            <h5><strong>Contoh Popup Browser:</strong></h5>
                            <div style="border: 2px solid #ddd; border-radius: 8px; padding: 10px; background: #f9f9f9;">
                                <small><i class="fas fa-camera"></i> "situs.com ingin menggunakan kamera Anda"</small><br>
                                <button class="btn btn-xs btn-success" disabled>Allow</button>
                                <button class="btn btn-xs btn-danger" disabled>Block</button>
                            </div>
                            <p style="margin-top: 10px;"><small>Klik <strong>Allow</strong> untuk melanjutkan</small></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center" style="margin-top: 20px;">
                    <button type="button" class="btn btn-success btn-lg" onclick="acknowledgePermissionAndProceed()">
                        <i class="fas fa-check"></i> Saya Mengerti, Lanjutkan
                    </button>
                    <button type="button" class="btn btn-default" onclick="hidePermissionNotification()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Upload Area -->
<div class="form-group" id="qrCodeArea" style="display: none;">
    <label class="col-md-3 control-label">Mobile Upload</label>
    <div class="col-md-9">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fas fa-qrcode"></i> Upload dari Smartphone
                    <button type="button" class="btn btn-xs btn-default pull-right" onclick="hideQRCode()">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </h4>
            </div>
            <div class="panel-body text-center">
                <div id="qrCodeContainer">
                    <div class="alert alert-info">
                        <h5>Scan QR Code ini dengan smartphone:</h5>
                        <div id="qrCodeImage" style="margin: 20px 0;">
                            <!-- QR Code will be generated here -->
                            <canvas id="qrCanvas" width="256" height="256" style="border: 2px solid #ddd;"></canvas>
                        </div>
                        <p><strong>Langkah:</strong></p>
                        <ol style="text-align: left; display: inline-block;">
                            <li>Scan QR code dengan aplikasi camera smartphone</li>
                            <li>Buka link yang muncul</li>
                            <li>Ambil foto atau pilih dari galeri</li>
                            <li>Upload akan otomatis tersinkron ke form ini</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS for permission notification animations -->
<style>
@keyframes slideInDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideOutUp {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(-20px);
        opacity: 0;
    }
}

#permissionAskNotification {
    animation: slideInDown 0.5s ease-out;
}

#permissionAskNotification.slide-out {
    animation: slideOutUp 0.3s ease-in forwards;
}

.permission-panel-highlight {
    border: 3px solid #f0ad4e !important;
    box-shadow: 0 0 15px rgba(240, 173, 78, 0.3);
    animation: glow 1.5s ease-in-out infinite alternate;
}

@keyframes glow {
    from {
        box-shadow: 0 0 15px rgba(240, 173, 78, 0.3);
    }
    to {
        box-shadow: 0 0 25px rgba(240, 173, 78, 0.6);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 5px rgba(217, 83, 79, 0.5);
    }
    50% {
        transform: scale(1.02);
        box-shadow: 0 0 20px rgba(217, 83, 79, 0.8);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 5px rgba(217, 83, 79, 0.5);
    }
}

#emergencyBanner {
    border: 3px solid #d9534f !important;
    background: linear-gradient(135deg, #f2dede 0%, #ebccd1 100%);
    border-radius: 8px;
}

#emergencyBanner.attention {
    animation: pulse 2s infinite;
}
</style>

<!-- Add comprehensive permissions policy meta tags -->
<meta http-equiv="Permissions-Policy" content="camera=*, microphone=*, geolocation=*">
<meta http-equiv="Feature-Policy" content="camera '*'; microphone '*'; geolocation '*'">

<!-- Alternative iframe permissions if this is embedded -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="permissions" content="camera, microphone">

<!-- QR Code library for mobile upload functionality -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
let stream = null;
let capturedImageBlob = null;

// Variable untuk test camera stream
let testStream = null;

function openCamera() {
    // Show permission notification first
    showPermissionNotification();
}

async function checkCameraPermission() {
    const permissionDiv = document.getElementById('cameraPermission');
    const permissionMsg = document.getElementById('cameraPermissionMsg');
    const errorDiv = document.getElementById('cameraError');
    const retryBtn = document.getElementById('retryPermissionBtn');
    
    // Reset UI
    permissionDiv.style.display = 'none';
    errorDiv.style.display = 'none';
    retryBtn.style.display = 'none';
    
    // Tampilkan notifikasi ask permission terlebih dahulu
    showPermissionNotification();
    
    // Langsung coba akses kamera tanpa cek permission dulu
    // Karena permission API kadang tidak akurat di localhost
    permissionMsg.textContent = 'Memulai kamera...';
    permissionDiv.style.display = 'block';
    
    setTimeout(() => {
        startCamera();
    }, 1000);
}

// Permission notification functions
function showPermissionNotification() {
    const notification = document.getElementById('permissionAskNotification');
    notification.style.display = 'block';
    
    // Scroll to notification
    notification.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
    
    // Auto-hide after 30 seconds if user doesn't respond
    window.permissionNotificationTimer = setTimeout(() => {
        hidePermissionNotification();
    }, 30000);
}

function hidePermissionNotification() {
    const notification = document.getElementById('permissionAskNotification');
    notification.style.display = 'none';
    
    // Clear auto-hide timer
    if (window.permissionNotificationTimer) {
        clearTimeout(window.permissionNotificationTimer);
        window.permissionNotificationTimer = null;
    }
}

function acknowledgePermissionAndProceed() {
    hidePermissionNotification();
    
    // Small delay to let UI update, then proceed with camera
    setTimeout(() => {
        proceedWithCameraFromPanel();
    }, 300);
}

// Fungsi untuk melanjutkan dengan kamera setelah user acknowledge dalam panel
function proceedWithCameraFromPanel() {
    // Check jQuery availability and use alternative
    if (typeof $ !== 'undefined') {
        $('#cameraModal').modal('show');
    } else {
        showCameraModal();
    }
    
    // Show additional floating tip
    showFloatingPermissionTip();
    
    // Debug: cek perangkat yang tersedia
    checkAvailableDevices();
    
    checkCameraPermission();
}

function showFloatingPermissionTip() {
    // Show additional tip
    const tipNotification = document.createElement('div');
    tipNotification.className = 'alert alert-warning';
    tipNotification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        border: 2px solid #f0ad4e;
    `;
    
    tipNotification.innerHTML = `
        <div style="text-align: center;">
            <h5 style="margin: 0 0 10px 0;">
                <i class="fas fa-lightbulb" style="color: #f0ad4e;"></i> 
                <strong>Tips:</strong>
            </h5>
            <p style="margin: 0; font-size: 13px;">
                Jika popup permission tidak muncul, periksa address bar browser 
                untuk ikon kamera yang mungkin terblokir.
            </p>
        </div>
    `;
    
    document.body.appendChild(tipNotification);
    
    // Auto remove tip after 5 seconds
    setTimeout(() => {
        if (tipNotification && tipNotification.parentNode) {
            tipNotification.style.animation = 'slideOutRight 0.3s ease-in forwards';
            setTimeout(() => {
                tipNotification.remove();
            }, 300);
        }
    }, 5000);
}

function showPermissionDeniedMessage() {
    const errorDiv = document.getElementById('cameraError');
    const errorMsg = document.getElementById('cameraErrorMsg');
    const retryBtn = document.getElementById('retryPermissionBtn');
    
    errorMsg.innerHTML = `
        Akses kamera ditolak. Untuk menggunakan fitur kamera:<br>
        1. Klik ikon gembok/kamera di address bar browser<br>
        2. Pilih "Always allow" atau "Selalu izinkan"<br>
        3. Refresh halaman atau klik tombol "Coba Lagi"
    `;
    errorDiv.style.display = 'block';
    retryBtn.style.display = 'inline-block';
}

function startCamera() {
    const video = document.getElementById('camera');
    const errorDiv = document.getElementById('cameraError');
    const errorMsg = document.getElementById('cameraErrorMsg');
    const permissionDiv = document.getElementById('cameraPermission');
    const retryBtn = document.getElementById('retryPermissionBtn');
    
    // Reset UI
    document.getElementById('captureBtn').style.display = 'inline-block';
    document.getElementById('retakeBtn').style.display = 'none';
    document.getElementById('usePhotoBtn').style.display = 'none';
    errorDiv.style.display = 'none';
    retryBtn.style.display = 'none';
    
    // Coba dengan constraint yang lebih sederhana dulu
    const constraints = {
        video: {
            width: { min: 320, ideal: 640, max: 1920 },
            height: { min: 240, ideal: 480, max: 1080 }
        }
    };
    
    console.log('Mencoba akses kamera...');
    
    // Request camera access
    navigator.mediaDevices.getUserMedia(constraints)
    .then(function(mediaStream) {
        console.log('Kamera berhasil diakses');
        stream = mediaStream;
        video.srcObject = stream;
        video.play();
        
        // Hide permission message when camera starts successfully
        permissionDiv.style.display = 'none';
    })
    .catch(function(err) {
        console.error('Error accessing camera:', err);
        console.error('Error name:', err.name);
        console.error('Error message:', err.message);
        
        permissionDiv.style.display = 'none';
        
        let errorMessage = '';
        
        switch (err.name) {
            case 'NotAllowedError':
            case 'PermissionDeniedError':
                // Check if it's a permissions policy violation
                if (err.message && err.message.includes('Permissions policy')) {
                    errorMessage = `
                        <strong>🚫 PERMISSIONS POLICY VIOLATION</strong><br>
                        Browser memblokir akses kamera karena kebijakan keamanan.<br>
                        <br>
                        <strong>SOLUSI:</strong><br>
                        1. <strong>Buka browser dalam mode incognito/private</strong><br>
                        2. Atau add parameter ini ke URL: <code>--disable-features=VizDisplayCompositor</code><br>
                        3. Atau coba Chrome dengan flag: <code>--allow-running-insecure-content</code><br>
                        4. Pastikan tidak ada iframe yang membatasi permissions<br>
                        <br>
                        <button type="button" class="btn btn-warning btn-sm" onclick="openInIncognito()">
                            <i class="fas fa-user-secret"></i> Buka Mode Incognito
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="checkPermissionsPolicyStatus()">
                            <i class="fas fa-shield-alt"></i> Check Policy Status
                        </button>
                    `;
                } else {
                    errorMessage = `
                        Akses kamera masih ditolak. Coba langkah berikut:<br>
                        1. Refresh halaman ini (F5 atau Ctrl+R)<br>
                        2. Pastikan tidak ada aplikasi lain yang menggunakan kamera<br>
                        3. Coba restart browser<br>
                        4. Periksa Windows Privacy Settings: Settings > Privacy > Camera<br>
                        <br>
                        <button type="button" class="btn btn-danger btn-sm" onclick="forcePermissionReset()">
                            <i class="fas fa-redo"></i> Force Reset Permission
                        </button>
                    `;
                }
                retryBtn.style.display = 'inline-block';
                
                // Trigger emergency banner for critical failures
                detectCameraFailure(err.name, err.message);
                break;
                
            case 'NotFoundError':
            case 'DevicesNotFoundError':
                errorMessage = 'Kamera tidak ditemukan. Pastikan kamera terhubung dan driver terinstall dengan benar.';
                break;
                
            case 'NotSupportedError':
                errorMessage = 'Browser tidak mendukung akses kamera. Pastikan menggunakan HTTPS atau localhost.';
                break;
                
            case 'NotReadableError':
            case 'TrackStartError':
                errorMessage = 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi seperti Zoom, Teams, atau aplikasi video call lainnya.';
                retryBtn.style.display = 'inline-block';
                break;
                
            case 'OverconstrainedError':
            case 'ConstraintNotSatisfiedError':
                errorMessage = 'Resolusi kamera tidak mendukung. Mencoba dengan setting paling dasar...';
                // Try again with basic constraints
                setTimeout(() => {
                    startCameraBasic();
                }, 2000);
                break;
                
            default:
                errorMessage = `
                    Error: ${err.message || 'Error tidak dikenal'}<br>
                    Coba:<br>
                    1. Refresh halaman (F5)<br>
                    2. Restart browser<br>
                    3. Cek Windows Camera Privacy Settings
                `;
                retryBtn.style.display = 'inline-block';
                break;
        }
        
        errorMsg.innerHTML = errorMessage;
        errorDiv.style.display = 'block';
    });
}

function startCameraBasic() {
    const video = document.getElementById('camera');
    const errorDiv = document.getElementById('cameraError');
    const errorMsg = document.getElementById('cameraErrorMsg');
    
    console.log('Mencoba dengan constraint basic...');
    
    // Try with basic video constraints as fallback
    navigator.mediaDevices.getUserMedia({ video: true })
    .then(function(mediaStream) {
        console.log('Kamera berhasil dengan constraint basic');
        stream = mediaStream;
        video.srcObject = stream;
        video.play();
        errorDiv.style.display = 'none';
    })
    .catch(function(err) {
        console.error('Error accessing camera with basic constraints:', err);
        errorMsg.innerHTML = `
            Tidak dapat mengakses kamera dengan setting apapun.<br>
            Error: ${err.name} - ${err.message}<br>
            <br>
            Troubleshooting:<br>
            1. Cek apakah kamera berfungsi di aplikasi Windows Camera<br>
            2. Restart browser sepenuhnya<br>
            3. Cek Windows Privacy Settings untuk Camera<br>
            4. Pastikan tidak ada aplikasi lain yang menggunakan kamera
        `;
        errorDiv.style.display = 'block';
        document.getElementById('retryPermissionBtn').style.display = 'inline-block';
    });
}

// Fungsi untuk debug - cek perangkat yang tersedia
async function checkAvailableDevices() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        console.log('Perangkat yang tersedia:', devices);
        
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        console.log('Kamera yang tersedia:', videoDevices);
        
        if (videoDevices.length === 0) {
            console.log('Tidak ada kamera yang ditemukan');
        } else {
            console.log(`Ditemukan ${videoDevices.length} kamera`);
        }
    } catch (err) {
        console.error('Error checking devices:', err);
    }
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

document.getElementById('captureBtn').addEventListener('click', function() {
    const video = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to blob
    canvas.toBlob(function(blob) {
        capturedImageBlob = blob;
        
        // Show captured image in video element
        const url = URL.createObjectURL(blob);
        video.srcObject = null;
        video.src = url;
        
        // Update UI
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('retakeBtn').style.display = 'inline-block';
        document.getElementById('usePhotoBtn').style.display = 'inline-block';
        
        stopCamera();
    }, 'image/jpeg', 0.8);
});

document.getElementById('retakeBtn').addEventListener('click', function() {
    startCamera();
});

document.getElementById('retryPermissionBtn').addEventListener('click', function() {
    checkCameraPermission();
});

document.getElementById('usePhotoBtn').addEventListener('click', function() {
    if (capturedImageBlob) {
        // Create a file from the blob
        const timestamp = new Date().getTime();
        const file = new File([capturedImageBlob], `camera_photo_${timestamp}.jpg`, {
            type: 'image/jpeg'
        });
        
        // Create a DataTransfer object to simulate file input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        // Set the file to the file input
        const fileInput = document.getElementById('uploadFile');
        fileInput.files = dataTransfer.files;
        
        // Trigger change event to update preview
        const event = new Event('change', { bubbles: true });
        fileInput.dispatchEvent(event);
        
        // Update file info
        document.getElementById('uploadFile-info').textContent = file.name;
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('uploadFile-imagePreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
        
        if (typeof $ !== 'undefined') {
            $('#cameraModal').modal('hide');
        } else {
            hideCameraModal();
        }
    }
});

// Clean up when modal is closed - support both jQuery dan vanilla JS
if (typeof $ !== 'undefined') {
    $('#cameraModal').on('hidden.bs.modal', function () {
        stopCamera();
        const video = document.getElementById('camera');
        video.src = '';
        video.srcObject = null;
    });

    // Reset UI when modal is shown
    $('#cameraModal').on('shown.bs.modal', function () {
        capturedImageBlob = null;
    });
} else {
    // Vanilla JS event listeners
    const modal = document.getElementById('cameraModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            stopCamera();
            const video = document.getElementById('camera');
            video.src = '';
            video.srcObject = null;
        });
        
        modal.addEventListener('shown.bs.modal', function() {
            capturedImageBlob = null;
        });
    }
}

// Clean up test camera when page unloads
window.addEventListener('beforeunload', function() {
    stopCameraTest();
    stopCamera();
});

// Fungsi untuk test akses kamera langsung di halaman
function testCameraAccess() {
    const testArea = document.getElementById('cameraTestArea');
    const testVideo = document.getElementById('testCamera');
    const testStatus = document.getElementById('testCameraStatus');
    const testMsg = document.getElementById('testCameraMsg');
    const testError = document.getElementById('testCameraError');
    const testErrorMsg = document.getElementById('testCameraErrorMsg');
    
    // Show test area
    testArea.style.display = 'block';
    testStatus.style.display = 'block';
    testError.style.display = 'none';
    testMsg.textContent = 'Meminta akses kamera...';
    
    console.log('=== STARTING CAMERA TEST ===');
    
    // Cek perangkat yang tersedia
    checkAvailableDevices();
    
    // Coba akses kamera dengan berbagai constraint
    const constraints = [
        // Constraint 1: Basic
        { video: true },
        // Constraint 2: With resolution
        { 
            video: { 
                width: { min: 320, ideal: 640, max: 1920 },
                height: { min: 240, ideal: 480, max: 1080 }
            } 
        },
        // Constraint 3: Specific resolution
        { 
            video: { 
                width: 640,
                height: 480
            } 
        }
    ];
    
    tryConstraints(constraints, 0);
    
    function tryConstraints(constraintList, index) {
        if (index >= constraintList.length) {
            testMsg.textContent = 'Semua constraint gagal';
            testError.style.display = 'block';
            testErrorMsg.textContent = 'Tidak dapat mengakses kamera dengan constraint apapun';
            return;
        }
        
        const currentConstraint = constraintList[index];
        testMsg.textContent = `Mencoba constraint ${index + 1}/${constraintList.length}...`;
        
        console.log(`Trying constraint ${index + 1}:`, currentConstraint);
        
        navigator.mediaDevices.getUserMedia(currentConstraint)
        .then(function(mediaStream) {
            console.log(`Constraint ${index + 1} berhasil!`);
            testStream = mediaStream;
            testVideo.srcObject = mediaStream;
            testVideo.play();
            
            testMsg.innerHTML = `
                ✅ Kamera berhasil diakses!<br>
                Constraint: ${index + 1}/${constraintList.length}<br>
                Stream: ${mediaStream.getVideoTracks().length} video track(s)
            `;
            
            // Show video track info
            const videoTrack = mediaStream.getVideoTracks()[0];
            if (videoTrack) {
                const settings = videoTrack.getSettings();
                console.log('Video track settings:', settings);
                testMsg.innerHTML += `<br>Resolution: ${settings.width}x${settings.height}`;
            }
            
            testError.style.display = 'none';
        })
        .catch(function(err) {
            console.error(`Constraint ${index + 1} gagal:`, err);
            
            if (index === constraintList.length - 1) {
                // Last constraint failed
                testMsg.textContent = 'Semua constraint gagal';
                testError.style.display = 'block';
                testErrorMsg.innerHTML = `
                    Akses kamera gagal dengan semua constraint.<br>
                    Error terakhir: ${err.name} - ${err.message}<br>
                    <br>
                    <strong>SOLUSI:</strong><br>
                    1. <strong>Klik tombol "Request Permission" terlebih dahulu</strong><br>
                    2. Pastikan memberikan izin "Allow" saat popup muncul<br>
                    3. Periksa address bar browser untuk ikon kamera<br>
                    4. Jika masih gagal, coba "Clear & Retry"
                `;
                
                // Show permission instructions
                document.getElementById('permissionInstructions').style.display = 'block';
                
                // Trigger emergency banner for test failures
                detectCameraFailure(err.name, err.message);
            } else {
                // Try next constraint
                setTimeout(() => {
                    tryConstraints(constraintList, index + 1);
                }, 1000);
            }
        });
    }
}

function stopCameraTest() {
    const testArea = document.getElementById('cameraTestArea');
    const testVideo = document.getElementById('testCamera');
    
    if (testStream) {
        testStream.getTracks().forEach(track => track.stop());
        testStream = null;
    }
    
    testVideo.srcObject = null;
    testVideo.src = '';
    testArea.style.display = 'none';
    
    console.log('=== CAMERA TEST STOPPED ===');
}

// Fungsi untuk request permission secara eksplisit
async function requestCameraPermission() {
    const permissionArea = document.getElementById('permissionArea');
    const permissionMsg = document.getElementById('permissionMsg');
    const permissionResult = document.getElementById('permissionResult');
    const permissionResultMsg = document.getElementById('permissionResultMsg');
    
    // Show permission area
    permissionArea.style.display = 'block';
    permissionResult.style.display = 'none';
    permissionMsg.textContent = 'Mengecek status permission...';
    
    console.log('=== REQUESTING CAMERA PERMISSION ===');
    
    try {
        // Check current permission state first
        if ('permissions' in navigator) {
            const permission = await navigator.permissions.query({ name: 'camera' });
            console.log('Current permission state:', permission.state);
            
            if (permission.state === 'denied') {
                // Permission is permanently denied
                permissionMsg.textContent = '❌ Permission ditolak secara permanen';
                permissionResult.className = 'alert alert-danger';
                permissionResult.style.display = 'block';
                permissionResultMsg.innerHTML = `
                    <strong>Permission Ditolak Secara Permanen!</strong><br>
                    Browser telah menyimpan pengaturan "deny" untuk situs ini.<br>
                    <br>
                    <button type="button" class="btn btn-danger btn-sm" onclick="showPermissionResetInstructions()">
                        <i class="fas fa-exclamation-triangle"></i> Lihat Cara Reset Permission
                    </button>
                `;
                return;
            }
            
            permissionMsg.textContent = `Current state: ${permission.state}`;
        }
        
        // Request camera access - this will trigger permission dialog
        permissionMsg.textContent = 'Meminta akses kamera... (dialog permission akan muncul)';
        
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: {
                width: { min: 320, ideal: 640 },
                height: { min: 240, ideal: 480 }
            } 
        });
        
        // Success!
        console.log('Permission granted successfully!');
        permissionMsg.textContent = '✅ Permission berhasil diberikan!';
        
        permissionResult.className = 'alert alert-success';
        permissionResult.style.display = 'block';
        permissionResultMsg.innerHTML = `
            <strong>Berhasil!</strong> Izin kamera telah diberikan.<br>
            Sekarang Anda bisa menggunakan fitur kamera dengan normal.<br>
            <small>Stream akan dihentikan dalam 3 detik...</small>
        `;
        
        // Stop the stream after showing success
        setTimeout(() => {
            stream.getTracks().forEach(track => track.stop());
            permissionMsg.textContent = '✅ Permission tersimpan. Kamera siap digunakan.';
        }, 3000);
        
    } catch (err) {
        console.error('Permission request failed:', err);
        
        permissionMsg.textContent = '❌ Permission ditolak';
        permissionResult.className = 'alert alert-danger';
        permissionResult.style.display = 'block';
        
        let errorMessage = '';
        
        switch (err.name) {
            case 'NotAllowedError':
                errorMessage = `
                    <strong>Permission Ditolak!</strong><br>
                    Ini mungkin karena permission sudah di-block sebelumnya.<br>
                    <br>
                    <button type="button" class="btn btn-danger btn-sm" onclick="showPermissionResetInstructions()">
                        <i class="fas fa-exclamation-triangle"></i> Lihat Cara Reset Permission
                    </button>
                `;
                break;
                
            case 'NotFoundError':
                errorMessage = `
                    <strong>Kamera Tidak Ditemukan!</strong><br>
                    Periksa:<br>
                    • Kamera terhubung dengan benar<br>
                    • Driver kamera terinstall<br>
                    • Kamera tidak digunakan aplikasi lain
                `;
                break;
                
            case 'NotSupportedError':
                errorMessage = `
                    <strong>Browser Tidak Mendukung!</strong><br>
                    • Pastikan menggunakan HTTPS atau localhost<br>
                    • Update browser ke versi terbaru<br>
                    • Coba browser lain (Chrome, Firefox, Edge)
                `;
                break;
                
            default:
                errorMessage = `
                    <strong>Error:</strong> ${err.name}<br>
                    <strong>Message:</strong> ${err.message}<br>
                    <strong>Saran:</strong> Coba restart browser dan ulangi proses
                `;
                break;
        }
        
        permissionResultMsg.innerHTML = errorMessage;
    }
}

// Fungsi untuk check permission secara detail
async function checkDetailedPermission() {
    const permissionMsg = document.getElementById('permissionMsg');
    const permissionResult = document.getElementById('permissionResult');
    const permissionResultMsg = document.getElementById('permissionResultMsg');
    
    console.log('=== CHECKING DETAILED PERMISSION ===');
    
    try {
        // Check devices first
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        console.log('Available video devices:', videoDevices);
        
        let statusHtml = `
            <strong>Device Information:</strong><br>
            • Total devices: ${devices.length}<br>
            • Video devices: ${videoDevices.length}<br>
        `;
        
        if (videoDevices.length === 0) {
            statusHtml += `<span class="text-danger">❌ Tidak ada kamera yang terdeteksi</span><br>`;
        } else {
            statusHtml += `<span class="text-success">✅ Kamera tersedia</span><br>`;
            videoDevices.forEach((device, index) => {
                statusHtml += `• Camera ${index + 1}: ${device.label || 'Unknown Camera'}<br>`;
            });
        }
        
        // Check permission API
        if ('permissions' in navigator) {
            const permission = await navigator.permissions.query({ name: 'camera' });
            statusHtml += `<br><strong>Permission State:</strong> ${permission.state}<br>`;
            
            switch (permission.state) {
                case 'granted':
                    statusHtml += `<span class="text-success">✅ Permission sudah diberikan</span>`;
                    break;
                case 'denied':
                    statusHtml += `<span class="text-danger">❌ Permission ditolak secara permanen</span>`;
                    break;
                case 'prompt':
                    statusHtml += `<span class="text-warning">⚠️ Permission belum diminta</span>`;
                    break;
            }
        } else {
            statusHtml += `<br><span class="text-warning">⚠️ Permission API tidak didukung browser</span>`;
        }
        
        // Check getUserMedia support
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            statusHtml += `<br><span class="text-success">✅ getUserMedia didukung</span>`;
        } else {
            statusHtml += `<br><span class="text-danger">❌ getUserMedia tidak didukung</span>`;
        }
        
        // Check if running on HTTPS or localhost
        const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
        if (isSecure) {
            statusHtml += `<br><span class="text-success">✅ Running on secure context</span>`;
        } else {
            statusHtml += `<br><span class="text-danger">❌ Not running on secure context (HTTPS/localhost required)</span>`;
        }
        
        permissionResult.className = 'alert alert-info';
        permissionResult.style.display = 'block';
        permissionResultMsg.innerHTML = statusHtml;
        
    } catch (err) {
        console.error('Error checking permission details:', err);
        permissionResult.className = 'alert alert-danger';
        permissionResult.style.display = 'block';
        permissionResultMsg.innerHTML = `Error checking details: ${err.message}`;
    }
}

// Fungsi untuk clear permission dan retry
function clearPermissionAndRetry() {
    const permissionArea = document.getElementById('permissionArea');
    
    // Reset UI
    permissionArea.style.display = 'none';
    
    // Show instructions
    const instructions = document.getElementById('permissionInstructions');
    instructions.style.display = 'block';
    
    alert('Untuk mereset permission:\n\n1. Klik ikon kamera/gembok di address bar\n2. Pilih "Reset permissions" atau hapus izin\n3. Refresh halaman (F5)\n4. Klik "Request Permission" lagi');
}

// Fungsi untuk menampilkan instruksi reset permission
function showPermissionResetInstructions() {
    const resetArea = document.getElementById('permissionResetArea');
    resetArea.style.display = 'block';
    
    // Scroll to the reset area
    resetArea.scrollIntoView({ behavior: 'smooth' });
    
    console.log('=== SHOWING PERMISSION RESET INSTRUCTIONS ===');
}

// Fungsi untuk menyembunyikan instruksi reset
function hidePermissionReset() {
    const resetArea = document.getElementById('permissionResetArea');
    resetArea.style.display = 'none';
}

// Fungsi untuk test setelah user reset permission
async function testAfterReset() {
    console.log('=== TESTING AFTER PERMISSION RESET ===');
    
    // Hide reset area
    hidePermissionReset();
    
    // Wait a bit for UI update
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Check permission status
    if ('permissions' in navigator) {
        try {
            const permission = await navigator.permissions.query({ name: 'camera' });
            console.log('Permission state after reset:', permission.state);
            
            if (permission.state === 'denied') {
                alert('Permission masih dalam status "denied". Pastikan Anda telah mengikuti langkah reset dengan benar dan refresh halaman (F5).');
                showPermissionResetInstructions();
                return;
            }
        } catch (err) {
            console.error('Error checking permission:', err);
        }
    }
    
    // Try to request permission again
    alert('Mencoba request permission lagi...');
    requestCameraPermission();
}

// Fungsi untuk membuka Chrome camera settings
function openChromeSettings() {
    const isChrome = navigator.userAgent.includes('Chrome');
    const isEdge = navigator.userAgent.includes('Edg');
    
    if (isChrome || isEdge) {
        // Try to open Chrome camera settings
        window.open('chrome://settings/content/camera', '_blank');
    } else {
        alert('Fungsi ini hanya bekerja di Chrome/Edge. Untuk browser lain, ikuti instruksi manual di atas.');
    }
}

// Fungsi untuk test permission khusus situs ini
async function testSiteSpecificPermission() {
    console.log('=== TESTING SITE-SPECIFIC PERMISSION ===');
    
    const siteUrl = window.location.origin;
    console.log('Current site:', siteUrl);
    console.log('Protocol:', window.location.protocol);
    console.log('Host:', window.location.host);
    
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.warn('jQuery not available, using vanilla JS');
    }
    
    try {
        // Check permissions policy
        if (document.featurePolicy) {
            const cameraAllowed = document.featurePolicy.allowsFeature('camera');
            console.log('Camera allowed by permissions policy:', cameraAllowed);
            
            if (!cameraAllowed) {
                alert(`
                    ❌ PERMISSIONS POLICY VIOLATION!
                    
                    Browser memblokir akses kamera karena permissions policy.
                    
                    SOLUSI:
                    1. Tambahkan meta tag permissions policy
                    2. Atau akses melalui HTTPS
                    3. Atau coba di browser lain
                    
                    Klik OK untuk melihat detail...
                `);
                
                console.log('Permissions policy details:', {
                    camera: document.featurePolicy.allowsFeature('camera'),
                    microphone: document.featurePolicy.allowsFeature('microphone'),
                    geolocation: document.featurePolicy.allowsFeature('geolocation')
                });
                
                return false;
            }
        }
        
        // Test direct camera access
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        
        console.log('✅ Site-specific permission test SUCCESS!');
        alert('✅ Test berhasil! Kamera dapat diakses dari situs ini.');
        
        // Stop stream immediately
        stream.getTracks().forEach(track => track.stop());
        return true;
        
    } catch (err) {
        console.error('❌ Site-specific permission test FAILED:', err);
        
        let solution = '';
        
        if (err.name === 'NotAllowedError') {
            solution = `
                MASALAH: Permission denied khusus untuk situs ini
                
                SOLUSI CEPAT:
                1. Buka situs ini di INCOGNITO/PRIVATE mode
                2. Atau tambahkan permissions policy
                3. Atau reset browser settings
                
                DETAIL:
                • Kamera terdeteksi: ✅ (${await getVideoDeviceCount()} device)
                • Permission state: ❌ Denied untuk situs ini
                • Browser policy: ❌ Violated
                
                Klik OK untuk solusi detail...
            `;
        }
        
        alert(solution);
        return false;
    }
}

// Helper function untuk count video devices
async function getVideoDeviceCount() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        return devices.filter(device => device.kind === 'videoinput').length;
    } catch {
        return 0;
    }
}

// Fungsi untuk clear semua data situs ini
function clearAllSiteData() {
    if (confirm('Ini akan menghapus semua data browser untuk situs ini (cookies, localStorage, permissions). Lanjutkan?')) {
        try {
            // Clear localStorage
            if (localStorage) localStorage.clear();
            
            // Clear sessionStorage  
            if (sessionStorage) sessionStorage.clear();
            
            // Clear any cached data
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                });
            }
            
            alert('Data situs telah dihapus. Halaman akan di-refresh dalam 2 detik...');
            
            setTimeout(() => {
                window.location.reload(true); // Force reload
            }, 2000);
            
        } catch (err) {
            console.error('Error clearing site data:', err);
            alert('Error clearing site data. Coba manual clear browser data (Ctrl+Shift+Delete)');
        }
    }
}

// Perbaikan untuk modal handling tanpa jQuery
function showCameraModal() {
    const modal = document.getElementById('cameraModal');
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'cameraModalBackdrop';
        document.body.appendChild(backdrop);
    }
}

function hideCameraModal() {
    const modal = document.getElementById('cameraModal');
    const backdrop = document.getElementById('cameraModalBackdrop');
    
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
    
    if (backdrop) {
        backdrop.remove();
    }
    
    // Clean up camera
    stopCamera();
    const video = document.getElementById('camera');
    if (video) {
        video.src = '';
        video.srcObject = null;
    }
}

// Missing functions for alternative camera options

// Fungsi untuk menampilkan alternatif kamera
function showCameraAlternatives() {
    const alternativeArea = document.getElementById('alternativeCameraArea');
    alternativeArea.style.display = 'block';
    
    // Scroll to the alternative area
    alternativeArea.scrollIntoView({ behavior: 'smooth' });
    
    console.log('=== SHOWING CAMERA ALTERNATIVES ===');
}

// Fungsi untuk menyembunyikan alternatif kamera
function hideAlternativeCamera() {
    const alternativeArea = document.getElementById('alternativeCameraArea');
    alternativeArea.style.display = 'none';
}

// Fungsi untuk membuka Windows Camera app
function openWindowsCamera() {
    // Try different methods to open Windows Camera
    try {
        // Method 1: Try using ms-camera protocol (Windows 10/11)
        window.open('microsoft.windows.camera:', '_blank');
    } catch (err) {
        console.log('ms-camera protocol failed, showing instructions');
    }
    
    // Show instructions regardless
    alert(`
        🎥 CARA MENGGUNAKAN WINDOWS CAMERA:
        
        1. Tekan Windows Key + S
        2. Ketik "Camera" 
        3. Klik aplikasi "Camera"
        4. Ambil foto dengan tombol capture
        5. Foto tersimpan di: Pictures > Camera Roll
        6. Kembali ke halaman ini
        7. Klik "Select File" dan pilih foto yang baru diambil
        
        ATAU
        
        1. Buka File Explorer
        2. Ketik "camera" di search bar Start Menu
        3. Klik aplikasi "Camera"
        
        Lokasi foto: C:\\Users\\[Username]\\Pictures\\Camera Roll\\
    `);
    
    console.log('=== OPENING WINDOWS CAMERA INSTRUCTIONS ===');
}

// Fungsi untuk menampilkan QR Code area (moved to end of file)

// Fungsi untuk menyembunyikan QR Code area
function hideQRCode() {
    const qrArea = document.getElementById('qrCodeArea');
    qrArea.style.display = 'none';
}

// Fungsi untuk generate QR Code (real implementation with qrcode.js)
function generateQRCode() {
    const canvas = document.getElementById('qrCanvas');
    
    // Create a mobile upload URL (you would need to implement the actual endpoint)
    const currentUrl = window.location.href;
    const mobileUploadUrl = currentUrl.replace(/\/[^\/]*$/, '') + '/mobile-upload?session=' + Date.now();
    
    // Check if QRCode library is loaded
    if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(canvas, mobileUploadUrl, {
            width: 256,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error) {
            if (error) {
                console.error('QR Code generation failed:', error);
                // Fallback to placeholder
                generatePlaceholderQRCode();
            } else {
                console.log('Real QR Code generated successfully');
                console.log('Mobile upload URL:', mobileUploadUrl);
            }
        });
    } else {
        console.warn('QRCode library not loaded, using placeholder');
        generatePlaceholderQRCode();
    }
}

// Fallback QR code placeholder
function generatePlaceholderQRCode() {
    const canvas = document.getElementById('qrCanvas');
    const ctx = canvas.getContext('2d');
    
    // Clear canvas
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, 256, 256);
    
    // Draw simple QR pattern placeholder
    ctx.fillStyle = '#000000';
    
    // Corner markers
    drawQRCorner(ctx, 20, 20);
    drawQRCorner(ctx, 180, 20);
    drawQRCorner(ctx, 20, 180);
    
    // Random pattern for demo
    for (let i = 60; i < 196; i += 8) {
        for (let j = 60; j < 196; j += 8) {
            if (Math.random() > 0.5) {
                ctx.fillRect(i, j, 8, 8);
            }
        }
    }
    
    // Add text overlay
    ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
    ctx.fillRect(80, 115, 96, 26);
    ctx.fillStyle = '#FF0000';
    ctx.font = 'bold 12px Arial';
    ctx.fillText('DEMO QR', 110, 132);
    
    console.log('QR Code placeholder generated');
}

// Helper function to draw QR corner markers
function drawQRCorner(ctx, x, y) {
    // Outer border
    ctx.fillRect(x, y, 56, 56);
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(x + 8, y + 8, 40, 40);
    ctx.fillStyle = '#000000';
    ctx.fillRect(x + 16, y + 16, 24, 24);
}

// Fungsi untuk membuka aplikasi kamera eksternal
function openExternalCamera(type) {
    let instructions = '';
    
    switch (type) {
        case 'snap':
            instructions = `
                📸 SNAP CAMERA:
                
                1. Download Snap Camera dari snapcamera.snapchat.com
                2. Install dan jalankan aplikasi
                3. Pilih filter yang diinginkan
                4. Buka browser dan gunakan fitur kamera
                5. Snap Camera akan menggantikan kamera default
                
                CATATAN: Snap Camera sudah tidak dikembangkan lagi,
                tapi masih bisa digunakan jika sudah terinstall.
            `;
            break;
            
        case 'obs':
            instructions = `
                🎥 OBS VIRTUAL CAMERA:
                
                1. Download OBS Studio dari obsproject.com
                2. Install dan buka OBS Studio
                3. Setup scene dengan camera source
                4. Klik "Start Virtual Camera" di controls
                5. Buka browser dan pilih "OBS Virtual Camera"
                
                KEUNTUNGAN:
                • Bisa tambah overlay, text, effects
                • Kontrol penuh atas output video
                • Gratis dan open source
            `;
            break;
            
        case 'cheese':
            instructions = `
                📷 APLIKASI KAMERA LAIN:
                
                WINDOWS:
                • Camera (built-in Windows app)
                • ManyCam (virtual camera dengan effects)
                • YouCam (camera dengan filters)
                
                LANGKAH UMUM:
                1. Install aplikasi kamera pilihan
                2. Ambil foto dengan aplikasi tersebut
                3. Save foto ke folder Pictures
                4. Upload melalui "Select File" di form ini
                
                REKOMENDASI:
                • Windows Camera (paling mudah)
                • ManyCam (untuk effects)
            `;
            break;
    }
    
    alert(instructions);
    console.log(`=== EXTERNAL CAMERA INSTRUCTIONS: ${type.toUpperCase()} ===`);
}

// Fungsi untuk get current page URL for QR code
function getCurrentPageURL() {
    return window.location.href;
}

// Add some additional utilities for better mobile upload experience
function showMobileUploadInstructions() {
    alert(`
        📱 MOBILE UPLOAD VIA QR CODE:
        
        1. Scan QR code dengan smartphone camera
        2. Browser akan membuka link upload
        3. Ambil foto atau pilih dari galeri
        4. Upload akan otomatis sync ke form ini
        
        ALTERNATIF:
        • WhatsApp Web: Kirim foto ke diri sendiri
        • Google Drive: Upload dari mobile, download di PC
        • Email: Kirim foto via email
        • USB Cable: Transfer langsung
        
        QR Code akan refresh setiap 5 menit untuk keamanan.
    `);
}

// Auto-refresh QR code for security
let qrRefreshInterval;

function startQRRefresh() {
    // Refresh QR code every 5 minutes
    qrRefreshInterval = setInterval(() => {
        if (document.getElementById('qrCodeArea').style.display !== 'none') {
            console.log('Refreshing QR code for security...');
            generateQRCode();
        }
    }, 300000); // 5 minutes
}

function stopQRRefresh() {
    if (qrRefreshInterval) {
        clearInterval(qrRefreshInterval);
        qrRefreshInterval = null;
    }
}

// Start QR refresh when QR is shown
function showQRCode() {
    const qrArea = document.getElementById('qrCodeArea');
    qrArea.style.display = 'block';
    
    // Generate QR code
    generateQRCode();
    
    // Start auto-refresh
    startQRRefresh();
    
    // Show instructions
    setTimeout(showMobileUploadInstructions, 1000);
    
    // Scroll to QR area
    qrArea.scrollIntoView({ behavior: 'smooth' });
    
    console.log('=== SHOWING QR CODE FOR MOBILE UPLOAD ===');
}

// Stop QR refresh when QR is hidden
function hideQRCode() {
    const qrArea = document.getElementById('qrCodeArea');
    qrArea.style.display = 'none';
    
    // Stop auto-refresh
    stopQRRefresh();
}

// Force permission reset function
function forcePermissionReset() {
    alert(`FORCE RESET PERMISSION - Ikuti langkah berikut:

CHROME:
1. Klik ikon gembok/info di sebelah URL
2. Klik "Site settings" atau "Pengaturan situs"
3. Scroll ke "Camera" → pilih "Allow"
4. Refresh halaman

FIREFOX:  
1. Klik ikon shield di sebelah URL
2. Klik "Turn off blocking for this site"
3. Atau buka Menu → Settings → Privacy & Security
4. Scroll ke Permissions → Camera → Settings
5. Hapus situs ini dari daftar blocked

EDGE:
1. Klik ikon gembok di address bar
2. Klik "Permissions for this site"
3. Set Camera ke "Allow"
4. Refresh halaman

ALTERNATIF:
• Coba mode incognito/private
• Restart browser
• Clear browser data (Ctrl+Shift+Delete)
• Reset browser settings`);
    
    console.log('=== FORCE PERMISSION RESET INSTRUCTIONS SHOWN ===');
}

// Open incognito/private window
function openInIncognito() {
    const currentURL = window.location.href;
    
    alert(`BUKA MODE INCOGNITO/PRIVATE:

CHROME: Ctrl+Shift+N
FIREFOX: Ctrl+Shift+P  
EDGE: Ctrl+Shift+N
SAFARI: Cmd+Shift+N (Mac)

Kemudian buka URL ini di mode incognito:
${currentURL}

Mode incognito akan reset semua permission dan memberikan fresh start untuk akses kamera.

Note: Anda perlu login ulang di mode incognito.`);
    
    // Try to open in new incognito window (may be blocked)
    try {
        // This usually won't work due to browser security, but worth trying
        window.open(currentURL, '_blank', 'incognito=yes');
    } catch (err) {
        console.log('Cannot programmatically open incognito window, showing manual instructions');
    }
    
    console.log('=== INCOGNITO MODE INSTRUCTIONS SHOWN ===');
}

// Advanced permission debugging
async function advancedPermissionDebug() {
    console.log('=== ADVANCED PERMISSION DEBUG ===');
    
    const debug = {
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        platform: navigator.platform,
        cookieEnabled: navigator.cookieEnabled,
        doNotTrack: navigator.doNotTrack,
        permissions: {},
        mediaDevices: {},
        errors: []
    };
    
    try {
        // Check navigator.mediaDevices availability
        debug.mediaDevices.available = !!navigator.mediaDevices;
        debug.mediaDevices.getUserMediaAvailable = !!navigator.mediaDevices?.getUserMedia;
        
        // Check permissions API
        if (navigator.permissions) {
            try {
                const cameraPermission = await navigator.permissions.query({name: 'camera'});
                debug.permissions.camera = {
                    state: cameraPermission.state,
                    name: cameraPermission.name
                };
            } catch (err) {
                debug.permissions.camera = { error: err.message };
            }
            
            try {
                const micPermission = await navigator.permissions.query({name: 'microphone'});
                debug.permissions.microphone = {
                    state: micPermission.state,
                    name: micPermission.name
                };
            } catch (err) {
                debug.permissions.microphone = { error: err.message };
            }
        } else {
            debug.permissions.api = 'not available';
        }
        
        // Try enumerating devices
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            debug.mediaDevices.deviceCount = devices.length;
            debug.mediaDevices.cameras = devices.filter(d => d.kind === 'videoinput').length;
            debug.mediaDevices.microphones = devices.filter(d => d.kind === 'audioinput').length;
            debug.mediaDevices.speakers = devices.filter(d => d.kind === 'audiooutput').length;
        } catch (err) {
            debug.mediaDevices.enumerateError = err.message;
        }
        
        // Test basic getUserMedia call
        try {
            const stream = await navigator.mediaDevices.getUserMedia({video: true});
            debug.mediaDevices.getUserMediaTest = 'SUCCESS';
            // Stop the stream immediately
            stream.getTracks().forEach(track => track.stop());
        } catch (err) {
            debug.mediaDevices.getUserMediaTest = `FAILED: ${err.name} - ${err.message}`;
        }
        
    } catch (err) {
        debug.errors.push(`Main debug error: ${err.message}`);
    }
    
    console.log('Advanced Debug Results:', debug);
    
    // Show user-friendly summary
    let summary = `ADVANCED PERMISSION DEBUG RESULTS:

Browser: ${debug.userAgent.split(' ')[0]}
Platform: ${debug.platform}

MEDIA DEVICES:
• Available: ${debug.mediaDevices.available ? 'YES' : 'NO'}
• getUserMedia: ${debug.mediaDevices.getUserMediaAvailable ? 'YES' : 'NO'}
• Cameras detected: ${debug.mediaDevices.cameras || 'Unknown'}

PERMISSIONS:
• Camera permission: ${debug.permissions.camera?.state || 'Unknown'}
• Microphone permission: ${debug.permissions.microphone?.state || 'Unknown'}

TEST RESULTS:
• getUserMedia test: ${debug.mediaDevices.getUserMediaTest || 'Not tested'}`;

    if (debug.errors.length > 0) {
        summary += `\n\nERRORS:\n${debug.errors.join('\n')}`;
    }
    
    alert(summary);
    
    return debug;
}

// Comprehensive camera access test with detailed feedback
async function comprehensiveCameraTest() {
    console.log('=== COMPREHENSIVE CAMERA TEST ===');
    
    const results = {
        timestamp: new Date().toISOString(),
        tests: [],
        summary: '',
        recommendations: []
    };
    
    // Test 1: Basic browser support
    results.tests.push({
        name: 'Browser Support',
        status: !!navigator.mediaDevices ? 'PASS' : 'FAIL',
        details: navigator.mediaDevices ? 'mediaDevices API available' : 'mediaDevices API not available'
    });
    
    // Test 2: HTTPS/Secure context
    results.tests.push({
        name: 'Secure Context',
        status: window.isSecureContext ? 'PASS' : 'FAIL',
        details: window.isSecureContext ? 'Running in secure context (HTTPS/localhost)' : 'Not in secure context - camera requires HTTPS'
    });
    
    // Test 3: Permissions Policy
    let policyAllowed = true;
    let policyDetails = '';
    
    try {
        if (document.featurePolicy) {
            const cameraAllowed = document.featurePolicy.allowsFeature('camera');
            policyAllowed = cameraAllowed;
            policyDetails = `Feature Policy: camera ${cameraAllowed ? 'allowed' : 'blocked'}`;
        } else if (document.permissionsPolicy) {
            const cameraAllowed = document.permissionsPolicy.allowsFeature('camera');
            policyAllowed = cameraAllowed;
            policyDetails = `Permissions Policy: camera ${cameraAllowed ? 'allowed' : 'blocked'}`;
        } else {
            policyDetails = 'No permissions policy API available';
        }
        
        results.tests.push({
            name: 'Permissions Policy',
            status: policyAllowed ? 'PASS' : 'FAIL',
            details: policyDetails
        });
        
        if (!policyAllowed) {
            results.recommendations.push('Permissions policy violation - try incognito mode');
            results.recommendations.push('Add proper iframe allow attributes if embedded');
        }
        
    } catch (err) {
        results.tests.push({
            name: 'Permissions Policy',
            status: 'ERROR',
            details: `Policy check failed: ${err.message}`
        });
    }
    
    // Test 4: Permission API
    let permissionState = 'unknown';
    if (navigator.permissions) {
        try {
            const permission = await navigator.permissions.query({name: 'camera'});
            permissionState = permission.state;
            results.tests.push({
                name: 'Permission State',
                status: permission.state === 'granted' ? 'PASS' : permission.state === 'prompt' ? 'PENDING' : 'FAIL',
                details: `Current permission state: ${permission.state}`
            });
        } catch (err) {
            results.tests.push({
                name: 'Permission State',
                status: 'ERROR',
                details: `Cannot check permission: ${err.message}`
            });
        }
    } else {
        results.tests.push({
            name: 'Permission API',
            status: 'UNAVAILABLE',
            details: 'Permissions API not supported in this browser'
        });
    }
    
    // Test 5: Device enumeration
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const cameras = devices.filter(d => d.kind === 'videoinput');
        results.tests.push({
            name: 'Camera Detection',
            status: cameras.length > 0 ? 'PASS' : 'FAIL',
            details: `Found ${cameras.length} camera(s): ${cameras.map(c => c.label || 'Unknown device').join(', ')}`
        });
    } catch (err) {
        results.tests.push({
            name: 'Camera Detection',
            status: 'ERROR',
            details: `Cannot enumerate devices: ${err.message}`
        });
    }
    
    // Test 6: Actual camera access
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 320, height: 240 }
        });
        
        results.tests.push({
            name: 'Camera Access',
            status: 'PASS',
            details: 'Successfully accessed camera'
        });
        
        // Stop stream immediately
        stream.getTracks().forEach(track => track.stop());
        
    } catch (err) {
        results.tests.push({
            name: 'Camera Access',
            status: 'FAIL',
            details: `${err.name}: ${err.message}`
        });
        
        // Add specific recommendations based on error
        switch (err.name) {
            case 'NotAllowedError':
                results.recommendations.push('Permission denied - use Force Reset Permission button');
                results.recommendations.push('Try opening in incognito/private mode');
                break;
            case 'NotFoundError':
                results.recommendations.push('No camera found - check hardware connection');
                results.recommendations.push('Close other applications using camera');
                break;
            case 'NotSupportedError':
                results.recommendations.push('Browser/environment not supported - use HTTPS');
                results.recommendations.push('Try different browser (Chrome, Firefox, Edge)');
                break;
        }
    }
    
    // Generate summary
    const passCount = results.tests.filter(t => t.status === 'PASS').length;
    const failCount = results.tests.filter(t => t.status === 'FAIL').length;
    const errorCount = results.tests.filter(t => t.status === 'ERROR').length;
    
    results.summary = `${passCount} tests passed, ${failCount} failed, ${errorCount} errors`;
    
    // General recommendations
    if (permissionState === 'denied') {
        results.recommendations.push('Permission permanently denied - manual reset required');
    }
    if (!window.isSecureContext) {
        results.recommendations.push('Use HTTPS or localhost for camera access');
    }
    if (failCount > 0 || errorCount > 0) {
        results.recommendations.push('Try Windows Camera app as alternative');
        results.recommendations.push('Use mobile phone camera with QR code upload');
    }
    
    console.log('Comprehensive Test Results:', results);
    
    // Show user-friendly results
    let display = `COMPREHENSIVE CAMERA TEST RESULTS\n\n`;
    display += `Summary: ${results.summary}\n\n`;
    
    display += `DETAILED RESULTS:\n`;
    results.tests.forEach(test => {
        display += `• ${test.name}: ${test.status}\n  ${test.details}\n\n`;
    });
    
    if (results.recommendations.length > 0) {
        display += `RECOMMENDATIONS:\n`;
        results.recommendations.forEach(rec => {
            display += `• ${rec}\n`;
        });
    }
    
    alert(display);
    
    return results;
}

// Check permissions policy status
function checkPermissionsPolicyStatus() {
    console.log('=== CHECKING PERMISSIONS POLICY STATUS ===');
    
    let policyInfo = {
        timestamp: new Date().toISOString(),
        isSecureContext: window.isSecureContext,
        protocol: location.protocol,
        hostname: location.hostname,
        featurePolicy: {},
        permissionsPolicy: {},
        documentPolicy: {},
        iframeContext: window !== window.parent,
        errors: []
    };
    
    try {
        // Check Feature Policy (older API)
        if (document.featurePolicy) {
            policyInfo.featurePolicy.available = true;
            policyInfo.featurePolicy.camera = document.featurePolicy.allowsFeature('camera');
            policyInfo.featurePolicy.microphone = document.featurePolicy.allowsFeature('microphone');
            policyInfo.featurePolicy.allowedFeatures = document.featurePolicy.allowedFeatures();
        } else {
            policyInfo.featurePolicy.available = false;
        }
        
        // Check Permissions Policy (newer API)
        if (document.permissionsPolicy) {
            policyInfo.permissionsPolicy.available = true;
            try {
                policyInfo.permissionsPolicy.camera = document.permissionsPolicy.allowsFeature('camera');
                policyInfo.permissionsPolicy.microphone = document.permissionsPolicy.allowsFeature('microphone');
            } catch (err) {
                policyInfo.permissionsPolicy.error = err.message;
            }
        } else {
            policyInfo.permissionsPolicy.available = false;
        }
        
        // Check if we're in an iframe
        if (policyInfo.iframeContext) {
            policyInfo.iframeAllowAttribute = document.querySelector('iframe')?.allow || 'Not found';
        }
        
        // Check meta tags
        const permissionsMeta = document.querySelector('meta[http-equiv="Permissions-Policy"]');
        const featureMeta = document.querySelector('meta[http-equiv="Feature-Policy"]');
        
        policyInfo.metaTags = {
            permissionsPolicy: permissionsMeta?.content || 'Not found',
            featurePolicy: featureMeta?.content || 'Not found'
        };
        
    } catch (err) {
        policyInfo.errors.push(`Policy check error: ${err.message}`);
    }
    
    console.log('Permissions Policy Info:', policyInfo);
    
    // Generate user-friendly report
    let report = `PERMISSIONS POLICY STATUS REPORT\n\n`;
    
    report += `Environment:\n`;
    report += `• Secure Context: ${policyInfo.isSecureContext ? 'YES' : 'NO'}\n`;
    report += `• Protocol: ${policyInfo.protocol}\n`;
    report += `• Hostname: ${policyInfo.hostname}\n`;
    report += `• In iframe: ${policyInfo.iframeContext ? 'YES' : 'NO'}\n\n`;
    
    if (policyInfo.featurePolicy.available) {
        report += `Feature Policy (Legacy):\n`;
        report += `• Camera allowed: ${policyInfo.featurePolicy.camera ? 'YES' : 'NO'}\n`;
        report += `• Microphone allowed: ${policyInfo.featurePolicy.microphone ? 'YES' : 'NO'}\n\n`;
    }
    
    if (policyInfo.permissionsPolicy.available) {
        report += `Permissions Policy (Current):\n`;
        if (policyInfo.permissionsPolicy.error) {
            report += `• Error: ${policyInfo.permissionsPolicy.error}\n`;
        } else {
            report += `• Camera allowed: ${policyInfo.permissionsPolicy.camera ? 'YES' : 'NO'}\n`;
            report += `• Microphone allowed: ${policyInfo.permissionsPolicy.microphone ? 'YES' : 'NO'}\n`;
        }
        report += '\n';
    }
    
    report += `Meta Tags:\n`;
    report += `• Permissions-Policy: ${policyInfo.metaTags.permissionsPolicy}\n`;
    report += `• Feature-Policy: ${policyInfo.metaTags.featurePolicy}\n\n`;
    
    if (policyInfo.iframeContext) {
        report += `Iframe Context:\n`;
        report += `• Allow attribute: ${policyInfo.iframeAllowAttribute}\n\n`;
    }
    
    if (policyInfo.errors.length > 0) {
        report += `Errors:\n`;
        policyInfo.errors.forEach(error => {
            report += `• ${error}\n`;
        });
        report += '\n';
    }
    
    // Add recommendations
    report += `RECOMMENDATIONS:\n`;
    
    if (!policyInfo.isSecureContext) {
        report += `• Use HTTPS or localhost for secure context\n`;
    }
    
    if (policyInfo.iframeContext) {
        report += `• Add 'camera; microphone' to iframe allow attribute\n`;
        report += `• Example: <iframe allow="camera; microphone" ...>\n`;
    }
    
    if (!policyInfo.featurePolicy.camera && !policyInfo.permissionsPolicy.camera) {
        report += `• Camera is blocked by permissions policy\n`;
        report += `• Try opening in incognito/private mode\n`;
        report += `• Check server headers for Permissions-Policy\n`;
    }
    
    report += `• Restart browser with --disable-web-security flag (for testing)\n`;
    report += `• Use Windows Camera app or mobile upload as alternative\n`;
    
    alert(report);
    
    return policyInfo;
}

// Enhanced incognito mode helper
function openInIncognito() {
    const currentURL = window.location.href;
    
    const instructions = `BUKA MODE INCOGNITO/PRIVATE:

CHROME: 
• Tekan Ctrl+Shift+N
• Atau klik ⋮ (menu) → New incognito window

FIREFOX: 
• Tekan Ctrl+Shift+P
• Atau klik ☰ (menu) → New private window

EDGE: 
• Tekan Ctrl+Shift+N
• Atau klik ⋯ (menu) → New InPrivate window

SAFARI (Mac): 
• Tekan Cmd+Shift+N
• Atau File → New Private Window

Kemudian copy-paste URL ini:
${currentURL}

WHY INCOGNITO WORKS:
• Reset semua permissions policy violations
• Mengabaikan kebijakan keamanan yang tersimpan
• Memberikan fresh start untuk akses kamera
• Tidak terpengaruh oleh cache atau cookies

ALTERNATIVE JIKA INCOGNITO TIDAK MEMBANTU:
• Gunakan Windows Camera app (tombol "Buka Windows Camera")
• Upload foto dari smartphone via QR code
• Restart browser dengan parameter --disable-web-security`;
    
    alert(instructions);
    
    // Try to open in new window (usually blocked by browsers)
    try {
        window.open(currentURL, '_blank', 'incognito=yes,private=yes');
    } catch (err) {
        console.log('Cannot programmatically open incognito window');
    }
    
    console.log('=== INCOGNITO MODE INSTRUCTIONS SHOWN ===');
}

// Emergency diagnostic for complete camera failure
function emergencyDiagnostic() {
    console.log('=== EMERGENCY DIAGNOSTIC ===');
    
    let diagnostic = {
        timestamp: new Date().toISOString(),
        issues: [],
        solutions: [],
        browserInfo: {
            userAgent: navigator.userAgent,
            vendor: navigator.vendor,
            platform: navigator.platform,
            cookieEnabled: navigator.cookieEnabled,
            onLine: navigator.onLine
        },
        securityContext: {
            isSecureContext: window.isSecureContext,
            protocol: location.protocol,
            hostname: location.hostname,
            port: location.port,
            origin: location.origin
        },
        apis: {
            mediaDevices: !!navigator.mediaDevices,
            getUserMedia: !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia),
            permissions: !!navigator.permissions,
            featurePolicy: !!document.featurePolicy,
            permissionsPolicy: !!document.permissionsPolicy
        }
    };
    
    // Analyze issues
    if (!window.isSecureContext) {
        diagnostic.issues.push('NOT SECURE CONTEXT - Camera requires HTTPS or localhost');
        diagnostic.solutions.push('Use HTTPS or access via localhost/127.0.0.1');
    }
    
    if (!navigator.mediaDevices) {
        diagnostic.issues.push('MEDIA DEVICES API NOT AVAILABLE');
        diagnostic.solutions.push('Update browser to latest version');
        diagnostic.solutions.push('Try different browser (Chrome, Firefox, Edge)');
    }
    
    if (!navigator.mediaDevices?.getUserMedia) {
        diagnostic.issues.push('getUserMedia NOT AVAILABLE');
        diagnostic.solutions.push('Browser does not support camera access');
    }
    
    // Check permissions policy
    let policyBlocked = false;
    try {
        if (document.featurePolicy && !document.featurePolicy.allowsFeature('camera')) {
            diagnostic.issues.push('FEATURE POLICY BLOCKS CAMERA');
            policyBlocked = true;
        }
        if (document.permissionsPolicy && !document.permissionsPolicy.allowsFeature('camera')) {
            diagnostic.issues.push('PERMISSIONS POLICY BLOCKS CAMERA');
            policyBlocked = true;
        }
        
        if (policyBlocked) {
            diagnostic.solutions.push('CRITICAL: Open in Incognito/Private mode');
            diagnostic.solutions.push('Use browser flags: --disable-web-security');
            diagnostic.solutions.push('Use alternative: Windows Camera app');
        }
    } catch (err) {
        diagnostic.issues.push(`POLICY CHECK ERROR: ${err.message}`);
    }
    
    // Check if in iframe
    if (window !== window.parent) {
        diagnostic.issues.push('RUNNING IN IFRAME - Permissions may be restricted');
        diagnostic.solutions.push('Open page directly (not in iframe)');
        diagnostic.solutions.push('Ensure iframe has allow="camera" attribute');
    }
    
    // Browser-specific issues
    const isChrome = navigator.userAgent.includes('Chrome');
    const isFirefox = navigator.userAgent.includes('Firefox');
    const isEdge = navigator.userAgent.includes('Edg');
    const isSafari = navigator.userAgent.includes('Safari') && !isChrome;
    
    if (isChrome || isEdge) {
        diagnostic.solutions.push('Chrome/Edge: Try chrome://settings/content/camera');
        diagnostic.solutions.push('Chrome/Edge: Use --allow-running-insecure-content flag');
    }
    
    if (isFirefox) {
        diagnostic.solutions.push('Firefox: Try about:config → media.navigator.permission.disabled = true');
    }
    
    // Generate emergency report
    let report = `🚨 EMERGENCY CAMERA DIAGNOSTIC REPORT 🚨\n\n`;
    
    report += `BROWSER: ${diagnostic.browserInfo.userAgent.split(' ')[0]}\n`;
    report += `SECURITY: ${diagnostic.securityContext.isSecureContext ? '✅ Secure' : '❌ Not Secure'}\n`;
    report += `URL: ${diagnostic.securityContext.origin}\n\n`;
    
    if (diagnostic.issues.length > 0) {
        report += `❌ CRITICAL ISSUES FOUND:\n`;
        diagnostic.issues.forEach((issue, index) => {
            report += `${index + 1}. ${issue}\n`;
        });
        report += '\n';
    }
    
    report += `🔧 IMMEDIATE SOLUTIONS (Try in order):\n\n`;
    
    // Priority solutions
    report += `PRIORITY 1 - INCOGNITO MODE:\n`;
    report += `• Press Ctrl+Shift+N (Chrome/Edge) or Ctrl+Shift+P (Firefox)\n`;
    report += `• Open this page in incognito/private window\n`;
    report += `• This bypasses most permission policies\n\n`;
    
    report += `PRIORITY 2 - WINDOWS CAMERA APP:\n`;
    report += `• Click "Buka Windows Camera" button above\n`;
    report += `• Take photo with Windows Camera app\n`;
    report += `• Use "Select File" to upload the photo\n\n`;
    
    report += `PRIORITY 3 - MOBILE PHONE:\n`;
    report += `• Click "Alternatif Kamera" → "Generate QR untuk Mobile Upload"\n`;
    report += `• Take photo with phone camera\n`;
    report += `• Transfer via WhatsApp Web, email, or USB\n\n`;
    
    if (diagnostic.solutions.length > 0) {
        report += `ADVANCED SOLUTIONS:\n`;
        diagnostic.solutions.forEach((solution, index) => {
            report += `• ${solution}\n`;
        });
        report += '\n';
    }
    
    report += `BROWSER FLAGS (Advanced Users):\n`;
    report += `• Close all browser windows\n`;
    report += `• Start browser with: --disable-web-security --allow-running-insecure-content\n`;
    report += `• Example: chrome.exe --disable-web-security --allow-running-insecure-content\n\n`;
    
    report += `If ALL solutions fail, the issue is likely:\n`;
    report += `• Windows Privacy Settings blocking camera\n`;
    report += `• Antivirus/Security software blocking camera\n`;
    report += `• Hardware/driver issues\n`;
    report += `• Corporate/network restrictions\n`;
    
    alert(report);
    
    console.log('Emergency Diagnostic Results:', diagnostic);
    
    return diagnostic;
}

// Quick alternatives when camera completely fails
function quickAlternatives() {
    const alternatives = `🚀 QUICK CAMERA ALTERNATIVES 🚀

FASTEST SOLUTIONS:

1️⃣ WINDOWS CAMERA APP (30 seconds)
   • Click "Buka Windows Camera" button above
   • Take photo in Windows Camera app
   • Photos saved to Pictures/Camera Roll
   • Click "Select File" and choose the photo

2️⃣ SMARTPHONE UPLOAD (1 minute)
   • Click "Alternatif Kamera" button above
   • Click "Generate QR untuk Mobile Upload"
   • Scan QR with phone camera
   • Take photo or choose from gallery

3️⃣ WHATSAPP WEB (2 minutes)
   • Take photo with phone
   • Send to yourself via WhatsApp
   • Open WhatsApp Web on computer
   • Download photo and use "Select File"

4️⃣ EMAIL METHOD (2 minutes)
   • Take photo with phone
   • Email photo to yourself
   • Download from email on computer
   • Use "Select File" to upload

5️⃣ USB TRANSFER (3 minutes)
   • Take photo with phone
   • Connect phone to computer via USB
   • Copy photo from phone to computer
   • Use "Select File" to upload

6️⃣ CLOUD STORAGE (3 minutes)
   • Take photo with phone
   • Upload to Google Drive/OneDrive
   • Download on computer
   • Use "Select File" to upload

IMMEDIATE ACTION:
Click "Buka Windows Camera" or "Alternatif Kamera" buttons above!

These methods work 100% regardless of browser issues.`;

    alert(alternatives);
    
    // Auto-show Windows Camera instructions
    setTimeout(() => {
        if (confirm('Would you like to try Windows Camera app now?\n\nClick OK to see instructions, Cancel to skip.')) {
            showCameraAlternatives();
        }
    }, 1000);
    
    console.log('=== QUICK ALTERNATIVES SHOWN ===');
}

// Show emergency banner when camera fails completely
function showEmergencyBanner() {
    const banner = document.getElementById('emergencyBanner');
    banner.style.display = 'block';
    
    // Scroll to banner
    banner.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
    
    // Add attention animation
    banner.style.animation = 'pulse 2s infinite';
    
    console.log('=== EMERGENCY BANNER SHOWN ===');
}

// Hide emergency banner
function hideEmergencyBanner() {
    const banner = document.getElementById('emergencyBanner');
    banner.style.display = 'none';
}

// Auto-detect camera failure and show emergency banner
function detectCameraFailure(errorName, errorMessage) {
    const criticalErrors = [
        'NotAllowedError',
        'PermissionDeniedError', 
        'NotSupportedError'
    ];
    
    const policyViolation = errorMessage && (
        errorMessage.includes('Permissions policy') ||
        errorMessage.includes('Feature-Policy') ||
        errorMessage.includes('not allowed')
    );
    
    if (criticalErrors.includes(errorName) || policyViolation) {
        console.log('CRITICAL CAMERA FAILURE DETECTED - Showing emergency banner');
        setTimeout(() => {
            showEmergencyBanner();
        }, 2000); // Show after 2 seconds to let user see the error first
        
        // Auto-show alternatives after banner is shown
        setTimeout(() => {
            if (confirm('Camera access is completely blocked.\n\nWould you like to see alternative upload methods?\n\nClick OK for instant solutions.')) {
                quickAlternatives();
            }
        }, 4000);
        
        return true;
    }
    
    return false;
}

// Auto-run comprehensive test and show solutions
function autoFixCameraIssues() {
    console.log('=== AUTO-FIX CAMERA ISSUES ===');
    
    // First run comprehensive test
    comprehensiveCameraTest().then(results => {
        const failCount = results.tests.filter(t => t.status === 'FAIL').length;
        const errorCount = results.tests.filter(t => t.status === 'ERROR').length;
        
        if (failCount >= 3 || errorCount >= 2) {
            // Multiple failures detected
            setTimeout(() => {
                alert('Multiple camera system failures detected!\n\nShowing emergency solutions...');
                emergencyDiagnostic();
            }, 2000);
        }
    }).catch(err => {
        console.error('Auto-fix test failed:', err);
        setTimeout(() => {
            quickAlternatives();
        }, 1000);
    });
}
</script>

