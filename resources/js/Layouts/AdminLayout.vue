<script setup>
import { ref, onMounted } from 'vue';
import { Link, Head, usePage } from '@inertiajs/vue3';
import StudioNotification from '@/Components/StudioNotification.vue';

const page = usePage();
const isScrolled = ref(false);
const user = page.props.auth.user;

const navLinks = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Inventory', route: 'admin.inventory', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Orders', route: 'admin.orders.index', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
    { name: 'Vouchers', route: 'admin.vouchers.index', icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' }
];

const isActive = (linkRoute) => route().current(linkRoute + '*');

onMounted(() => { 
    window.addEventListener('scroll', () => { 
        isScrolled.value = window.scrollY > 10; 
    }); 
});
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    
    <div class="min-h-screen bg-[#F9FAFB] text-[#111827] font-['Inter'] antialiased selection:bg-black selection:text-white flex flex-col">
        <Head title="ADONIS // ADMINISTRATIVE" />

        <nav :class="[isScrolled ? 'h-14 shadow-sm' : 'h-16', 'fixed top-0 w-full z-[100] bg-white border-b border-zinc-200 transition-all duration-300 px-4 md:px-10 flex items-center justify-between']">
            
            <div class="flex items-center gap-6 h-full text-left">
                <Link :href="route('admin.dashboard')" class="flex items-center gap-3 shrink-0 group">
                    <div class="w-8 h-8 bg-black rounded flex items-center justify-center shadow-lg shadow-black/10 group-active:scale-90 transition-transform">
                        <span class="text-white text-[13px] font-black tracking-tighter italic">A</span>
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="text-[13px] font-extrabold tracking-tight uppercase font-['Plus_Jakarta_Sans'] group-hover:text-zinc-600 transition-colors">ADONIS</span>
                        <span class="text-[8px] text-zinc-400 font-bold uppercase tracking-[0.2em] mt-0.5 italic">Control Node</span>
                    </div>
                </Link>

                <div class="hidden md:flex h-full items-center ml-4">
                    <Link v-for="link in navLinks" :key="link.name" :href="route(link.route)"
                        class="h-full px-5 flex items-center text-[10px] font-black uppercase tracking-widest transition-all relative"
                        :class="isActive(link.route) ? 'text-black' : 'text-zinc-400 hover:text-zinc-900'">
                        {{ link.name }}
                        <div v-if="isActive(link.route)" class="absolute bottom-0 left-0 w-full h-[2px] bg-black"></div>
                    </Link>
                </div>
            </div>

            <div class="flex items-center gap-4 md:gap-6">
                <div class="flex items-center gap-3 border-r border-zinc-200 pr-4 md:pr-6 h-8 text-right">
                    <div class="hidden sm:flex flex-col items-end leading-none">
                        <span class="text-[10px] text-zinc-900 font-black uppercase tracking-tight">{{ user.name }}</span>
                        <span class="text-[8px] text-emerald-600 font-black uppercase tracking-tighter mt-1 italic">Authorized Admin</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-[10px] font-black uppercase overflow-hidden">
                        {{ user.name.charAt(0) }}
                    </div>
                </div>
                
                <Link :href="route('logout')" method="post" as="button" 
                      class="hidden md:flex items-center gap-2 px-5 py-2.5 bg-zinc-900 text-white rounded-lg hover:bg-red-600 active:scale-95 transition-all group">
                    <span class="text-[10px] font-black uppercase tracking-widest">Sign Out</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                </Link>
            </div>
        </nav>

        <main class="pt-24 pb-24 md:pb-12 px-4 md:px-10 max-w-[1400px] mx-auto w-full flex-1">
            <div class="animate-in">
                <slot />
            </div>
        </main>

        <footer class="hidden md:flex bg-white border-t border-zinc-100 px-10 py-6 justify-between items-center text-zinc-400">
            <span class="text-[9px] font-black tracking-[0.3em] uppercase italic text-left">
                © 2026 ADONIS PHILIPPINES // REGISTRY
            </span>
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2 text-[9px] font-black uppercase text-zinc-500 italic">
                    <span class="w-1.5 h-1.5 bg-[#10B981] rounded-full animate-pulse shadow-[0_0_8px_#10B981]"></span>
                    Core Operational
                </span>
                <span class="text-[9px] font-mono border-l border-zinc-200 pl-6 uppercase text-zinc-300 tracking-tighter">
                    ADX-2.0.4-SNC
                </span>
            </div>
        </footer>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[100] h-16 flex justify-around items-center pb-safe shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
            <Link v-for="link in navLinks" :key="link.name" :href="route(link.route)"
                class="flex flex-col items-center justify-center gap-1 w-full h-full transition-all"
                :class="isActive(link.route) ? 'text-black' : 'text-zinc-400'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="link.icon" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">{{ link.name }}</span>
            </Link>
            <Link :href="route('logout')" method="post" as="button" class="flex flex-col items-center justify-center gap-1 w-full h-full text-zinc-400 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-[8px] font-black uppercase tracking-tighter">Exit</span>
            </Link>
        </nav>

        <StudioNotification />
    </div>
</template>

<style scoped>
.pb-safe { padding-bottom: env(safe-area-inset-bottom); }
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #111827; }
.animate-in { animation: fadeInSlide 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fadeInSlide { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>