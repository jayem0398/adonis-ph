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
    <Head title="Create Account | ADONIS STUDIO" />

    <div class="min-h-screen bg-zinc-50 flex flex-col justify-center items-center antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white font-black px-4 md:px-6 py-12">
        
        <!-- SELYADO: Centered Logo Above Register Box -->
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

        <!-- SELYADO: Distraction-Free Register Form with Normal E-commerce Words -->
        <main class="w-full max-w-md bg-white border border-zinc-200 rounded-2xl p-8 md:p-12 shadow-xl relative overflow-hidden">
            
            <div class="mb-10 text-center">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-black italic">New Customer</span>
                <h1 class="text-3xl font-black tracking-tighter text-zinc-900 uppercase mt-2 font-['Plus_Jakarta_Sans'] italic">
                    Create <span class="text-zinc-300">Account</span>
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6 text-left">
                
                <div class="p-4 bg-zinc-50 border-l-2 border-zinc-900 rounded-r-xl">
                    <p class="text-[7px] uppercase tracking-[0.3em] leading-relaxed text-zinc-500 font-black">
                        Security: Password must be at least <span class="text-zinc-900">8 characters</span>.
                    </p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Full Name</label>
                    <input v-model="form.name" type="text" required autofocus placeholder="ENTER FULL NAME" 
                        class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all placeholder:text-zinc-300">
                    <div v-if="form.errors.name" class="text-[7px] text-red-500 font-black uppercase tracking-widest mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Email Address</label>
                    <input v-model="form.email" type="email" required placeholder="ENTER EMAIL" 
                        class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all placeholder:text-zinc-300">
                    <div v-if="form.errors.email" class="text-[7px] text-red-500 font-black uppercase tracking-widest mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Password</label>
                        <input v-model="form.password" type="password" required placeholder="••••••••"
                            class="w-full bg-zinc-50 border rounded-xl text-[10px] font-black p-4 focus:bg-white outline-none transition-all placeholder:text-zinc-300 tracking-widest"
                            :class="!isPasswordLongEnough && form.password ? 'border-red-300 focus:border-red-500' : 'border-zinc-100 focus:border-zinc-900'">
                        <p v-if="!isPasswordLongEnough && form.password" class="text-[7px] text-red-500 uppercase tracking-widest mt-1 italic font-black">Min 8 Chars</p>
                        <div v-if="form.errors.password" class="text-[7px] text-red-500 font-black uppercase tracking-widest mt-1">{{ form.errors.password }}</div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400">Confirm Password</label>
                        <input v-model="form.password_confirmation" type="password" required placeholder="••••••••"
                            class="w-full bg-zinc-50 border rounded-xl text-[10px] font-black p-4 focus:bg-white outline-none transition-all placeholder:text-zinc-300 tracking-widest"
                            :class="!passwordsMatch && form.password_confirmation ? 'border-red-300 focus:border-red-500' : 'border-zinc-100 focus:border-zinc-900'">
                        <p v-if="!passwordsMatch && form.password_confirmation" class="text-[7px] text-red-500 uppercase tracking-widest mt-1 italic font-black">Mismatch</p>
                    </div>
                </div>

                <div class="pt-4 space-y-6">
                    <button type="submit" :disabled="form.processing || !passwordsMatch || !isPasswordLongEnough" 
                        class="group flex items-center justify-between w-full h-14 bg-zinc-900 text-white px-8 rounded-xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl active:scale-95 disabled:opacity-50">
                        <span>{{ form.processing ? 'Creating...' : 'Create Account' }}</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                    <div class="flex justify-center items-center pt-6 border-t border-zinc-100">
                        <p class="text-[8px] uppercase tracking-[0.3em] text-zinc-400 mr-3">Already have an account?</p>
                        <Link :href="route('login')" class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-900 hover:text-[#10B981] transition-colors">Sign In</Link>
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