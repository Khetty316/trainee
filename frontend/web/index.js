/*var NEW_PUBLIC_KEY = 'BLRPeMkINzGwpC-bbavvOhwCTtWi2MyZN09HKf_eCELhEUgG1Fe2Odw_GVl7fpkB0G7U1JVif7C2xdPu-qTGdb0';


if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js').then(function (registration) {
            // Registration was successful
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('ServiceWorker registration failed: ', err);
        });

        navigator.serviceWorker.register('/sw.js').then(function (registration) {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(NEW_PUBLIC_KEY)
            };

            registration.pushManager.getSubscription()
                    .then(function (subscription) {
                        if (subscription) {
                            let json = subscription.toJSON();
                            let public_key = json.keys.p256dh;

                            if (public_key !== NEW_PUBLIC_KEY) {
                                subscription.unsubscribe();
                            }
                        }
                    });

            return registration.pushManager.subscribe(subscribeOptions);
        }).then(function (pushSubscription) {
            console.log('*** Received PushSubscription: ', JSON.stringify(pushSubscription));
            return pushSubscription;
        });
        ;
    });
}

Notification.requestPermission(function (status) {
    console.log('Notification permission status:', status);
});

//displayNotification();
function displayNotification() {
    if (Notification.permission === 'granted') {
        navigator.serviceWorker.getRegistration().then(function (reg) {
            reg.showNotification('Hello world!');
        });
    }
}

function displayNotification1() {
    if (Notification.permission == 'granted') {
        navigator.serviceWorker.getRegistration().then(function (reg) {
            var options = {
                body: 'Here is a notification body!',
                icon: 'images/example.png',
                vibrate: [100, 50, 100],
                data: {
                    dateOfArrival: Date.now(),
                    primaryKey: 1
                }
            };
            reg.showNotification('Hello world!', options);
        });
    }
}

function displayNotification2() {
    if (Notification.permission == 'granted') {
        navigator.serviceWorker.getRegistration().then(function (reg) {
            var options = {
                body: 'Here is a notification body!',
                icon: 'images/example.png',
                vibrate: [100, 50, 100],
                data: {
                    dateOfArrival: Date.now(),
                    primaryKey: 1
                },
                actions: [
                    {action: 'explore', title: 'Explore this new world',
                        icon: 'images/checkmark.png'},
                    {action: 'close', title: 'Close notification',
                        icon: 'images/xmark.png'},
                ],
                silent: false
            };
            reg.showNotification('Hello world!', options);
        });
    }
}
*/







//function subscribe(registration) {
//    registration.pushManager.subscribe({
//        userVisibleOnly: true,
//        applicationServerKey: urlBase64ToUint8Array(NEW_PUBLIC_KEY)
//    }).then(pushSubscription => {
//        //successfully subscribed to push
//        //save it to your DB etc....
//    });
//}

/*
 Notification.requestPermission(function (result) {
 if (permissionResult == 'granted') {
 subscribeUser();
 }
 });
 
 function subscribeUser() {
 navigator.serviceWorker.ready
 .then(registration => {
 registration.pushManager.getSubscription()
 .then(pushSubscription => {
 if (!pushSubscription) {
 //the user was never subscribed
 subscribe(registration);
 } else {
 //check if user was subscribed with a different key
 let json = pushSubscription.toJSON();
 let public_key = json.keys.p256dh;
 
 console.log(public_key);
 
 if (public_key != NEW_PUBLIC_KEY) {
 pushSubscription.unsubscribe().then(successful => {
 // You've successfully unsubscribed
 subscribe(registration);
 }).catch(e => {
 // Unsubscription failed
 });
 }
 }
 });
 });
 }
 
 function subscribe(registration) {
 registration.pushManager.subscribe({
 userVisibleOnly: true,
 applicationServerKey: urlBase64ToUint8Array(NEW_PUBLIC_KEY)
 })
 .then(pushSubscription => {
 //successfully subscribed to push
 //save it to your DB etc....
 });
 }
 */
function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}


//
//function askPermission() {
//    return new Promise(function (resolve, reject) {
//        const permissionResult = Notification.requestPermission(function (result) {
//            resolve(result);
//        });
//
//        if (permissionResult) {
//            permissionResult.then(resolve, reject);
//        }
//    }).then(function (permissionResult) {
//        if (permissionResult !== 'granted') {
//            throw new Error('We weren\'t granted permission.');
//        }
//    });
//}
//
//function subscribeUserToPush() {
//    return navigator.serviceWorker.register('/sw.js')
//            .then(function (registration) {
//                const subscribeOptions = {
//                    userVisibleOnly: true,
//                    applicationServerKey: urlBase64ToUint8Array(
//                            'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U'
//                            )
//                };
//
//                return registration.pushManager.subscribe(subscribeOptions);
//            })
//            .then(function (pushSubscription) {
//                console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
//                return pushSubscription;
//            });
//}

/*
 subscribeUserToPush();
 function subscribeUserToPush() {
 return navigator.serviceWorker.register('/sw.js')
 .then(function (registration) {
 const subscribeOptions = {
 userVisibleOnly: true,
 applicationServerKey: urlBase64ToUint8Array(
 //                            'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U',
 'BLRPeMkINzGwpC-bbavvOhwCTtWi2MyZN09HKf_eCELhEUgG1Fe2Odw_GVl7fpkB0G7U1JVif7C2xdPu-qTGdb0'
 )
 };
 // nyQQwfNtSAKMrwOGHtGMZ5K_JvPXUr8aflXDIvVlgW4
 return registration.pushManager.subscribe(subscribeOptions);
 })
 .then(function (pushSubscription) {
 console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
 return pushSubscription;
 });
 }
 
 
 function urlBase64ToUint8Array(base64String) {
 var padding = '='.repeat((4 - base64String.length % 4) % 4);
 var base64 = (base64String + padding)
 .replace(/\-/g, '+')
 .replace(/_/g, '/');
 
 var rawData = window.atob(base64);
 var outputArray = new Uint8Array(rawData.length);
 
 for (var i = 0; i < rawData.length; ++i) {
 outputArray[i] = rawData.charCodeAt(i);
 }
 return outputArray;
 }
 */
//sendSubscriptionToBackEnd();
function sendSubscriptionToBackEnd(subscription) {
    return fetch('/api/save-subscription/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(subscription)
    })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Bad status code from server.');
                }

                return response.json();
            })
            .then(function (responseData) {
                if (!(responseData.data && responseData.data.success)) {
                    throw new Error('Bad response from server.');
                }
            });
}
//
//if ('serviceWorker' in navigator) {
//    navigator.serviceWorker.register("/sw.js").then(retistration => {
//        console.log("SW Registered!");
//        console.log(registration);
//
//    }).catch(error => {
//        console.log("SW Registration Failed!");
//        console.log(error)
//    });
//}