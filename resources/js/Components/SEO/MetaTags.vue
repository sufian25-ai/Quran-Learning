<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" :content="keywords" />
        <link rel="canonical" :href="canonicalUrl" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="ogType" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" :content="image" />
        <meta property="og:site_name" content="QuranLearn" />
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:url" :content="canonicalUrl" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="image" />
        
        <!-- Additional Meta Tags -->
        <meta name="robots" content="index, follow" />
        <meta name="author" content="QuranLearn" />
        <meta name="language" content="English" />
    </Head>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    keywords: {
        type: String,
        default: 'Quran learning, Quran courses, Islamic education, Tajweed, Hifz, Quran recitation'
    },
    image: {
        type: String,
        default: '/images/og-default.jpg'
    },
    ogType: {
        type: String,
        default: 'website'
    },
    canonical: {
        type: String,
        default: null
    }
});

const fullTitle = computed(() => {
    return props.title ? `${props.title} | QuranLearn` : 'QuranLearn - Learn Quran Online with Expert Teachers';
});

const canonicalUrl = computed(() => {
    return props.canonical || window.location.href;
});
</script>
