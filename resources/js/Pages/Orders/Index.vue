<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({ orders: { type: Array, default: () => [] }, auth: Object });
const page = usePage();
const isScrolled = ref(false);
const cartCount = computed(() => page.props.cartCount || 0);
const isUserAdmin = computed(() => props.auth?.user && (props.auth.user.role === 'admin' || props.auth.user.is_admin));

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0 }).format(val);

const resolveImage = (order) => {
    const firstItem = order.items?.[0];
    const rawPath = firstItem?.product?.image_path || firstItem?.image_path || order.image_path;
    if (!rawPath) return 'https://placehold.co/200x300/18181b/ffffff?text=ADONIS+VOID';
    return rawPath.startsWith('http') ? rawPath : `/storage/${rawPath.replace(/^public\//, '')}`;
};

// SELYADO: Multi-Color Status Protocol
const getStatusTheme = (status) => {
    const s = status ? status.toLowerCase() : '';
    if (s.includes('pending')) return 'text-amber-500 border-amber-200 bg-amber-50';
    if (s.includes('process')) return 'text-blue-500 border-blue-200 bg-blue-50';
    if (s.includes('ship')) return 'text-indigo-500 border-indigo-200 bg-indigo-50';
    if (s.includes('deliver') || s.includes('complete')) return 'text-[#10B981] border-[#10B981]/20 bg-[#10B981]/5';
    if (s.includes('cancel') || s.includes('fail')) return 'text-red-500 border-red-200 bg-red-50';
    return 'text-zinc-500 border-zinc-200 bg-zinc-50';
};
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Archive History // Adonis Studio" />
    
    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 border-b border-zinc-100 shadow-sm' : 'bg-white py-4 border-b border-zinc-50']" 
             class="fixed top-0 w-full z-[110] transition-all duration-500 px-6 md:px-12 flex items-center justify-between font-medium">
            
             <div class="flex items-center gap-10">
                <Link :href="route('welcome')" class="relative flex items-center group/nav">
                    <div class="bg-zinc-900 text-white h-7 px-2 flex items-center justify-center transition-all duration-500 group-hover/nav:md:px-4 text-sm font-black italic">
                        A<span class="max-w-0 overflow-hidden whitespace-nowrap text-[8px] tracking-[0.4em] transition-all duration-500 group-hover/nav:max-w-[100px] group-hover/nav:ml-2 uppercase italic hidden md:block">STUDIO</span>
                    </div>
                    <div class="flex flex-col ml-3 leading-none text-left">
                        <span class="text-sm font-bold tracking-[-0.05em] uppercase font-['Plus_Jakarta_Sans']">ADONIS</span>
                        <span class="text-[5px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 font-bold italic">Philippines</span>
                    </div>
                </Link>
                <div class="hidden md:flex items-center gap-8 border-l border-zinc-100 pl-10">
                    <Link :href="route('archive.index')" class="text-[9px] uppercase tracking-[0.3em] text-zinc-400 hover:text-black transition-colors font-medium">Shop</Link>
                    <Link :href="route('orders.index')" class="text-[9px] uppercase tracking-[0.3em] text-black border-b border-black pb-0.5 font-bold">History</Link>
                </div>
            </div>

            <div class="flex items-center gap-10">
                <Link :href="route('cart.index')" class="hidden md:block relative text-zinc-400 hover:text-[#10B981] transition-colors">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span v-if="cartCount > 0" class="absolute -top-1.5 -right-2 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-bold">{{ cartCount }}</span>
                </Link>
                <Link :href="auth.user ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="text-zinc-400 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left">
            <div class="flex flex-col gap-0.5 mb-8 border-b border-zinc-900 pb-4">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic">Central Record Log</span>
                <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                    Archive <span class="text-zinc-200">History</span>
                </h1>
            </div>

            <div v-if="orders.length > 0" class="space-y-4">
                <div v-for="order in orders" :key="order.id" 
                     class="flex flex-col md:flex-row gap-4 p-5 bg-zinc-50 border border-zinc-100 rounded-2xl group hover:bg-white hover:border-zinc-200 hover:shadow-xl hover:shadow-zinc-100 transition-all duration-500">
                    
                    <div class="flex items-center gap-5 flex-grow">
                        <div class="w-16 h-20 bg-white border border-zinc-100 rounded-xl overflow-hidden shrink-0 shadow-sm">
                            <img :src="resolveImage(order)" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000">
                        </div>
                        <div class="space-y-1.5">
                            <span class="text-[8px] font-bold uppercase text-[#10B981] tracking-[0.2em] block italic">Ref #{{ String(order.id).padStart(5, '0') }}</span>
                            <h3 class="text-xs font-black uppercase tracking-tight font-['Plus_Jakarta_Sans'] leading-none">Manifest Document</h3>
                            <p class="text-[7px] text-zinc-400 uppercase tracking-widest font-bold">Logged: {{ order.created_at }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between md:justify-end gap-10 pt-5 md:pt-0 border-t md:border-t-0 border-zinc-100">
                        <div class="space-y-2 text-left md:text-right">
                            <span class="text-[7px] font-bold uppercase text-zinc-300 tracking-[0.4em] block italic">Status</span>
                            <div :class="getStatusTheme(order.status)" class="inline-flex items-center gap-1.5 border px-3 py-1 rounded-lg italic transition-colors">
                                <span class="w-1 h-1 rounded-full bg-current animate-pulse"></span>
                                <p class="text-[8px] font-black uppercase tracking-[0.2em]">{{ order.status.replace('_', ' ') }}</p>
                            </div>
                        </div>
                        <div class="space-y-1 text-right">
                            <span class="text-[7px] font-bold uppercase text-zinc-300 tracking-[0.4em] block italic">Valuation</span>
                            <p class="text-base font-black italic tracking-tighter font-['Plus_Jakarta_Sans']">{{ formatCurrency(order.total_amount) }}</p>
                        </div>
                        <Link :href="route('orders.show', order.id)" class="bg-zinc-900 text-white w-10 h-10 flex items-center justify-center rounded-xl hover:bg-[#10B981] transition-all shadow-lg active:scale-95">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="rotate-[-45deg]"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </Link>
                    </div>
                </div>
            </div>
            
            <div v-else class="py-32 text-center space-y-8 bg-zinc-50 border-2 border-dashed border-zinc-100 rounded-[2.5rem]">
                <div class="inline-block p-10 bg-white rounded-2xl shadow-sm opacity-50 grayscale">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="space-y-2">
                    <p class="text-[9px] text-zinc-400 uppercase tracking-[0.5em] font-bold italic">No records detected in log.</p>
                </div>
                <Link :href="route('archive.index')" class="inline-block bg-zinc-900 text-white px-10 py-4 rounded-xl text-[9px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all italic shadow-xl">Start Acquisition</Link>
            </div>
        </main>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-3 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.08)]">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300'"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300'">Home</span>
            </Link>
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300'"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300'">Shop</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300'"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span v-if="cartCount > 0" class="absolute -top-1.5 right-0 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-bold">{{ cartCount }}</span>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300'">Bag</span>
            </Link>
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300'"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300'">Orders</span>
            </Link>
        </nav>

        <StudioNotification />
    </div>
</template>