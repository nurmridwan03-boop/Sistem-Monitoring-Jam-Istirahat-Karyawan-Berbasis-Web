// Service Worker untuk Firebase Cloud Messaging
// File ini harus berada di root directory (bersama index.php)
// Notifikasi akan muncul sebagai browser notification bahkan saat user tidak di halaman

importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

// Inisialisasi Firebase - GANTI dengan config Anda dari Firebase Console
firebase.initializeApp({
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
});

const messaging = firebase.messaging();

// Handler untuk background messages (ketika aplikasi tidak terbuka atau di background)
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    
    const notificationTitle = payload.notification.title || 'Notifikasi Istirahat';
    const notificationOptions = {
        body: payload.notification.body || 'Anda memiliki notifikasi baru',
        icon: '/assets/favicon.ico',
        badge: '/assets/favicon.ico',
        tag: 'istirahat-notification',
        requireInteraction: false,
        silent: false,
        vibrate: [200, 100, 200], // Vibrate pattern untuk mobile
        data: payload.data || {}
    };

    // Tampilkan notifikasi browser (akan muncul di sistem operasi)
    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handler untuk klik notifikasi
self.addEventListener('notificationclick', function(event) {
    console.log('[firebase-messaging-sw.js] Notification click received.');
    event.notification.close();
    
    // Buka aplikasi saat notifikasi diklik
    event.waitUntil(
        clients.matchAll({type: 'window', includeUncontrolled: true}).then(function(clientList) {
            // Jika aplikasi sudah terbuka, fokus ke aplikasi
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url === '/' || client.url.indexOf(self.location.origin) === 0) {
                    return client.focus();
                }
            }
            // Jika aplikasi belum terbuka, buka aplikasi
            if (clients.openWindow) {
                // Redirect ke halaman yang relevan berdasarkan data notifikasi
                if (event.notification.data && event.notification.data.type === 'istirahat_selesai') {
                    return clients.openWindow('/karyawan.php');
                }
                return clients.openWindow('/');
            }
        })
    );
});

// Handler untuk close notifikasi
self.addEventListener('notificationclose', function(event) {
    console.log('[firebase-messaging-sw.js] Notification closed.');
});

