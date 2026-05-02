<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const passwordsMatch = computed(() => {
    if (!form.password_confirmation) return true;
    return form.password === form.password_confirmation;
});

const isPasswordLongEnough = computed(() => {
    if (!form.password) return true;
    return form.password.length >= 8;
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Register | ADONIS" />

    <div class="min-h-screen bg-white flex flex-col selection:bg-[#10B981] selection:text-white font-['Inter'] font-black text-zinc-900 overflow-x-hidden">
        
        <header class="py-12 text-center">
            <Link href="/" class="text-[10px] font-black uppercase tracking-[0.8em] text-zinc-900">ADONIS<span class="text-zinc-400">PH</span></Link>
        </header>

        <div class="flex-1 flex flex-col justify-center items-center px-6 pb-24">
            <div class="w-full max-w-md bg-white border border-zinc-100 rounded-2xl p-10 md:p-12 shadow-xl">
                
                <div class="mb-10 text-center">
                    <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-black">New Registration</span>
                    <h1 class="text-3xl font-black tracking-tighter text-zinc-900 uppercase mt-2 font-['Plus_Jakarta_Sans']">
                        Join <span class="text-zinc-300">Studio</span>
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6 text-left">
                    
                    <div class="p-4 bg-zinc-50 border-l-2 border-zinc-900 rounded-r-xl">
                        <p class="text-[7px] uppercase tracking-[0.3em] leading-relaxed text-zinc-500 font-black">
                            Security Requirement: Passcode must exceed <span class="text-zinc-900">8 characters</span>.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Full Designation</label>
                        <input v-model="form.name" type="text" required autofocus placeholder="YOUR NAME" 
                            class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-black outline-none transition-all placeholder:text-zinc-300">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Identity / Email</label>
                        <input v-model="form.email" type="email" required placeholder="EMAIL@EXAMPLE.COM" 
                            class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-black outline-none transition-all placeholder:text-zinc-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Passcode</label>
                            <input v-model="form.password" type="password" required placeholder="••••••••"
                                class="w-full bg-zinc-50 border rounded-xl text-[10px] font-black p-4 focus:bg-white outline-none transition-all placeholder:text-zinc-300 tracking-widest"
                                :class="!isPasswordLongEnough ? 'border-red-300 focus:border-red-500' : 'border-zinc-100 focus:border-black'">
                            <p v-if="!isPasswordLongEnough" class="text-[7px] text-red-500 uppercase tracking-widest mt-1 italic font-black">Min 8 Chars</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Verify Passcode</label>
                            <input v-model="form.password_confirmation" type="password" required placeholder="••••••••"
                                class="w-full bg-zinc-50 border rounded-xl text-[10px] font-black p-4 focus:bg-white outline-none transition-all placeholder:text-zinc-300 tracking-widest"
                                :class="!passwordsMatch ? 'border-red-300 focus:border-red-500' : 'border-zinc-100 focus:border-black'">
                            <p v-if="!passwordsMatch" class="text-[7px] text-red-500 uppercase tracking-widest mt-1 italic font-black">Mismatch</p>
                        </div>
                    </div>

                    <div class="pt-4 space-y-6">
                        <button type="submit" :disabled="form.processing || !passwordsMatch || !isPasswordLongEnough" 
                            class="group flex items-center justify-between w-full h-16 bg-zinc-900 text-white px-8 rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-2xl active:scale-95 disabled:opacity-50">
                            <span>Establish</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>

                        <div class="flex justify-center items-center pt-6 border-t border-zinc-50">
                            <p class="text-[8px] uppercase tracking-[0.3em] text-zinc-400 mr-3">Already Active?</p>
                            <Link :href="route('login')" class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-900 hover:text-[#10B981] transition-colors">Authenticate</Link>
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