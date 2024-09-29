// resources/js/app.js

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Pusher
window.Pusher = Pusher;

// Initialize Laravel Echo with your Pusher credentials
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '91f11fe6e051c76cbda6', // Your Pusher key
    cluster: 'mt1',            // Your Pusher cluster
    forceTLS: true,
});

// Listen for the event
window.Echo.channel('invitations')
    .listen('InvitationSent', (e) => {
        console.log('Invitation sent:', e.invitation);
        // Here, you can add your notification logic (e.g., displaying a notification)
    });
