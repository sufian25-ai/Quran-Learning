<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const activeTab = ref('profile');
const avatarInput = ref(null);
const avatarPreview = ref(null);
const uploadingAvatar = ref(false);

const tabs = [
    { id: 'profile', label: 'Profile', icon: '👤' },
    { id: 'account', label: 'Account', icon: '🔐' },
    { id: 'notifications', label: 'Notifications', icon: '🔔' },
    { id: 'preferences', label: 'Preferences', icon: '⚙️' },
];

const profileForm = useForm({
    name: user.value.name,
    email: user.value.email,
    phone: user.value.phone || '',
    timezone: user.value.timezone || 'UTC',
    language: user.value.language || 'en',
    country_code: user.value.country_code || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const notificationForm = useForm({
    email_class_reminder: true,
    email_newsletter: true,
    email_promotions: false,
    sms_class_reminder: false,
});

// Avatar upload function
const triggerAvatarInput = () => {
    avatarInput.value?.click();
};

const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        alert('Please upload a JPG, PNG or GIF image.');
        return;
    }

    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image size must be less than 2MB.');
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

    // Upload the file
    uploadingAvatar.value = true;
    const formData = new FormData();
    formData.append('avatar', file);

    router.post('/profile/avatar', formData, {
        preserveScroll: true,
        onSuccess: () => {
            uploadingAvatar.value = false;
        },
        onError: () => {
            uploadingAvatar.value = false;
            avatarPreview.value = null;
            alert('Failed to upload avatar. Please try again.');
        },
    });
};

const updateProfile = () => {
    profileForm.patch('/profile', {
        preserveScroll: true,
    });
};

const updatePassword = () => {
    passwordForm.put('/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const updateNotifications = () => {
    notificationForm.post('/profile/notifications', {
        preserveScroll: true,
    });
};

const timezones = [
    'UTC', 'America/New_York', 'America/Los_Angeles', 'Europe/London',
    'Europe/Paris', 'Asia/Dubai', 'Asia/Dhaka', 'Asia/Kolkata',
    'Asia/Singapore', 'Australia/Sydney'
];

const languages = [
    { code: 'en', name: 'English' },
    { code: 'ar', name: 'العربية (Arabic)' },
    { code: 'bn', name: 'বাংলা (Bengali)' },
    { code: 'ur', name: 'اردو (Urdu)' },
];
</script>

<template>
    <Head title="Settings" />

    <StudentLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900">
                    Account Settings ⚙️
                </h2>
                <p class="text-gray-500 text-sm">Manage your profile and preferences</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Sidebar Tabs -->
                    <div class="md:w-64 flex-shrink-0">
                        <div class="bg-white rounded-2xl shadow-soft p-4">
                            <nav class="space-y-1">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'w-full flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-colors',
                                        activeTab === tab.id
                                            ? 'bg-primary-50 text-primary-700'
                                            : 'text-gray-600 hover:bg-gray-50'
                                    ]"
                                >
                                    <span class="text-lg mr-3">{{ tab.icon }}</span>
                                    {{ tab.label }}
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <!-- Profile Tab -->
                        <div v-if="activeTab === 'profile'" class="bg-white rounded-2xl shadow-soft p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h3>
                            <form @submit.prevent="updateProfile" class="space-y-6">
                                <!-- Avatar -->
                                <div class="flex items-center gap-6 pb-6 border-b border-gray-100">
                                    <!-- Hidden file input -->
                                    <input
                                        ref="avatarInput"
                                        type="file"
                                        accept="image/jpeg,image/png,image/gif"
                                        class="hidden"
                                        @change="handleAvatarChange"
                                    />
                                    
                                    <!-- Avatar display -->
                                    <div 
                                        @click="triggerAvatarInput"
                                        class="relative w-20 h-20 rounded-full cursor-pointer group overflow-hidden"
                                    >
                                        <!-- Show uploaded avatar or preview -->
                                        <img
                                            v-if="avatarPreview || user.avatar"
                                            :src="avatarPreview || user.avatar"
                                            class="w-full h-full object-cover"
                                            alt="Avatar"
                                        />
                                        <!-- Show initial letter if no avatar -->
                                        <div
                                            v-else
                                            class="w-full h-full bg-primary-100 flex items-center justify-center text-3xl text-primary-600"
                                        >
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        
                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span v-if="uploadingAvatar" class="text-white text-sm">Uploading...</span>
                                            <span v-else class="text-white text-2xl">📷</span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <button 
                                            type="button" 
                                            @click="triggerAvatarInput"
                                            :disabled="uploadingAvatar"
                                            class="text-primary-500 hover:text-primary-600 text-sm font-medium disabled:opacity-50"
                                        >
                                            {{ uploadingAvatar ? 'Uploading...' : 'Change Avatar' }}
                                        </button>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG or GIF. Max 2MB</p>
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input
                                            id="name"
                                            v-model="profileForm.name"
                                            type="text"
                                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                        />
                                        <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-500">{{ profileForm.errors.name }}</p>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input
                                            id="email"
                                            v-model="profileForm.email"
                                            type="email"
                                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                        />
                                        <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-500">{{ profileForm.errors.email }}</p>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <input
                                            id="phone"
                                            v-model="profileForm.phone"
                                            type="tel"
                                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                            placeholder="+1 (555) 123-4567"
                                        />
                                    </div>
                                    <div>
                                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                        <select
                                            id="timezone"
                                            v-model="profileForm.timezone"
                                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                        >
                                            <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                        <select
                                            id="language"
                                            v-model="profileForm.language"
                                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                        >
                                            <option v-for="lang in languages" :key="lang.code" :value="lang.code">{{ lang.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="profileForm.processing"
                                        class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl disabled:opacity-50"
                                    >
                                        {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Account Tab -->
                        <div v-if="activeTab === 'account'" class="space-y-6">
                            <!-- Change Password -->
                            <div class="bg-white rounded-2xl shadow-soft p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h3>
                                <form @submit.prevent="updatePassword" class="space-y-6">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                        <input
                                            id="current_password"
                                            v-model="passwordForm.current_password"
                                            type="password"
                                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                        />
                                        <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-500">{{ passwordForm.errors.current_password }}</p>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                            <input
                                                id="password"
                                                v-model="passwordForm.password"
                                                type="password"
                                                class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                            />
                                            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-500">{{ passwordForm.errors.password }}</p>
                                        </div>
                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                            <input
                                                id="password_confirmation"
                                                v-model="passwordForm.password_confirmation"
                                                type="password"
                                                class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            :disabled="passwordForm.processing"
                                            class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl disabled:opacity-50"
                                        >
                                            {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Danger Zone -->
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-red-100">
                                <h3 class="text-lg font-semibold text-red-600 mb-2">Danger Zone</h3>
                                <p class="text-sm text-gray-500 mb-4">Permanently delete your account and all data.</p>
                                <button class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg">
                                    Delete Account
                                </button>
                            </div>
                        </div>

                        <!-- Notifications Tab -->
                        <div v-if="activeTab === 'notifications'" class="bg-white rounded-2xl shadow-soft p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Notification Preferences</h3>
                            <form @submit.prevent="updateNotifications" class="space-y-6">
                                <div class="space-y-4">
                                    <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 cursor-pointer">
                                        <div>
                                            <p class="font-medium text-gray-900">Class Reminders</p>
                                            <p class="text-sm text-gray-500">Get notified before your classes</p>
                                        </div>
                                        <input v-model="notificationForm.email_class_reminder" type="checkbox" class="rounded text-primary-500 focus:ring-primary-500" />
                                    </label>
                                    <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 cursor-pointer">
                                        <div>
                                            <p class="font-medium text-gray-900">Newsletter</p>
                                            <p class="text-sm text-gray-500">Receive learning tips and updates</p>
                                        </div>
                                        <input v-model="notificationForm.email_newsletter" type="checkbox" class="rounded text-primary-500 focus:ring-primary-500" />
                                    </label>
                                    <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 cursor-pointer">
                                        <div>
                                            <p class="font-medium text-gray-900">Promotions</p>
                                            <p class="text-sm text-gray-500">Special offers and discounts</p>
                                        </div>
                                        <input v-model="notificationForm.email_promotions" type="checkbox" class="rounded text-primary-500 focus:ring-primary-500" />
                                    </label>
                                    <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 cursor-pointer">
                                        <div>
                                            <p class="font-medium text-gray-900">SMS Reminders</p>
                                            <p class="text-sm text-gray-500">Get SMS alerts for classes</p>
                                        </div>
                                        <input v-model="notificationForm.sms_class_reminder" type="checkbox" class="rounded text-primary-500 focus:ring-primary-500" />
                                    </label>
                                </div>
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="notificationForm.processing"
                                        class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl disabled:opacity-50"
                                    >
                                        Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Preferences Tab -->
                        <div v-if="activeTab === 'preferences'" class="bg-white rounded-2xl shadow-soft p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Display Preferences</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Theme</label>
                                    <div class="flex gap-4">
                                        <button class="flex-1 p-4 border-2 border-primary-500 rounded-xl text-center">
                                            <span class="text-2xl mb-2 block">☀️</span>
                                            <span class="text-sm font-medium">Light</span>
                                        </button>
                                        <button class="flex-1 p-4 border-2 border-gray-200 rounded-xl text-center hover:border-gray-300">
                                            <span class="text-2xl mb-2 block">🌙</span>
                                            <span class="text-sm font-medium">Dark</span>
                                        </button>
                                        <button class="flex-1 p-4 border-2 border-gray-200 rounded-xl text-center hover:border-gray-300">
                                            <span class="text-2xl mb-2 block">💻</span>
                                            <span class="text-sm font-medium">System</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
