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
    <Head title="Register | ADONIS" />

    <div class="min-h-screen bg-[#F9FAFB] flex flex-col selection:bg-black selection:text-white">
        
        <header class="py-12 text-center">
            <Link href="/" class="text-xs font-bold uppercase tracking-[0.8em] text-black">ADONIS PH</Link>
        </header>

        <div class="flex-1 flex flex-col justify-center items-center px-6 pb-24">
            <div class="w-full max-w-md bg-white border border-gray-100 p-10 md:p-12 shadow-[0_1px_2px_rgba(0,0,0,0.01)]">
                
                <div class="mb-12">
                    <h1 class="text-3xl font-extralight tracking-tighter italic text-black uppercase">
                        Join the <span class="text-gray-300">Studio</span>
                    </h1>
                    <p class="text-[8px] uppercase tracking-[0.5em] text-gray-400 mt-2 italic">Create your account to start shopping</p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="p-4 bg-gray-50 border-l border-black">
                        <p class="text-[8px] uppercase tracking-widest leading-loose text-gray-500">
                            Note: Your password must be at least <span class="font-bold text-black">8 characters</span> long for security.
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-400">Full Name</label>
                        <input v-model="form.name" type="text" required autofocus placeholder="YOUR NAME" class="w-full border-b border-gray-100 py-2 text-[11px] tracking-widest uppercase focus:border-black transition-colors outline-none bg-transparent placeholder:text-gray-200">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-400">Email Address</label>
                        <input v-model="form.email" type="email" required placeholder="EMAIL@EXAMPLE.COM" class="w-full border-b border-gray-100 py-2 text-[11px] tracking-widest uppercase focus:border-black transition-colors outline-none bg-transparent placeholder:text-gray-200">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-400">Password</label>
                            <input v-model="form.password" type="password" required class="w-full border-b py-2 text-[11px] tracking-widest focus:border-black transition-colors outline-none bg-transparent" :class="!isPasswordLongEnough ? 'border-red-200' : 'border-gray-100'">
                            <p v-if="!isPasswordLongEnough" class="text-[7px] text-red-400 uppercase tracking-widest mt-1 italic">Minimum 8 characters</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-400">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" required class="w-full border-b py-2 text-[11px] tracking-widest focus:border-black transition-colors outline-none bg-transparent" :class="!passwordsMatch ? 'border-red-200' : 'border-gray-100'">
                            <p v-if="!passwordsMatch" class="text-[7px] text-red-400 uppercase tracking-widest mt-1 italic">Passwords don't match</p>
                        </div>
                    </div>

                    <div class="pt-6 flex flex-col gap-6">
                        <button type="submit" :disabled="form.processing || !passwordsMatch || !isPasswordLongEnough" class="w-full bg-black text-white py-4 text-[9px] font-bold uppercase tracking-[0.5em] hover:bg-zinc-900 transition-all duration-500 disabled:opacity-20">
                            Create Account
                        </button>

                        <div class="flex justify-center pt-4 border-t border-gray-50">
                            <p class="text-[8px] uppercase tracking-widest text-gray-300 mr-2">Already a member?</p>
                            <Link :href="route('login')" class="text-[8px] uppercase tracking-[0.3em] text-black font-bold border-b border-black pb-1">Sign In instead</Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="py-10 text-center">
            <p class="text-[7px] uppercase tracking-[1em] text-gray-200 pointer-events-none italic">© 2026 Studio Collective</p>
        </footer>

        <StudioNotification />
    </div>
</template>