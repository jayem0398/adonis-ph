<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudioNotification from '@/Components/StudioNotification.vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Authentication | ADONIS" />

    <div class="min-h-screen bg-white flex flex-col selection:bg-[#10B981] selection:text-white font-['Inter'] font-black text-zinc-900 overflow-x-hidden">
        
        <header class="py-12 text-center">
            <Link href="/" class="text-[10px] font-black uppercase tracking-[0.8em] text-zinc-900">ADONIS<span class="text-zinc-400">PH</span></Link>
        </header>

        <div class="flex-1 flex flex-col justify-center items-center px-6 pb-24">
            <div class="w-full max-w-md bg-white border border-zinc-100 rounded-2xl p-10 md:p-12 shadow-xl">
                
                <div class="mb-10 text-center">
                    <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-black">System Access</span>
                    <h1 class="text-3xl font-black tracking-tighter text-zinc-900 uppercase mt-2 font-['Plus_Jakarta_Sans']">
                        Auth <span class="text-zinc-300">Protocol</span>
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-8 text-left">
                    <div class="space-y-6">
                        <div class="space-y-1.5">
                            <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Identity / Email</label>
                            <input 
                                v-model="form.email"
                                type="email" 
                                required
                                autofocus
                                placeholder="ENTER CREDENTIAL"
                                class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-black outline-none transition-all placeholder:text-zinc-300"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center">
                                <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Passcode</label>
                                <Link v-if="canResetPassword" :href="route('password.request')" class="text-[7px] uppercase tracking-[0.2em] text-zinc-400 hover:text-black transition-colors">Recovery</Link>
                            </div>
                            <input 
                                v-model="form.password"
                                type="password" 
                                required
                                placeholder="••••••••"
                                class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-black outline-none transition-all placeholder:text-zinc-300 tracking-widest"
                            >
                        </div>
                    </div>

                    <div class="pt-2 space-y-6">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="group flex items-center justify-between w-full h-16 bg-zinc-900 text-white px-8 rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-2xl active:scale-95 disabled:opacity-50"
                        >
                            <span>{{ form.processing ? 'Verifying...' : 'Initialize' }}</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>

                        <div class="flex justify-center items-center pt-6 border-t border-zinc-50">
                            <p class="text-[8px] uppercase tracking-[0.3em] text-zinc-400 mr-3">Unregistered?</p>
                            <Link :href="route('register')" class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-900 hover:text-[#10B981] transition-colors">Create Identity</Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="py-10 text-center">
            <p class="text-[7px] font-black uppercase tracking-[1em] text-zinc-300 pointer-events-none italic">© 2026 Adonis Studio</p>
        </footer>
        
        <StudioNotification />
    </div>
</template>