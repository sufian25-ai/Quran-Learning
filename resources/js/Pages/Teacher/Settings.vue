<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const activeTab = ref('profile');
const avatarInput = ref(null);
const avatarPreview = ref(null);
const uploadingAvatar = ref(false);

const tabs = [
    { id: 'profile', label: 'Profile', icon: '👤' },
    { id: 'teaching', label: 'Teaching Profile', icon: '👨‍🏫' },
    { id: 'account', label: 'Security', icon: '🔐' },
    { id: 'notifications', label: 'Notifications', icon: '🔔' },
    { id: 'availability', label: 'Availability', icon: '📅' },
];

const profileForm = useForm({
    name: user.value.name,
    email: user.value.email,
    phone: user.value.phone || '',
    timezone: user.value.timezone || 'Asia/Dhaka',
    language: user.value.language || 'en',
});

const teachingForm = useForm({
    bio: user.value.teacherProfile?.bio || '',
    specializations: user.value.teacherProfile?.specializations || [],
    languages: user.value.teacherProfile?.languages || ['English', 'Bengali'],
    experience_years: user.value.teacherProfile?.experience_years || 0,
    hourly_rate: user.value.teacherProfile?.hourly_rate || 500,
    is_accepting_students: user.value.teacherProfile?.is_accepting_students ?? true,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const notificationForm = useForm({
    email_new_student: true,
    email_class_reminder: true,
    email_review_received: true,
    sms_class_reminder: false,
});

const availabilityForm = useForm({
    schedule: {
        saturday: { available: true, start: '09:00', end: '17:00' },
        sunday: { available: true, start: '09:00', end: '17:00' },
        monday: { available: true, start: '09:00', end: '17:00' },
        tuesday: { available: true, start: '09:00', end: '17:00' },
        wednesday: { available: true, start: '09:00', end: '17:00' },
        thursday: { available: false, start: '09:00', end: '17:00' },
        friday: { available: false, start: '09:00', end: '17:00' },
    }
});

const specializationOptions = [
    'Quran Recitation', 'Tajweed', 'Hifz (Memorization)', 
    'Arabic Language', 'Islamic Studies', 'Tafsir',
    'Hadith Studies', 'Fiqh', 'Seerah'
];

const triggerAvatarInput = () => {
    avatarInput.value?.click();
};

const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        alert('Please upload a JPG, PNG or GIF image.');
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Image size must be less than 2MB.');
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

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
            alert('Failed to upload avatar.');
        },
    });
};

const updateProfile = () => {
    profileForm.patch('/profile', { preserveScroll: true });
};

const updateTeachingProfile = () => {
    teachingForm.post('/teacher/profile', { preserveScroll: true });
};

const updatePassword = () => {
    passwordForm.put('/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const timezones = [
    'Asia/Dhaka', 'UTC', 'America/New_York', 'America/Los_Angeles',
    'Europe/London', 'Asia/Dubai', 'Asia/Kolkata', 'Asia/Singapore'
];

const languages = [
    { code: 'en', name: 'English' },
    { code: 'ar', name: 'العربية (Arabic)' },
    { code: 'bn', name: 'বাংলা (Bengali)' },
    { code: 'ur', name: 'اردو (Urdu)' },
];

const days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
</script>

<template>
    <Head title="Teacher Settings" />

    <TeacherLayout>
        <template #header>
            <div>
                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white">
                    Teacher Settings 👨‍🏫
                </h2>
                <p class="text-gray-500 dark:text-slate-400 text-sm">Manage your teaching profile and preferences</p>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Sidebar Tabs -->
                    <div class="lg:w-64 flex-shrink-0">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-4 sticky top-24">
                            <nav class="space-y-1">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'w-full flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all',
                                        activeTab === tab.id
                                            ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 shadow-sm'
                                            : 'text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700'
                                    ]"
                                >
                                    <span class="text-lg mr-3">{{ tab.icon }}</span>
                                    {{ tab.label }}
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 space-y-6">
                        <!-- Profile Tab -->
                        <div v-if="activeTab === 'profile'" class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                                    <span class="text-xl">👤</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Update your basic profile details</p>
                                </div>
                            </div>
                            
                            <form @submit.prevent="updateProfile" class="space-y-6">
                                <!-- Avatar -->
                                <div class="flex items-center gap-6 pb-6 border-b border-gray-100 dark:border-slate-700">
                                    <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="handleAvatarChange" />
                                    <div @click="triggerAvatarInput" class="relative w-24 h-24 rounded-2xl cursor-pointer group overflow-hidden shadow-lg">
                                        <img v-if="avatarPreview || user.avatar" :src="avatarPreview || user.avatar" class="w-full h-full object-cover" alt="Avatar" />
                                        <div v-else class="w-full h-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-4xl text-white font-bold">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-2xl">📷</span>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" @click="triggerAvatarInput" :disabled="uploadingAvatar" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-xl transition-colors disabled:opacity-50">
                                            {{ uploadingAvatar ? 'Uploading...' : 'Change Photo' }}
                                        </button>
                                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">JPG, PNG or GIF. Max 2MB</p>
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Full Name</label>
                                        <input v-model="profileForm.name" type="text" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Email Address</label>
                                        <input v-model="profileForm.email" type="email" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Phone Number</label>
                                        <input v-model="profileForm.phone" type="tel" placeholder="+880 1XXX-XXXXXX" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Timezone</label>
                                        <select v-model="profileForm.timezone" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                            <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" :disabled="profileForm.processing" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors disabled:opacity-50">
                                        {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Teaching Profile Tab -->
                        <div v-if="activeTab === 'teaching'" class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                                    <span class="text-xl">👨‍🏫</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Teaching Profile</h3>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Showcase your expertise to students</p>
                                </div>
                            </div>

                            <form @submit.prevent="updateTeachingProfile" class="space-y-6">
                                <!-- Bio -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">About Me / Bio</label>
                                    <textarea v-model="teachingForm.bio" rows="4" placeholder="Tell students about your teaching experience, qualifications, and approach..." class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                                    <p class="text-xs text-gray-400 mt-1">This will be shown on your public profile</p>
                                </div>

                                <!-- Specializations -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-3">Specializations</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="spec in specializationOptions"
                                            :key="spec"
                                            type="button"
                                            @click="teachingForm.specializations.includes(spec) ? teachingForm.specializations = teachingForm.specializations.filter(s => s !== spec) : teachingForm.specializations.push(spec)"
                                            :class="[
                                                'px-4 py-2 rounded-full text-sm font-medium transition-all',
                                                teachingForm.specializations.includes(spec)
                                                    ? 'bg-emerald-500 text-white'
                                                    : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600'
                                            ]"
                                        >
                                            {{ spec }}
                                        </button>
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Years of Experience</label>
                                        <input v-model="teachingForm.experience_years" type="number" min="0" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Hourly Rate (BDT)</label>
                                        <input v-model="teachingForm.hourly_rate" type="number" min="0" step="50" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                </div>

                                <!-- Accepting Students Toggle -->
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700 rounded-xl">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Accepting New Students</p>
                                        <p class="text-sm text-gray-500 dark:text-slate-400">Allow students to enroll in your batches</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" v-model="teachingForm.is_accepting_students" class="sr-only peer" />
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" :disabled="teachingForm.processing" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors disabled:opacity-50">
                                        {{ teachingForm.processing ? 'Saving...' : 'Update Teaching Profile' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div v-if="activeTab === 'account'" class="space-y-6">
                            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                                        <span class="text-xl">🔐</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h3>
                                        <p class="text-sm text-gray-500 dark:text-slate-400">Secure your account with a strong password</p>
                                    </div>
                                </div>
                                
                                <form @submit.prevent="updatePassword" class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Current Password</label>
                                        <input v-model="passwordForm.current_password" type="password" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">New Password</label>
                                            <input v-model="passwordForm.password" type="password" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Confirm Password</label>
                                            <input v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500" />
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="passwordForm.processing" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors disabled:opacity-50">
                                            Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Notifications Tab -->
                        <div v-if="activeTab === 'notifications'" class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                                    <span class="text-xl">🔔</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notification Preferences</h3>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Choose what notifications you receive</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">New Student Enrollments</p>
                                        <p class="text-sm text-gray-500 dark:text-slate-400">When a student joins your batch</p>
                                    </div>
                                    <input v-model="notificationForm.email_new_student" type="checkbox" class="w-5 h-5 rounded text-emerald-500 focus:ring-emerald-500" />
                                </label>
                                <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Class Reminders</p>
                                        <p class="text-sm text-gray-500 dark:text-slate-400">15 minutes before your classes</p>
                                    </div>
                                    <input v-model="notificationForm.email_class_reminder" type="checkbox" class="w-5 h-5 rounded text-emerald-500 focus:ring-emerald-500" />
                                </label>
                                <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">New Reviews</p>
                                        <p class="text-sm text-gray-500 dark:text-slate-400">When students leave reviews</p>
                                    </div>
                                    <input v-model="notificationForm.email_review_received" type="checkbox" class="w-5 h-5 rounded text-emerald-500 focus:ring-emerald-500" />
                                </label>
                            </div>
                        </div>

                        <!-- Availability Tab -->
                        <div v-if="activeTab === 'availability'" class="bg-white dark:bg-slate-800 rounded-2xl shadow-soft p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/50 flex items-center justify-center">
                                    <span class="text-xl">📅</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Weekly Availability</h3>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Set your teaching hours for each day</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div v-for="day in days" :key="day" class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-700 rounded-xl">
                                    <label class="flex items-center gap-3 min-w-[140px]">
                                        <input v-model="availabilityForm.schedule[day].available" type="checkbox" class="w-5 h-5 rounded text-emerald-500 focus:ring-emerald-500" />
                                        <span class="font-medium text-gray-900 dark:text-white capitalize">{{ day }}</span>
                                    </label>
                                    <template v-if="availabilityForm.schedule[day].available">
                                        <div class="flex items-center gap-2">
                                            <input v-model="availabilityForm.schedule[day].start" type="time" class="rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-600 dark:text-white text-sm" />
                                            <span class="text-gray-500">to</span>
                                            <input v-model="availabilityForm.schedule[day].end" type="time" class="rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-600 dark:text-white text-sm" />
                                        </div>
                                    </template>
                                    <span v-else class="text-gray-400 dark:text-slate-500 text-sm">Not available</span>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="button" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors">
                                    Save Availability
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
