if ('serviceWorker' in navigator && 'Notification' in window) {
    navigator.serviceWorker.register('/wp-content/plugins/wp-push-notifier/sw.js')
        .then(function(reg) {
            console.log('Service Worker registered.');

            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    // Fetch latest post info
                    fetch('/wp-content/plugins/wp-push-notifier/latest.json')
                        .then(res => res.json())
                        .then(data => {
                            reg.showNotification(data.title, {
                                body: data.body,
                                icon: data.icon,
                                data: { url: data.url }
                            });
                        });
                }
            });
        });
}
