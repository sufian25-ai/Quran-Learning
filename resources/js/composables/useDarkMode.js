import { ref, watch, onMounted } from 'vue';

// Global state
const isDark = ref(false);

export function useDarkMode() {
    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        updateDOM();
        savePreference();
    };

    const setDarkMode = (value) => {
        isDark.value = value;
        updateDOM();
        savePreference();
    };

    const updateDOM = () => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    const savePreference = () => {
        localStorage.setItem('darkMode', isDark.value ? 'dark' : 'light');
    };

    const initDarkMode = () => {
        // Check localStorage first
        const saved = localStorage.getItem('darkMode');

        if (saved) {
            isDark.value = saved === 'dark';
        } else {
            // Fall back to system preference
            isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        updateDOM();

        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('darkMode')) {
                isDark.value = e.matches;
                updateDOM();
            }
        });
    };

    onMounted(() => {
        initDarkMode();
    });

    return {
        isDark,
        toggleDarkMode,
        setDarkMode,
        initDarkMode,
    };
}
