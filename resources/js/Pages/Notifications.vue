<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    auth: Object,
    notifications: {
        type: Array,
        default: () => []
    },
    unreadCount: {
        type: Number,
        default: 0
    }
});

const selectedFilter = ref('all');

const filteredNotifications = computed(() => {
    if (selectedFilter.value === 'all') return props.notifications;
    if (selectedFilter.value === 'unread') return props.notifications.filter(n => !n.read_at);
    return props.notifications.filter(n => n.type === selectedFilter.value);
});

const markAsRead = (id) => {
    router.post(`/notifications/${id}/read`, {}, { preserveScroll: true });
};

const markAllRead = () => {
    router.post('/notifications/mark-all-read', {}, { preserveScroll: true });
};

const getNotificationIcon = (type) => {
    const icons = {
        'class_reminder': '🔔',
        'class_started': '▶️',
        'class_cancelled': '❌',
        'payment_received': '💳',
        'payment_due': '⚠️',
        'enrollment_confirmed': '✅',
        'badge_earned': '🏅',
        'streak_milestone': '🔥',
        'teacher_note': '📝',
        'announcement': '📢',
        'new_resource': '📁',
        'certificate_ready': '🎓',
    };
    return icons[type] || '🔔';
};

const getNotificationBg = (type, isRead) => {
    if (isRead) return 'bg-gray-50';
    const colors = {
        'class_reminder': 'bg-blue-50',
        'class_started': 'bg-green-50',
        'class_cancelled': 'bg-red-50',
        'payment_due': 'bg-yellow-50',
        'badge_earned': 'bg-purple-50',
    };
    return colors[type] || 'bg-primary-50';
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    return date.toLocaleDateString();
};
</script>

<template>
    <Head title="Notifications" />

    <StudentLayout>
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                        <p v-if="unreadCount" class="text-sm text-gray-500">{{ unreadCount }} unread</p>
                    </div>
                    <button
                        v-if="unreadCount"
                        @click="markAllRead"
                        class="text-sm text-primary-500 hover:text-primary-600 font-medium"
                    >
                        Mark all as read
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                    <button
                        v-for="filter in [
                            { value: 'all', label: 'All' },
                            { value: 'unread', label: 'Unread' },
                            { value: 'class_reminder', label: '🔔 Classes' },
                            { value: 'payment_received', label: '💳 Payments' },
                            { value: 'badge_earned', label: '🏅 Badges' },
                        ]"
                        :key="filter.value"
                        @click="selectedFilter = filter.value"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap',
                            selectedFilter === filter.value
                                ? 'bg-primary-500 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        {{ filter.label }}
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="space-y-3">
                    <div
                        v-for="notification in filteredNotifications"
                        :key="notification.id"
                        :class="[
                            'relative rounded-xl p-4 transition-all cursor-pointer hover:shadow-md',
                            getNotificationBg(notification.type, notification.read_at),
                            !notification.read_at && 'border-l-4 border-primary-500'
                        ]"
                        @click="markAsRead(notification.id)"
                    >
                        <div class="flex items-start space-x-4">
                            <span class="text-2xl flex-shrink-0">{{ getNotificationIcon(notification.type) }}</span>
                            <div class="flex-1 min-w-0">
                                <p :class="['font-medium', notification.read_at ? 'text-gray-700' : 'text-gray-900']">
                                    {{ notification.title }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">{{ notification.message }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-400">
                                    <span>{{ formatTime(notification.created_at) }}</span>
                                    <span v-if="notification.action_url" class="ml-4">
                                        <Link :href="notification.action_url" class="text-primary-500 hover:underline">
                                            {{ notification.action_text || 'View' }} →
                                        </Link>
                                    </span>
                                </div>
                            </div>
                            <span v-if="!notification.read_at" class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0 mt-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!filteredNotifications.length" class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <span class="text-5xl mb-4 block">🔔</span>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Notifications</h3>
                    <p class="text-gray-500">
                        {{ selectedFilter === 'unread' ? "You're all caught up!" : 'You have no notifications yet.' }}
                    </p>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
