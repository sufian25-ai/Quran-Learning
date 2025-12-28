import { fbPixel } from '@/utils/fbPixel.js';

/**
 * Google Analytics 4 Tracking Helper
 */

class GoogleAnalyticsTracker {
    constructor() {
        this.isInitialized = false;
    }

    /**
     * Track event
     */
    event(eventName, params = {}) {
        if (window.gtag && this.isInitialized) {
            window.gtag('event', eventName, params);
            console.log(`[GA4] ${eventName}`, params);
        }
    }

    /**
     * Track course view
     */
    trackCourseView(course) {
        this.event('view_item', {
            currency: 'BDT',
            value: course.price || 0,
            items: [{
                item_id: course.id,
                item_name: course.title,
                item_category: 'Course',
                price: course.price || 0,
                quantity: 1
            }]
        });
    }

    /**
     * Track enrollment start
     */
    trackEnrollmentStart(course) {
        this.event('begin_checkout', {
            currency: 'BDT',
            value: course.price || 0,
            items: [{
                item_id: course.id,
                item_name: course.title,
                item_category: 'Course',
                price: course.price || 0,
                quantity: 1
            }]
        });
    }

    /**
     * Track successful enrollment/purchase
     */
    trackEnrollmentComplete(course, transactionId = null) {
        this.event('purchase', {
            transaction_id: transactionId || `txn_${Date.now()}`,
            currency: 'BDT',
            value: course.price || 0,
            items: [{
                item_id: course.id,
                item_name: course.title,
                item_category: 'Course',
                price: course.price || 0,
                quantity: 1
            }]
        });
    }

    /**
     * Track user registration
     */
    trackRegistration(method = 'email') {
        this.event('sign_up', {
            method: method
        });
    }

    /**
     * Track login
     */
    trackLogin(method = 'email') {
        this.event('login', {
            method: method
        });
    }

    /**
     * Track search
     */
    trackSearch(searchQuery) {
        this.event('search', {
            search_term: searchQuery
        });
    }

    setInitialized(status) {
        this.isInitialized = status;
    }
}

export const ga4 = new GoogleAnalyticsTracker();

/**
 * Combined tracking function
 * Tracks to both Facebook Pixel and Google Analytics
 */
export const trackEvent = {
    courseView: (course) => {
        fbPixel.trackCourseView(course);
        ga4.trackCourseView(course);
    },

    enrollmentStart: (course) => {
        fbPixel.trackEnrollmentStart(course);
        ga4.trackEnrollmentStart(course);
    },

    enrollmentComplete: (course, transactionId) => {
        fbPixel.trackEnrollmentComplete(course, transactionId);
        ga4.trackEnrollmentComplete(course, transactionId);
    },

    registration: (method = 'email') => {
        fbPixel.trackRegistration(method);
        ga4.trackRegistration(method);
    },

    login: (method = 'email') => {
        ga4.trackLogin(method);
    },

    search: (query) => {
        fbPixel.trackSearch(query);
        ga4.trackSearch(query);
    }
};
