'use strict';

self.addEventListener('install', event => console.log('Service Worker installing.'));

self.addEventListener('activate', event => console.log('Service Worker activating.'));

self.addEventListener('push', event => {
    console.log('[Service Worker] Push received.');
    console.log(`[Service Worker] Push had this data: "${event.data.text()}".`);

    const data = event.data.json();
    const title = data.title;
    const options = Object.assign({
        badge: 'assets/images/notification-default-badge.png',
        icon: 'assets/images/notification-default-icon.png'
    }, data);

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', event => {
    console.log('[Service Worker] Notification click received.');

    const link = event.notification.data.link;
    if (!link)
        return;

    event.notification.close();
    event.waitUntil(clients.openWindow(link));
});