self.addEventListener('push', function(event) {
    console.log('Push notification received:', event);
    
    let notificationData = {
        title: 'New Notification',
        body: 'You have a new notification',
        icon: '/user/img/logo.png',
        badge: '/user/img/logo.png',
        data: {
            url: '/notifications'
        }
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            notificationData = {
                title: payload.title || notificationData.title,
                body: payload.message || payload.body || notificationData.body,
                icon: payload.icon || notificationData.icon,
                badge: payload.badge || notificationData.badge,
                data: {
                    url: payload.link || payload.url || notificationData.data.url
                }
            };
        } catch (e) {
            console.error('Error parsing push notification:', e);
        }
    }

    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            data: notificationData.data,
            requireInteraction: false,
            vibrate: [200, 100, 200]
        })
    );
});

self.addEventListener('notificationclick', function(event) {
    console.log('Notification clicked:', event);
    
    event.notification.close();

    const urlToOpen = event.notification.data && event.notification.data.url 
        ? event.notification.data.url 
        : '/notifications';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {
            // Check if there's already a window open
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.indexOf(urlToOpen) >= 0 && 'focus' in client) {
                    return client.focus();
                }
            }
            // If no window is open, open a new one
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
    event.waitUntil(clients.claim());
});
