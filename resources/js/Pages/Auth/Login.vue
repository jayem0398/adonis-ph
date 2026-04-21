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
    <Head title="Login | ADONIS" />

    <div class="min-h-screen bg-[#F9FAFB] flex flex-col selection:bg-black selection:text-white">
        
        <header class="py-12 text-center">
            <Link href="/" class="text-xs font-bold uppercase tracking-[0.8em] text-black">ADONIS PH</Link>
        </header>

        <div class="flex-1 flex flex-col justify-center items-center px-6 pb-24">
            <div class="w-full max-w-md bg-white border border-gray-100 p-10 md:p-12 shadow-[0_1px_2px_rgba(0,0,0,0.01)]">
                
                <div class="mb-12">
                    <h1 class="text-3xl font-extralight tracking-tighter italic text-black uppercase">
                        Welcome <span class="text-gray-300">Back</span>
                    </h1>
                    <p class="text-[8px] uppercase tracking-[0.5em] text-gray-400 mt-2 italic">Please sign in to your account</p>
                </div>

                <form @submit.prevent="submit" class="space-y-10">
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <label class="block text-[8px] font-bold uppercase tracking-[0.3em] text-gray-400">Email Address</label>
                            <input 
                                v-model="form.email"
                                type="email" 
                                required
                                autofocus
                                placeholder="name@example.com"
                                class="w-full border-b border-gray-100 py-2 text-[11px] tracking-widest uppercase focus:border-black transition-all duration-500 outline-none bg-transparent placeholder:text-gray-200 placeholder:normal-case"
                            >
                        </div>

                        <div class="space-y-1">
                            <div class="flex justify-between items-center">
                                <label class="block text-[8px] font-bold uppercase tracking-[0.3em] text-gray-400">Password</label>
                                <Link v-if="canResetPassword" :href="route('password.request')" class="text-[7px] uppercase tracking-[0.1em] text-gray-300 hover:text-black">Forgot Password?</Link>
                            </div>
                            <input 
                                v-model="form.password"
                                type="password" 
                                required
                                placeholder="••••••••"
                                class="w-full border-b border-gray-100 py-2 text-[11px] tracking-widest focus:border-black transition-all duration-500 outline-none bg-transparent placeholder:text-gray-200"
                            >
                        </div>
                    </div>

                    <div class="pt-4 space-y-8">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full bg-black text-white py-4 text-[9px] font-bold uppercase tracking-[0.5em] hover:bg-zinc-900 transition-all duration-700 disabled:opacity-30"
                        >
                            Sign In
                        </button>

                        <div class="flex justify-center items-center pt-8 border-t border-gray-50">
                            <p class="text-[8px] uppercase tracking-widest text-gray-300 mr-2">New here?</p>
                            <Link :href="route('register')" class="text-[8px] font-bold uppercase tracking-[0.2em] text-black border-b border-black pb-0.5">Create an Account</Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="py-10 text-center">
            <p class="text-[7px] uppercase tracking-[1em] text-gray-200 pointer-events-none italic">© 2026 Studio Collective</p>
        </footer>
    </div>
</template>