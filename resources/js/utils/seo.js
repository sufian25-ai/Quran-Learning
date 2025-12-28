/**
 * SEO Helper Utilities
 * Provides default SEO configurations for different page types
 */

export const seoConfig = {
    // Homepage
    home: {
        title: 'Learn Quran Online with Expert Teachers',
        description: 'Join thousands of students learning Quran with qualified teachers. Start with a free trial class today. Tajweed, Hifz, Nazra courses available.',
        keywords: 'online quran learning, quran teachers, islamic education, tajweed course, hifz quran, quran academy'
    },

    // Courses Page
    courses: {
        title: 'Quran Courses - Tajweed, Hifz, Nazra & More',
        description: 'Explore our comprehensive Quran courses designed for all levels. Learn from certified teachers with flexible schedules and affordable pricing.',
        keywords: 'quran courses, tajweed, hifz program, nazra course, quran reading, islamic courses'
    },

    // Teachers Page
    teachers: {
        title: 'Our Expert Quran Teachers',
        description: 'Meet our qualified and experienced Quran teachers. All teachers are certified with Ijazah and have years of teaching experience.',
        keywords: 'quran teachers, qualified ustadh, ijazah holders, certified quran instructors'
    },

    // Pricing Page
    pricing: {
        title: 'Affordable Quran Course Pricing',
        description: 'Transparent and affordable pricing for Quran courses. Choose a plan that fits your budget. Free trial class available.',
        keywords: 'quran course prices, affordable islamic education, online quran fees'
    },

    // About Page
    about: {
        title: 'About QuranLearn - Our Mission & Vision',
        description: 'Learn about our mission to make Quran education accessible worldwide. Discover our story, values, and commitment to quality Islamic education.',
        keywords: 'about quranlearn, islamic academy, quran education mission'
    }
};

/**
 * Generate SEO meta for a course
 */
export function getCourseSeO(course) {
    return {
        title: course.title,
        description: course.description || `Learn ${course.title} with expert teachers. ${course.duration} weeks course with flexible schedules.`,
        keywords: `${course.title}, quran course, ${course.category || 'islamic education'}`,
        image: course.image || '/images/courses/default.jpg',
        ogType: 'article'
    };
}

/**
 * Generate SEO meta for a teacher
 */
export function getTeacherSEO(teacher) {
    return {
        title: `${teacher.name} - Quran Teacher`,
        description: teacher.bio || `Learn Quran with ${teacher.name}. Qualified teacher with ${teacher.experience || 'years of'} experience in teaching Quran and Islamic studies.`,
        keywords: `${teacher.name}, quran teacher, ustadh, islamic instructor`,
        image: teacher.photo || '/images/teachers/default.jpg',
        ogType: 'profile'
    };
}

/**
 * Get site URL
 */
export function getSiteUrl(path = '') {
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin;
    return `${baseUrl}${path}`;
}
