<template>
    <!-- Facebook Pixel Code -->
</template>

<script setup>
import { onMounted } from 'vue';
import { fbPixel } from '@/utils/fbPixel.js';

const pixelId = import.meta.env.VITE_FACEBOOK_PIXEL_ID;

onMounted(() => {
    if (!pixelId || pixelId === 'YOUR_FACEBOOK_PIXEL_ID') {
        console.warn('⚠️ Facebook Pixel ID not configured. Set VITE_FACEBOOK_PIXEL_ID in .env');
        return;
    }

    // Facebook Pixel Base Code
    (function(f,b,e,v,n,t,s) {
        if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)
    })(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    
    window.fbq('init', pixelId);
    window.fbq('track', 'PageView');
    
    fbPixel.setInitialized(true);
    
    console.log('✅ Facebook Pixel initialized:', pixelId);
});

// Expose tracking helper globally
defineExpose({
    track: fbPixel.track.bind(fbPixel),
    trackCustom: fbPixel.trackCustom.bind(fbPixel),
    trackCourseView: fbPixel.trackCourseView.bind(fbPixel),
    trackEnrollmentStart: fbPixel.trackEnrollmentStart.bind(fbPixel),
    trackEnrollmentComplete: fbPixel.trackEnrollmentComplete.bind(fbPixel),
    trackRegistration: fbPixel.trackRegistration.bind(fbPixel),
    trackLead: fbPixel.trackLead.bind(fbPixel)
});
</script>

<style scoped>
/* No styles needed for tracking script */
</style>
