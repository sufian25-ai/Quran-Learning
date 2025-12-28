import { router } from '@inertiajs/vue3';

/**
 * Facebook Pixel Tracking Helper
 * Provides methods to track standard and custom events
 */

class FacebookPixelTracker {
    constructor() {
        this.isInitialized = false;
    }

    /**
     * Track standard Facebook Pixel event
     */
    track(eventName, params = {}) {
        if (window.fbq && this.isInitialized) {
            window.fbq('track', eventName, params);
            console.log(`[FB Pixel] ${eventName}`, params);
        }
    }

    /**
     * Track custom event
     */
    trackCustom(eventName, params = {}) {
        if (window.fbq && this.isInitialized) {
            window.fbq('trackCustom', eventName, params);
            console.log(`[FB Pixel Custom] ${eventName}`, params);
        }
    }

    /**
     * Track course view
     */
    trackCourseView(course) {
        this.track('ViewContent', {
            content_name: course.title,
            content_category: 'Course',
            content_ids: [course.id],
            content_type: 'product',
            value: course.price || 0,
            currency: 'BDT'
        });
    }

    /**
     * Track enrollment initiation
     */
    trackEnrollmentStart(course) {
        this.track('InitiateCheckout', {
            content_name: course.title,
            content_category: 'Course',
            content_ids: [course.id],
            value: course.price || 0,
            currency: 'BDT',
            num_items: 1
        });
    }

    /**
     * Track successful enrollment/purchase
     */
    trackEnrollmentComplete(course, transactionId = null) {
        this.track('Purchase', {
            content_name: course.title,
            content_ids: [course.id],
            content_type: 'product',
            value: course.price || 0,
            currency: 'BDT',
            transaction_id: transactionId
        });
    }

    /**
     * Track user registration
     */
    trackRegistration(method = 'email') {
        this.track('CompleteRegistration', {
            content_name: 'User Registration',
            status: 'completed',
            registration_method: method
        });
    }

    /**
     * Track lead (e.g., contact form submission)
     */
    trackLead(leadType = 'contact_form') {
        this.track('Lead', {
            content_name: leadType,
            content_category: 'Lead'
        });
    }

    /**
     * Track search
     */
    trackSearch(searchQuery) {
        this.track('Search', {
            search_string: searchQuery
        });
    }

    /**
     * Track custom educational events
     */
    trackLessonComplete(courseId, lessonId) {
        this.trackCustom('LessonComplete', {
            course_id: courseId,
            lesson_id: lessonId
        });
    }

    trackVideoWatch(videoId, duration) {
        this.trackCustom('VideoWatch', {
            video_id: videoId,
            watch_duration: duration
        });
    }

    setInitialized(status) {
        this.isInitialized = status;
    }
}

export const fbPixel = new FacebookPixelTracker();
