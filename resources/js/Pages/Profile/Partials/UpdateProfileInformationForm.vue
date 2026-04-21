<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section class="space-y-8">
        <header>
            <h2 class="text-[10px] font-bold uppercase tracking-[0.3em] text-black">Identity Details</h2>
            <p class="text-[8px] text-gray-400 uppercase tracking-widest mt-1">Update your name and archival email.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
            <div class="space-y-1">
                <label class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-300">Full Name</label>
                <input v-model="form.name" type="text" required class="w-full border-b border-gray-100 py-2 text-[11px] tracking-widest uppercase focus:border-black transition-colors outline-none bg-transparent">
            </div>

            <div class="space-y-1">
                <label class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-300">Email Address</label>
                <input v-model="form.email" type="email" required class="w-full border-b border-gray-100 py-2 text-[11px] tracking-widest uppercase focus:border-black transition-colors outline-none bg-transparent">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" :disabled="form.processing" class="bg-black text-white px-8 py-3 text-[9px] font-bold uppercase tracking-[0.4em] hover:bg-zinc-800 transition-all">
                    Save Changes
                </button>
                <p v-if="form.recentlySuccessful" class="text-[8px] uppercase tracking-widest text-gray-400">Updated.</p>
            </div>
        </form>
    </section>
</template>