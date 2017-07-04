// TODO(maximegelinas): Extract the UI logic in an other JS file.

'use strict';

// TODO(maximegelinas): Get the public from the server.
const applicationServerPublicKey = 'BETMjzbMvx0HPbS2ch2KlJmmZfbX0cxWQq7wR2Anuzd8MDQOiG7g05192GPDX6vMIWRm92YCfJeAdXxj1RNxhzw';

const serviceWorkerPath = 'service-worker.js';
const pushButton = document.querySelector('.app-push-subscription-btn');

let isSubscribed = false;
let swRegistration = null;

function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i)
        outputArray[i] = rawData.charCodeAt(i);

    return outputArray;
}

function updateBtn() {
    if (Notification.permission === 'denied') {
        pushButton.textContent = 'Notifications blocked';
        pushButton.disabled = true;
        unsubscribeUser();
        return;
    }

    pushButton.textContent =
        isSubscribed ?
        'Disable notifications' :
        'Enable notifications';

    pushButton.disabled = false;
}

function strignifySubscription(subscription) {
    const subscriptionData = JSON.parse(JSON.stringify(subscription));
    return JSON.stringify({
        endpoint: subscriptionData.endpoint,
        userPublicKey: subscriptionData.keys['p256dh'],
        userAuthToken: subscriptionData.keys['auth']
    });
}

function addSubscriptionOnServer(subscription) {
    if (!subscription) {
        console.error('Trying to update invalid push subscription on server.');
        return;
    }

    console.log('Add subscription :', subscription);

    const subscriptionData = JSON.parse(JSON.stringify(subscription));

    return fetch('/api/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(subscription)
        })
        .then(() => subscription);
}

function removeSubscriptionOnServer(subscription) {
    if (!subscription) {
        console.error('Trying to remove invalid push subscription on server.');
        return;
    }

    console.log('Removing subscription :', subscription);

    const publicKey = JSON.parse(JSON.stringify(subscription)).keys['p256dh'];

    return fetch(`/api/push-subscriptions/${publicKey}`, {
            method: 'DELETE'
        })
        .then(() => subscription);
}

function subscribeUser() {
    const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
    return swRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: applicationServerKey
        })
        .then(subscription => subscription ? addSubscriptionOnServer(subscription) : () => subscription)
        .then(() => {
            console.log('User is subscribed.');
            isSubscribed = true;
        })
        .catch(error => {
            if (Notification.permission === 'denied')
                console.log('The user has blocked the notification permission request.')
            else
                console.error('Failed to subscribe the user: ', error);
        });
}

function unsubscribeUser() {
    return swRegistration.pushManager.getSubscription()
        .then(subscription => subscription ? removeSubscriptionOnServer(subscription) : () => subscription)
        .then(subscription => subscription ? subscription.unsubscribe() : () => subscription)
        .then(() => {
            console.log('User is unsubscribed.');
            isSubscribed = false;
        })
        .catch(error => console.error('Failed to unsubscribe the user: ', error))
}

function initialiseUi() {
    pushButton.addEventListener('click', () => {
        pushButton.disabled = true;

        const promise = isSubscribed ? unsubscribeUser() : subscribeUser();
        promise.then(() => updateBtn());
    });

    // Set the initial subscription value.
    swRegistration.pushManager.getSubscription()
        .then(subscription => {
            isSubscribed = !(subscription === null);

            if (isSubscribed)
                console.log('User is subscribed.');
            else
                console.log('User is not subscribed.');

            updateBtn();
        });
}

if ('serviceWorker' in navigator && 'PushManager' in window) {
    console.log('Service Worker and Push is supported.');

    navigator.serviceWorker.register(serviceWorkerPath)
        .then(swReg => {
            console.log('Service Worker is registered.', swReg);

            swRegistration = swReg;
            initialiseUi();
        })
        .catch(error => console.error('Failed to register Service Worker: ', error));
} else {
    console.warn('Push messaging is not supported.');
    pushButton.textContent = 'Notifications are unsupported by your browser';
}