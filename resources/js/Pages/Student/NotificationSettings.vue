<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
    auth: Object,
    settings: {
        type: Object,
        default: () => ({})
    }
});

const form = useForm({
    class_reminders: props.settings.class_reminders ?? true,
    recitation_feedback: props.settings.recitation_feedback ?? true,
    weekly_progress: props.settings.weekly_progress ?? true,
    promotional: props.settings.promotional ?? false,
    reminder_time: props.settings.reminder_time ?? 30,
});

const saving = ref(false);
const saved = ref(false);

const saveSettings = () => {
    saving.value = true;
    form.post('/settings/notifications', {
        onSuccess: () => {
            saved.value = true;
            setTimeout(() => saved.value = false, 3000);
        },
        onFinish: () => saving.value = false,
    });
};
</script>

<template>
    <Head title="Notification Settings" />

    <StudentLayout>
        <template #header>
            <div>
                <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white">🔔 Notification Settings</h2>
                <p class="text-gray-500 dark:text-slate-400 text-sm">Control which emails you receive</p>
            </div>
        </template>

        <div class="max-w-2xl">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
                <!-- Success Message -->
                <div v-if="saved" class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-xl text-emerald-700 dark:text-emerald-400">
                    ✅ Settings saved successfully!
                </div>

                <form @submit.prevent="saveSettings" class="space-y-6">
                    <!-- Class Reminders -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">⏰</span>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Class Reminders</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Get notified before your classes start</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="form.class_reminders" type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Reminder Time -->
                    <div v-if="form.class_reminders" class="ml-12 p-4 border-l-2 border-emerald-200 dark:border-emerald-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                            Remind me before:
                        </label>
                        <select 
                            v-model="form.reminder_time"
                            class="rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option :value="15">15 minutes</option>
                            <option :value="30">30 minutes</option>
                            <option :value="60">1 hour</option>
                            <option :value="120">2 hours</option>
                            <option :value="1440">1 day</option>
                        </select>
                    </div>

                    <!-- Recitation Feedback -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">🎤</span>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Recitation Feedback</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Get notified when your teacher reviews your recitation</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="form.recitation_feedback" type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Weekly Progress -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">📊</span>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Weekly Progress Report</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Receive a summary of your learning progress every week</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="form.weekly_progress" type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Promotional -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">🎁</span>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Promotional Emails</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Receive updates about new courses and special offers</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="form.promotional" type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Save Button -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="saving"
                            class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl transition-colors disabled:opacity-50"
                        >
                            {{ saving ? 'Saving...' : '💾 Save Settings' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Preview Card -->
            <div class="mt-6 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white">
                <h3 class="font-semibold text-lg mb-2">📧 Test Your Email Settings</h3>
                <p class="text-emerald-100 text-sm mb-4">
                    Want to see what the emails look like? We can send you a test email.
                </p>
                <button class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-medium transition-colors">
                    Send Test Email
                </button>
            </div>
        </div>
    </StudentLayout>
</template>
