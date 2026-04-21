<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import StudioNotification from '@/Components/StudioNotification.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-[#FBFBFB] selection:bg-black selection:text-white font-black antialiased">
        
        <nav class="bg-white/80 backdrop-blur-xl border-b border-zinc-100 fixed w-full z-[100] hidden md:block">
            <div class="max-w-[1600px] mx-auto px-8 lg:px-12">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <Link :href="route('dashboard')" class="flex flex-col group">
                            <span class="text-xl font-black tracking-[-0.05em] text-black group-hover:tracking-widest transition-all duration-500 uppercase">ADONIS</span>
                            <span class="text-[6px] tracking-[0.5em] text-zinc-400 uppercase italic">Central Management Archive</span>
                        </Link>
                    </div>

                    <div class="flex items-center gap-12">
                        <Link :href="route('dashboard')" :class="route().current('dashboard') ? 'text-black border-b border-black' : 'text-zinc-400'" class="text-[9px] uppercase tracking-[0.3em] font-black hover:text-black transition-all pb-1">Summary</Link>
                        <Link :href="route('admin.products.index')" :class="route().current('admin.products.*') ? 'text-black border-b border-black' : 'text-zinc-400'" class="text-[9px] uppercase tracking-[0.3em] font-black hover:text-black transition-all pb-1">Inventory</Link>
                        <Link :href="route('admin.orders.index')" :class="route().current('admin.orders.*') ? 'text-black border-b border-black' : 'text-zinc-400'" class="text-[9px] uppercase tracking-[0.3em] font-black hover:text-black transition-all pb-1">Order Log</Link>
                        <Link :href="route('profile.edit')" :class="route().current('profile.edit') ? 'text-black border-b border-black' : 'text-zinc-400'" class="text-[9px] uppercase tracking-[0.3em] font-black hover:text-black transition-all pb-1">Identity</Link>
                        <Link :href="route('logout')" method="post" as="button" class="text-[9px] uppercase tracking-[0.3em] text-red-500 font-black italic hover:tracking-[0.5em] transition-all">System Exit</Link>
                    </div>
                </div>
            </div>
        </nav>

        <header class="md:hidden bg-white border-b border-zinc-100 fixed top-0 w-full z-[100] px-6 h-16 flex items-center justify-between">
            <span class="text-[10px] font-black tracking-[0.3em] uppercase">ADONIS ADMIN</span>
            <div class="flex items-center gap-2">
                <span class="text-[7px] text-zinc-400 uppercase font-black">Live</span>
                <div class="h-1.5 w-1.5 rounded-full bg-[#10B981] animate-pulse"></div>
            </div>
        </header>

        <nav class="md:hidden fixed bottom-0 w-full bg-white/95 backdrop-blur-md border-t border-zinc-100 z-[100] px-6 py-5">
            <div class="flex justify-between items-center">
                <Link :href="route('dashboard')" class="flex flex-col items-center gap-1.5" :class="route().current('dashboard') ? 'text-[#10B981]' : 'text-zinc-400'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    <span class="text-[7px] uppercase font-black tracking-widest">Dash</span>
                </Link>
                <Link :href="route('admin.products.index')" class="flex flex-col items-center gap-1.5" :class="route().current('admin.products.*') ? 'text-[#10B981]' : 'text-zinc-400'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 3v18h18"/><path d="M19 9l-5 5-4-4-3 3"/></svg>
                    <span class="text-[7px] uppercase font-black tracking-widest">Archive</span>
                </Link>
                <Link :href="route('admin.orders.index')" class="flex flex-col items-center gap-1.5" :class="route().current('admin.orders.*') ? 'text-[#10B981]' : 'text-zinc-400'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span class="text-[7px] uppercase font-black tracking-widest">Orders</span>
                </Link>
                <Link :href="route('profile.edit')" class="flex flex-col items-center gap-1.5" :class="route().current('profile.edit') ? 'text-[#10B981]' : 'text-zinc-400'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/></svg>
                    <span class="text-[7px] uppercase font-black tracking-widest">Profile</span>
                </Link>
            </div>
        </nav>

        <main class="pt-20 md:pt-28 pb-32">
            <slot />
        </main>

        <StudioNotification />
    </div>
</template>

<style scoped>
/* Smooth transition for active links */
.transition-all {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>