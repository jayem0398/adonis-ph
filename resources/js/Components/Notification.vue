<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');

// Watch for flash messages sent from the backend
watch(() => page.props.flash.success, (msg) => {
    if (msg) {
        message.value = msg;
        show.value = true;
        setTimeout(() => show.value = false, 4000);
    }
}, { immediate: true });
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-500 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed bottom-10 right-10 z-[300] bg-black text-white px-8 py-4 shadow-2xl flex items-center gap-4">
            <div class="w-1 h-1 rounded-full bg-white animate-pulse"></div>
            <span class="text-[10px] font-bold uppercase tracking-[0.3em]">{{ message }}</span>
        </div>
    </Transition>
</template>