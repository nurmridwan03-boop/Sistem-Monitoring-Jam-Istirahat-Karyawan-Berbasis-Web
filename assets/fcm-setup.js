/**
 * Firebase Cloud Messaging Setup
 * File ini di-include di semua halaman untuk mendaftarkan FCM token
 * Notifikasi akan muncul sebagai browser notification bahkan saat user tidak di halaman
 */

// Konfigurasi Firebase - GANTI dengan config Anda dari Firebase Console
const FCM_CONFIG = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID",
    vapidKey: "YOUR_VAPID_KEY"
};

// Tunggu sampai Firebase SDK dimuat
(function() {
    // Check if Firebase is already loaded, if not wait a bit
    function initFCM() {
        if (typeof firebase === 'undefined') {
            console.warn('Firebase SDK belum dimuat, mencoba lagi...');
            setTimeout(initFCM, 100);
            return;
        }
        
        try {
            // Initialize Firebase
            if (!firebase.apps.length) {
                firebase.initializeApp(FCM_CONFIG);
            }
            
            const messaging = firebase.messaging();
            
            // Fungsi untuk menyimpan token ke server
            function saveTokenToServer(token) {
                fetch('save_fcm_token.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        token: token,
                        device_info: navigator.userAgent
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Token saved:', data);
                })
                .catch(error => {
                    console.error('Error saving token:', error);
                });
            }
            
            // Register Service Worker untuk background notifications
            if ('serviceWorker' in navigator && 'Notification' in window) {
                navigator.serviceWorker.register('/firebase-messaging-sw.js')
                    .then(function(registration) {
                        console.log('Service Worker registered:', registration);
                        
                        // Request permission untuk notifications
                        return Notification.requestPermission();
                    })
                    .then(function(permission) {
                        if (permission === 'granted') {
                            console.log('Notification permission granted.');
                            // Dapatkan FCM token
                            return messaging.getToken({ 
                                vapidKey: FCM_CONFIG.vapidKey,
                                serviceWorkerRegistration: navigator.serviceWorker.ready
                            });
                        } else {
                            console.log('Notification permission denied.');
                            return null;
                        }
                    })
                    .then(function(token) {
                        if (token) {
                            console.log('FCM Token:', token);
                            // Simpan token ke server
                            saveTokenToServer(token);
                        } else {
                            console.log('No FCM token available.');
                        }
                    })
                    .catch(function(err) {
                        console.error('Error setting up FCM:', err);
                    });
                
                // Handler untuk token refresh
                messaging.onTokenRefresh(function() {
                    messaging.getToken({ 
                        vapidKey: FCM_CONFIG.vapidKey,
                        serviceWorkerRegistration: navigator.serviceWorker.ready
                    }).then(function(refreshedToken) {
                        console.log('Token refreshed:', refreshedToken);
                        if (refreshedToken) {
                            saveTokenToServer(refreshedToken);
                        }
                    }).catch(function(err) {
                        console.log('Unable to retrieve refreshed token ', err);
                    });
                });
                
                // Handler untuk foreground messages (saat aplikasi terbuka)
                messaging.onMessage(function(payload) {
                    console.log('Message received in foreground. ', payload);
                    
                    // Tampilkan notifikasi browser saat aplikasi terbuka
                    if (payload.notification) {
                        const notificationTitle = payload.notification.title || 'Notifikasi Istirahat';
                        const notificationOptions = {
                            body: payload.notification.body || 'Anda memiliki notifikasi baru',
                            icon: '/assets/favicon.ico',
                            badge: '/assets/favicon.ico',
                            tag: 'istirahat-notification',
                            requireInteraction: false,
                            sound: 'default'
                        };
                        
                        // Tampilkan browser notification
                        if (Notification.permission === 'granted') {
                            const notification = new Notification(notificationTitle, notificationOptions);
                            
                            // Handler untuk klik notifikasi
                            notification.onclick = function(event) {
                                event.preventDefault();
                                window.focus();
                                // Redirect ke halaman yang relevan jika diperlukan
                                if (payload.data && payload.data.type === 'istirahat_selesai') {
                                    window.location.href = '/karyawan.php';
                                }
                                notification.close();
                            };
                        }
                    }
                });
            } else {
                console.warn('Service Worker atau Notification tidak didukung di browser ini.');
            }
        } catch (error) {
            console.error('Error initializing FCM:', error);
        }
    }
    
    // Mulai inisialisasi FCM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFCM);
    } else {
        initFCM();
    }
})();
