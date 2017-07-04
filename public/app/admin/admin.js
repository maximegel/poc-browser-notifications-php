'use strict';

function triggerPushMessage() {
    return fetch('/api/push-messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: document.querySelector('.app-push-message-text-field textarea').value
        })
        .catch(error => console.error(error));
}

function showSubscribers() {
    document.querySelector('.app-subscriber-table tbody').innerHTML = '';
    return fetch('/api/push-subscriptions', {
            method: 'GET'
        })
        .then(response => {
            response.json()
                .then(data => {
                    data
                        .map(dto => {
                            return {
                                databaseId: dto.id,
                                endpoint: dto.endpoint
                            };
                        })
                        .map(model => {
                            var element = document.createElement('tr')
                            element.innerHTML = `
                                <td class="mdl-data-table__cell--non-numeric">${model.databaseId}</td>
                                <td class="mdl-data-table__cell--non-numeric">${model.endpoint}</td>`;
                            return element;
                        })
                        .forEach(element => document.querySelector('.app-subscriber-table tbody').appendChild(element));
                });
        })
        .catch(error => console.error(error));
}

document.querySelector('.app-trigger-push-message-btn').addEventListener('click', () => triggerPushMessage());
document.querySelector('.app-refresh-subscriber-list-btn').addEventListener('click', () => showSubscribers());

showSubscribers();