<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({
    conversation: {
        type: Object,
        required: true
    },
    messages: {
        type: Array,
        default: () => []
    }
});

const allMessages = ref([...props.messages]);
const newMessage = ref('');
const isSending = ref(false);
const messagesContainer = ref(null);
const lastMessageId = ref(props.messages.length > 0 ? Math.max(...props.messages.map(m => m.id)) : 0);

let pollingInterval = null;

const sendMessage = () => {
    if (!newMessage.value.trim() || isSending.value) return;

    const messageText = newMessage.value;
    newMessage.value = '';
    isSending.value = true;

    router.post(`/admin/chat/${props.conversation.id}/reply`, {
        message: messageText,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isSending.value = false;
            // Removed loadNewMessages() - waiting for Pusher event
        },
        onError: () => {
            isSending.value = false;
            // Restore the message on error
            newMessage.value = messageText;
        }
    });
};

const closeConversation = () => {
    if (confirm('Are you sure you want to close this conversation?')) {
        router.post(`/admin/chat/${props.conversation.id}/close`);
    }
};

const loadNewMessages = async () => {
    try {
        const response = await axios.get(`/admin/chat/${props.conversation.id}/messages`, {
            params: { after: lastMessageId.value }
        });

        if (response.data.messages.length > 0) {
            const newMessages = response.data.messages.filter(
                m => !allMessages.value.find(existing => existing.id === m.id)
            );
            
            allMessages.value.push(...newMessages);
            
            const maxId = Math.max(...response.data.messages.map(m => m.id));
            if (maxId > lastMessageId.value) {
                lastMessageId.value = maxId;
            }

            scrollToBottom();
        }
    } catch (error) {
        console.error('Failed to load messages:', error);
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
};

onMounted(() => {
    scrollToBottom();
    
    // Subscribe to channel
    window.Echo.channel(`chat.${props.conversation.session_id}`)
        .listen('MessageSent', (e) => {
            if (!allMessages.value.find(m => m.id === e.message.id)) {
                allMessages.value.push(e.message);
                lastMessageId.value = e.message.id;
                scrollToBottom();
            }
        });
});

onUnmounted(() => {
    window.Echo.leave(`chat.${props.conversation.session_id}`);
});
</script>

<template>
    <Head :title="`Chat with ${conversation.guest_name} | Admin`" />

    <AdminLayout>
        <div class="h-[calc(100vh-180px)] flex flex-col bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link href="/admin/chat" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </Link>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ conversation.guest_name?.charAt(0) || 'G' }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-semibold text-gray-900">{{ conversation.guest_name }}</h2>
                                    <span v-if="conversation.is_guest_online" class="flex items-center gap-1 text-xs text-green-600">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        Online
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">{{ conversation.guest_email }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <span :class="[
                            'px-3 py-1 rounded-full text-sm font-medium',
                            conversation.status === 'active' ? 'bg-green-100 text-green-700' :
                            conversation.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            'bg-gray-100 text-gray-600'
                        ]">
                            {{ conversation.status }}
                        </span>
                        
                        <button
                            v-if="conversation.status !== 'closed'"
                            @click="closeConversation"
                            class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium"
                        >
                            Close Chat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div 
                ref="messagesContainer"
                class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-4"
            >
                <div
                    v-for="message in allMessages"
                    :key="message.id"
                    :class="[
                        'max-w-[70%] p-4 rounded-2xl',
                        message.sender_type === 'guest' || message.sender_type === 'user'
                            ? 'mr-auto bg-white border border-gray-200 rounded-bl-md shadow-sm'
                            : message.sender_type === 'system'
                                ? 'mx-auto bg-gray-100 text-gray-600 text-center text-sm italic max-w-[50%]'
                                : 'ml-auto bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-br-md shadow-md'
                    ]"
                >
                    <p class="whitespace-pre-wrap">{{ message.message }}</p>
                    <div :class="[
                        'flex items-center gap-2 mt-2 text-xs',
                        message.sender_type === 'admin' || message.sender_type === 'teacher'
                            ? 'text-emerald-100 justify-end'
                            : 'text-gray-400'
                    ]">
                        <span v-if="message.sender_type !== 'system'">{{ message.sender_name }}</span>
                        <span>{{ formatTime(message.created_at) }}</span>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div v-if="conversation.status !== 'closed'" class="bg-white border-t border-gray-200 p-4 flex-shrink-0">
                <form @submit.prevent="sendMessage" class="flex gap-3">
                    <input
                        v-model="newMessage"
                        type="text"
                        placeholder="Type your reply..."
                        class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        :disabled="isSending"
                    />
                    <button
                        type="submit"
                        :disabled="!newMessage.trim() || isSending"
                        class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50 flex items-center gap-2"
                    >
                        <span>Send</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Closed Conversation Notice -->
            <div v-else class="bg-gray-100 border-t border-gray-200 p-4 text-center text-gray-500 flex-shrink-0">
                This conversation has been closed.
            </div>
        </div>
    </AdminLayout>
</template>
