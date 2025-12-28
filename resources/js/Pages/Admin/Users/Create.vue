<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    auth: Object,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'student',
});

const submit = () => {
    form.post('/admin/users', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create User | Admin" />

    <AdminLayout>
        <div class="max-w-2xl">
            <div class="mb-8">
                <Link href="/admin/users" class="text-gray-500 hover:text-gray-700">
                    ← Back to Users
                </Link>
                <h1 class="text-2xl font-bold text-gray-900 mt-4">Create New User</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Full name"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="email@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select
                            v-model="form.role"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                        <p v-if="form.errors.role" class="mt-1 text-sm text-red-500">{{ form.errors.role }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Min 8 characters"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Repeat password"
                        />
                    </div>

                    <div class="flex gap-4 pt-4">
                        <Link
                            href="/admin/users"
                            class="flex-1 py-3 text-center border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
