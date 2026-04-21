<script setup>
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const isError = ref(false);

// Selyado: Watch for both success and error flashes
watch(() => page.props.flash, (flash) => {
    if (flash?.success || flash?.error) {
        message.value = (flash.success || flash.error).replace(/_/g, ' ');
        isError.value = !!flash.error;
        show.value = true;
        
        setTimeout(() => {
            show.value = false;
            // Clear flash data to prevent re-triggering
            if (flash.success) page.props.flash.success = null;
            if (flash.error) page.props.flash.error = null;
        }, 3500);
    }
}, { deep: true, immediate: true });
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-500 cubic-bezier(0.16, 1, 0.3, 1)"
            enter-from-class="opacity-0 translate-y-8 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="show" class="fixed bottom-24 md:bottom-10 left-0 right-0 md:left-auto md:right-10 z-[9999] flex justify-center px-6 pointer-events-none font-black">
                
                <div :class="[isError ? 'border-red-500/50' : 'border-emerald-500/50']" 
                     class="bg-zinc-950/95 backdrop-blur-md text-white flex items-center shadow-[0_20px_40px_rgba(0,0,0,0.4)] border pointer-events-auto overflow-hidden w-full max-w-[300px] md:w-auto md:min-w-[260px] rounded-xl relative">
                    
                    <div :class="[isError ? 'bg-red-500' : 'bg-emerald-500']" 
                         class="absolute bottom-0 left-0 h-[2px] animate-[timer_3.5s_linear_forwards]"></div>
                    
                    <div class="px-5 py-4 flex items-center gap-4 w-full">
                        <div class="flex items-center justify-center shrink-0">
                            <svg v-if="!isError" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="4"><path d="M20 6 9 17l-5-5"/></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="4"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </div>

                        <div class="flex flex-col text-left">
                            <span class="text-[7px] uppercase tracking-[0.4em] text-zinc-500 leading-none mb-1 font-bold italic">
                                {{ isError ? 'System_Error' : 'Protocol_Success' }}
                            </span>
                            <p class="text-[10px] md:text-[11px] font-black uppercase tracking-widest leading-none">
                                {{ message }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes timer {
    0% { width: 100%; }
    100% { width: 0%; }
}

/* Selyado: Mobile haptics shadow */
@media (max-width: 768px) {
    .bg-zinc-950\/95 {
        box-shadow: 0 -10px 40px -10px rgba(0,0,0,0.5);
    }
}
</style>