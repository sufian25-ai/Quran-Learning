<template>
    <!-- Google Analytics 4 Code -->
</template>

<script setup>
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { ga4 } from '@/utils/analytics.js';

const measurementId = import.meta.env.VITE_GOOGLE_ANALYTICS_ID;

onMounted(() => {
    if (!measurementId || measurementId === 'G-XXXXXXXXXX') {
        console.warn('⚠️ Google Analytics ID not configured. Set VITE_GOOGLE_ANALYTICS_ID in .env');
        return;
    }

    // Google Analytics 4 (gtag.js)
    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`;
    document.head.appendChild(script);

    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.gtag = gtag;
    gtag('js', new Date());
    gtag('config', measurementId);

    ga4.setInitialized(true);
    
    console.log('✅ Google Analytics initialized:', measurementId);

    // Track page views on Inertia navigation
    router.on('navigate', (event) => {
        if (window.gtag) {
            gtag('event', 'page_view', {
                page_path: window.location.pathname,
                page_title: document.title
            });
        }
    });
});

// Expose tracking helper globally
defineExpose({
    event: ga4.event.bind(ga4),
    trackCourseView: ga4.trackCourseView.bind(ga4),
    trackEnrollmentStart: ga4.trackEnrollmentStart.bind(ga4),
    trackEnrollmentComplete: ga4.trackEnrollmentComplete.bind(ga4),
    trackRegistration: ga4.trackRegistration.bind(ga4),
    trackLogin: ga4.trackLogin.bind(ga4)
});
</script>

<style scoped>
/* No styles needed for tracking script */
</style>
