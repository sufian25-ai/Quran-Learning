<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: 'Learn Quran online with expert teachers. Hifz, Tajweed, and Islamic studies for all ages.',
    },
    keywords: {
        type: String,
        default: 'quran, learn quran, online quran, hifz, tajweed, islamic studies',
    },
    image: {
        type: String,
        default: '/images/og-default.jpg', // Make sure this exists or use a logo
    },
    url: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'website',
    },
});

const siteName = 'QuranLearn';
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" :content="keywords" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="type" />
        <meta property="og:url" :content="url || $page.url" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" :content="image" />
        <meta property="og:site_name" :content="siteName" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:url" :content="url || $page.url" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="image" />
        
        <!-- Canonical -->
        <link rel="canonical" :href="url || $page.url" />
    </Head>
</template>
