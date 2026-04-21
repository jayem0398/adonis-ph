<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

// Ito ang titingin kung naka-login ka o hindi
const auth = computed(() => page.props.auth);
</script>

<template>
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-50">
        <div class="max-w-[1440px] mx-auto px-12 h-20 flex justify-between items-center">
            
            <Link href="/" class="text-xs font-bold uppercase tracking-[0.8em] text-black">ADONIS</Link>

            <div class="flex items-center gap-8">
                <Link :href="route('cart.index')" class="flex items-center gap-2">
                    <span class="text-[9px] uppercase tracking-[0.2em] font-bold">Bag [{{ page.props.cartCount }}]</span>
                </Link>

                <template v-if="auth.user">
                    <Link :href="route('orders.index')" class="text-[9px] uppercase tracking-[0.2em] font-bold text-gray-400 hover:text-black transition-colors">Orders</Link>
                    <Link :href="route('profile.edit')" class="text-[9px] uppercase tracking-[0.2em] font-bold text-gray-400 hover:text-black transition-colors">Profile</Link>
                    
                    <Link v-if="auth.user.is_admin" :href="route('dashboard')" class="text-[9px] uppercase tracking-[0.2em] font-black text-black">Admin</Link>
                    
                    <Link :href="route('logout')" method="post" as="button" class="text-[9px] uppercase tracking-[0.2em] font-bold text-red-300">Exit</Link>
                </template>

                <template v-else>
                    <Link :href="route('login')" class="text-[9px] uppercase tracking-[0.2em] font-bold text-gray-400">Access</Link>
                </template>
            </div>
        </div>
    </nav>
</template>