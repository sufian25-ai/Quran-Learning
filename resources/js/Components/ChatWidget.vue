<template>
    <!-- Chat Button -->
    <button
        v-if="!isOpen && !isMinimized"
        @click="openChat"
        class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full shadow-2xl flex items-center justify-center text-white hover:scale-110 transition-all duration-300 z-50 group"
    >
        <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <!-- Notification Badge -->
        <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-bounce">
            {{ unreadCount }}
        </span>
    </button>

    <!-- Minimized Bar -->
    <button
        v-if="isMinimized"
        @click="isMinimized = false; isOpen = true"
        class="fixed bottom-6 right-6 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full shadow-2xl flex items-center gap-3 text-white hover:scale-105 transition-all z-50"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span class="font-medium">Continue Chat</span>
        <span v-if="unreadCount > 0" class="w-5 h-5 bg-red-500 text-xs font-bold rounded-full flex items-center justify-center">
            {{ unreadCount }}
        </span>
    </button>

    <!-- Chat Window -->
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-8 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-8 scale-95"
    >
        <div
            v-if="isOpen"
            class="fixed bottom-6 right-6 w-96 h-[32rem] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden z-50 border border-gray-100"
        >
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-xl">📖</span>
                        </div>
                        <div>
                            <h3 class="font-semibold">QuranLearn Support</h3>
                            <p class="text-xs text-emerald-100">We typically reply in minutes</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="minimizeChat" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                            </svg>
                        </button>
                        <button @click="closeChat" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Start Form (if no conversation) -->
            <div v-if="!conversationId" class="flex-1 overflow-y-auto p-5">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">👋</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">Welcome!</h4>
                    <p class="text-gray-500 text-sm mt-1">How can we help you today?</p>
                </div>

                <form @submit.prevent="startConversation" class="space-y-4">
                    <div>
                        <input
                            v-model="guestName"
                            type="text"
                            placeholder="Your Name"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        />
                    </div>
                    <div>
                        <input
                            v-model="guestEmail"
                            type="email"
                            placeholder="Your Email"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        />
                    </div>
                    <div>
                        <textarea
                            v-model="initialMessage"
                            placeholder="Type your message..."
                            required
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-70"
                    >
                        <span v-if="isLoading">Starting...</span>
                        <span v-else>Start Chat</span>
                    </button>
                </form>
            </div>

            <!-- Messages Area (if conversation exists) -->
            <div v-else class="flex-1 overflow-y-auto p-4 space-y-4" ref="messagesContainer">
                <div
                    v-for="message in messages"
                    :key="message.id"
                    :class="[
                        'max-w-[85%] p-3 rounded-2xl',
                        message.sender_type === 'guest' || message.sender_type === 'user'
                            ? 'ml-auto bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-br-md'
                            : message.sender_type === 'system'
                                ? 'mx-auto bg-gray-100 text-gray-600 text-center text-sm italic'
                                : 'mr-auto bg-gray-100 text-gray-800 rounded-bl-md'
                    ]"
                >
                    <p class="text-sm whitespace-pre-wrap">{{ message.message }}</p>
                    <p :class="[
                        'text-xs mt-1',
                        message.sender_type === 'guest' || message.sender_type === 'user'
                            ? 'text-emerald-100'
                            : 'text-gray-400'
                    ]">
                        {{ formatTime(message.created_at) }}
                    </p>
                </div>

                <!-- Typing indicator -->
                <div v-if="isTyping" class="flex items-center gap-1 text-gray-400 text-sm">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    </div>
                    <span class="ml-2">Support is typing...</span>
                </div>
            </div>

            <!-- Message Input (if conversation exists) -->
            <div v-if="conversationId" class="p-4 border-t border-gray-100">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        v-model="newMessage"
                        type="text"
                        placeholder="Type a message..."
                        class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        :disabled="isSending"
                    />
                    <button
                        type="submit"
                        :disabled="!newMessage.trim() || isSending"
                        class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue';
import axios from 'axios';

const isOpen = ref(false);
const isMinimized = ref(false);
const isLoading = ref(false);
const isSending = ref(false);
const isTyping = ref(false);

// Guest info
const guestName = ref('');
const guestEmail = ref('');
const initialMessage = ref('');

// Conversation
const conversationId = ref(null);
const sessionId = ref(null);
const messages = ref([]);
const newMessage = ref('');
const unreadCount = ref(0);
const lastMessageId = ref(0);

const messagesContainer = ref(null);
let pollingInterval = null;

// Load session from localStorage
onMounted(() => {
    const savedSession = localStorage.getItem('chat_session');
    if (savedSession) {
        const session = JSON.parse(savedSession);
        sessionId.value = session.sessionId;
        conversationId.value = session.conversationId;
        guestName.value = session.guestName || '';
        guestEmail.value = session.guestEmail || '';
        
        // Load existing messages
        if (conversationId.value) {
            loadMessages();
            subscribeToChannel();
        }
    } else {
        sessionId.value = generateSessionId();
    }
});

onUnmounted(() => {
    leaveChannel();
});

const generateSessionId = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};

const saveSession = () => {
    localStorage.setItem('chat_session', JSON.stringify({
        sessionId: sessionId.value,
        conversationId: conversationId.value,
        guestName: guestName.value,
        guestEmail: guestEmail.value,
    }));
};

const openChat = () => {
    isOpen.value = true;
    isMinimized.value = false;
    unreadCount.value = 0;
};

const closeChat = () => {
    isOpen.value = false;
    isMinimized.value = false;
};

const minimizeChat = () => {
    isOpen.value = false;
    isMinimized.value = true;
};

const startConversation = async () => {
    if (!guestName.value || !guestEmail.value || !initialMessage.value) return;

    isLoading.value = true;
    try {
        const response = await axios.post('/api/chat/start', {
            name: guestName.value,
            email: guestEmail.value,
            message: initialMessage.value,
            session_id: sessionId.value,
        });

        conversationId.value = response.data.conversation_id;
        sessionId.value = response.data.session_id;
        saveSession();
        
        // Load messages
        await loadMessages();
        subscribeToChannel();
        
        initialMessage.value = '';
    } catch (error) {
        console.error('Failed to start conversation:', error);
        alert('Failed to start chat. Please try again.');
    } finally {
        isLoading.value = false;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || isSending.value) return;

    const messageText = newMessage.value;
    newMessage.value = '';
    isSending.value = true;

    try {
        const response = await axios.post(`/api/chat/${conversationId.value}/messages`, {
            message: messageText,
            session_id: sessionId.value,
        });
        
        // Optimistically add the message to local state for immediate feedback
        // Pusher will also send it, but duplicate check will prevent showing it twice
        if (response.data.success && response.data.message) {
            if (!messages.value.find(m => m.id === response.data.message.id)) {
                messages.value.push(response.data.message);
                lastMessageId.value = response.data.message.id;
                scrollToBottom();
            }
        }
    } catch (error) {
        console.error('Failed to send message:', error);
        // Restore message on error
        newMessage.value = messageText;
    } finally {
        isSending.value = false;
    }
};

const loadMessages = async () => {
    if (!conversationId.value || !sessionId.value) return;

    try {
        const response = await axios.get(`/api/chat/${conversationId.value}/messages`, {
            params: {
                session_id: sessionId.value,
                after: lastMessageId.value,
            },
        });

        if (response.data.messages.length > 0) {
            // Add new messages
            const newMessages = response.data.messages.filter(
                m => !messages.value.find(existing => existing.id === m.id)
            );
            
            messages.value.push(...newMessages);
            
            // Update last message ID
            const maxId = Math.max(...response.data.messages.map(m => m.id));
            if (maxId > lastMessageId.value) {
                lastMessageId.value = maxId;
            }

            // Update unread count for messages from support
            const supportMessages = newMessages.filter(m => 
                ['admin', 'teacher', 'system'].includes(m.sender_type)
            );
            if (!isOpen.value && supportMessages.length > 0) {
                unreadCount.value += supportMessages.length;
            }

            scrollToBottom();
        }

        // Check if conversation was closed
        if (response.data.status === 'closed') {
            stopPolling();
        }
    } catch (error) {
        console.error('Failed to load messages:', error);
    }
};

const subscribeToChannel = () => {
    if (!sessionId.value) return;

    window.Echo.channel(`chat.${sessionId.value}`)
        .listen('MessageSent', (e) => {
            // Check if message already exists
            if (!messages.value.find(m => m.id === e.message.id)) {
                
                // If it's a closed status update (system message with specific text? or event data?)
                // The event contains the message. If the message says "closed", we could handle it, 
                // but ChatController sends a system message.
                
                messages.value.push(e.message);
                lastMessageId.value = e.message.id;
                
                // Update unread count if chat is closed and sender is support
                if (!isOpen.value && ['admin', 'teacher', 'system'].includes(e.message.sender_type)) {
                    unreadCount.value++;
                }
                
                scrollToBottom();
            }
        });
};

const leaveChannel = () => {
    if (sessionId.value) {
        window.Echo.leave(`chat.${sessionId.value}`);
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
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

// Watch for open state to clear unread
watch(isOpen, (open) => {
    if (open) {
        unreadCount.value = 0;
        scrollToBottom();
    }
});
</script>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
