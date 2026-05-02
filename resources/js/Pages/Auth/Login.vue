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
    <Head title="Login | ADONIS STUDIO" />

    <div class="min-h-screen bg-zinc-50 flex flex-col justify-center items-center antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white font-black px-4 md:px-6">
        
        <!-- SELYADO: Centered Logo Above Login Box -->
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

        <!-- SELYADO: Distraction-Free Login Form -->
        <main class="w-full max-w-md bg-white border border-zinc-200 rounded-2xl p-8 md:p-12 shadow-xl relative overflow-hidden">
            
            <div v-if="status" class="mb-6 text-[10px] font-black text-[#10B981] uppercase tracking-widest text-center">
                {{ status }}
            </div>

            <div class="mb-10 text-center">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-black italic">Welcome Back</span>
                <h1 class="text-3xl font-black tracking-tighter text-zinc-900 uppercase mt-2 font-['Plus_Jakarta_Sans'] italic">
                    Account <span class="text-zinc-300">Login</span>
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-8 text-left">
                <div class="space-y-6">
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

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Password</label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-[7px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-zinc-900 transition-colors">Forgot Password?</Link>
                        </div>
                        <input 
                            v-model="form.password"
                            type="password" 
                            required
                            placeholder="••••••••"
                            class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all placeholder:text-zinc-300 tracking-widest"
                            :class="{ 'border-red-500': form.errors.password }"
                        >
                        <div v-if="form.errors.password" class="text-[7px] text-red-500 font-black uppercase tracking-widest mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-black cursor-pointer accent-black transition-all" />
                            <span class="ml-2 text-[8px] font-black uppercase tracking-widest text-zinc-500">Remember Me</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2 space-y-6">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="group flex items-center justify-between w-full h-14 bg-zinc-900 text-white px-8 rounded-xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl active:scale-95 disabled:opacity-50"
                    >
                        <span>{{ form.processing ? 'Signing In...' : 'Sign In' }}</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                    <div class="flex justify-center items-center pt-6 border-t border-zinc-100">
                        <p class="text-[8px] uppercase tracking-[0.3em] text-zinc-400 mr-3">New Customer?</p>
                        <Link :href="route('register')" class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-900 hover:text-[#10B981] transition-colors">Create Account</Link>
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