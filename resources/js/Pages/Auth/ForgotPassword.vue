<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StudioNotification from '@/Components/StudioNotification.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Forgot Password | ADONIS STUDIO" />

    <div class="min-h-screen bg-zinc-50 flex flex-col justify-center items-center antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white font-black px-4 md:px-6 py-12">
        
        <!-- Centered Logo Above Recovery Box -->
        <div class="mb-10">
            <Link :href="route('welcome')" class="flex items-center group">
                <div class="bg-zinc-900 text-white h-9 px-3 flex items-center justify-center transition-all duration-500 text-xl font-black">
                    A<span class="ml-2 uppercase text-[9px] tracking-[0.4em] hidden md:block">STUDIO</span>
                </div>
                <div class="flex flex-col ml-3 leading-none text-left">
                    <span class="text-xl font-black tracking-[-0.05em] uppercase text-zinc-900">ADONIS</span>
                    <span class="text-[6px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 italic font-bold">Philippines</span>
                </div>
            </Link>
        </div>

        <!-- Distraction-Free Recovery Form -->
        <main class="w-full max-w-md bg-white border border-zinc-200 rounded-2xl p-8 md:p-12 shadow-xl relative overflow-hidden">
            
            <div class="mb-8 text-center">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-black italic">Account Recovery</span>
                <h1 class="text-3xl font-black tracking-tighter text-zinc-900 uppercase mt-2 font-['Plus_Jakarta_Sans'] italic">
                    Reset <span class="text-zinc-300">Password</span>
                </h1>
            </div>

            <div class="mb-8 p-4 bg-zinc-50 border-l-2 border-zinc-900 rounded-r-xl">
                <p class="text-[7px] uppercase tracking-[0.3em] leading-relaxed text-zinc-500 font-black">
                    Forgot your password? Enter your email address and we will send you a secure link to choose a new one.
                </p>
            </div>

            <div v-if="status" class="mb-6 p-4 bg-[#10B981]/10 border border-[#10B981]/20 rounded-xl text-[8px] font-black text-[#10B981] uppercase tracking-widest text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6 text-left">
                
                <div class="space-y-1.5">
                    <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Email Address</label>
                    <input 
                        v-model="form.email" 
                        type="email" 
                        required 
                        autofocus 
                        placeholder="ENTER EMAIL" 
                        class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all placeholder:text-zinc-300"
                        :class="{ 'border-red-500': form.errors.email }"
                    >
                    <div v-if="form.errors.email" class="text-[7px] text-red-500 font-black uppercase tracking-widest mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="pt-4 space-y-6">
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="group flex items-center justify-between w-full h-14 bg-zinc-900 text-white px-8 rounded-xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl active:scale-95 disabled:opacity-50"
                    >
                        <span>{{ form.processing ? 'Sending...' : 'Email Reset Link' }}</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                    <div class="flex justify-center items-center pt-6 border-t border-zinc-100">
                        <p class="text-[8px] uppercase tracking-[0.3em] text-zinc-400 mr-3">Remembered it?</p>
                        <Link :href="route('login')" class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-900 hover:text-[#10B981] transition-colors">Return to Login</Link>
                    </div>
                </div>
            </form>
        </main>

        <StudioNotification />
    </div>
</template>

<style scoped>
input { -webkit-tap-highlight-color: transparent; }
</style>