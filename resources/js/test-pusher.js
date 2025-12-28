// Pusher Debug Test
console.log('=== PUSHER DEBUG TEST ===');
console.log('Window.Echo exists:', !!window.Echo);
console.log('Window.Pusher exists:', !!window.Pusher);

if (window.Echo) {
    console.log('Echo config:', window.Echo.connector.pusher.config);
    console.log('Pusher connection state:', window.Echo.connector.pusher.connection.state);

    // Test subscription
    const testChannel = window.Echo.channel('test-channel');
    console.log('Test channel subscribed');

    testChannel.listen('.test-event', (data) => {
        console.log('Received test event:', data);
    });
}

// Log all Pusher events
if (window.Pusher) {
    Pusher.logToConsole = true;
}
