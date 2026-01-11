// Konfigurasi Firebase
// GANTI dengan konfigurasi Firebase project Anda dari Firebase Console
// Buka: Firebase Console > Project Settings > General > Your apps > Web app

const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID",
    vapidKey: "YOUR_VAPID_KEY" // Dapatkan dari Cloud Messaging > Web Push certificates
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Cloud Messaging
const messaging = firebase.messaging();

// Request permission untuk notifications
messaging.requestPermission().then(function() {
    console.log('Notification permission granted.');
    return messaging.getToken();
}).then(function(token) {
    console.log('FCM Token:', token);
    // Simpan token ke server
    if (token) {
        saveTokenToServer(token);
    }
}).catch(function(err) {
    console.log('Unable to get permission to notify.', err);
});

// Handler untuk token refresh
messaging.onTokenRefresh(function() {
    messaging.getToken().then(function(refreshedToken) {
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
    console.log('Message received. ', payload);
    
    // Tampilkan notifikasi di foreground
    if (payload.notification) {
        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
            icon: '/assets/favicon.ico',
            badge: '/assets/favicon.ico'
        };
        
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notificationTitle, notificationOptions);
        }
    }
});

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



