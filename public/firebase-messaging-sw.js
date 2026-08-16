/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
    apiKey: "AIzaSyD-1qTy-COPv7IT4u_X8uCwoSPV4mOUDJw",
    authDomain: "khola-bazaar.firebaseapp.com",
    projectId: "khola-bazaar",
    storageBucket: "khola-bazaar.appspot.com",
    messagingSenderId: "108629861013",
    appId: "1:108629861013:web:236f1d80012c4c260a319c",
    measurementId: "G-CV7FC7EPLZ"
});


/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload,
  );
  /* Customize notification here */
  const notificationTitle = "Background Message Title";
  const notificationOptions = {
    body: "Background Message body.",
    icon: "images/ssl.png",
  };

  // const notificationOptions = {
  //   body: payload.data.body,
  //   icon: payload.data.icon,
  //   image : payload.data.image,
  //   data:{
  //       time: new Date(Date.now()).toString(),
  //       click_action : payload.data.click_action
  //   }
  // };

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions,
  );
});
