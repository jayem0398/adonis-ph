<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import ProductCard from '@/Components/ProductCard.vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({
    products: Array,
    filters: Object
});

const page = usePage();
const isScrolled = ref(false);
const selectedCategory = ref(props.filters.category || '');

const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth || { user: null });
const isUserAdmin = computed(() => auth.value?.user && (auth.value.user.role === 'admin' || auth.value.user.is_admin));

const categories = [
    'BASIC COLLECTION',
    'SEAMLESS EVERYDAY',
    'SPORTS ACTIVE'
];

watch(selectedCategory, (value) => {
    router.get(route('archive.index'), { category: value }, { 
        preserveState: true, 
        replace: true 
    });
});

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Archive Repository // Adonis Studio" />

    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white overflow-x-hidden font-black">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 shadow-sm border-b border-zinc-100' : 'bg-white py-4 border-b border-zinc-50']" 
             class="fixed top-0 w-full z-[110] transition-all duration-500 px-6 md:px-12 flex items-center justify-between font-black">
            
            <div class="flex items-center gap-10 font-black">
                <Link :href="route('welcome')" class="relative flex items-center group/nav font-black">
                    <div class="bg-zinc-900 text-white h-7 px-2 flex items-center justify-center transition-all duration-500 group-hover/nav:md:px-4 text-sm font-black italic">
                        A<span class="max-w-0 overflow-hidden whitespace-nowrap text-[8px] tracking-[0.4em] transition-all duration-500 group-hover/nav:max-w-[100px] group-hover/nav:ml-2 uppercase italic hidden md:block">STUDIO</span>
                    </div>
                    <div class="flex flex-col ml-3 leading-none text-left">
                        <span class="text-sm font-black tracking-[-0.05em] uppercase font-['Plus_Jakarta_Sans']">ADONIS</span>
                        <span class="text-[5px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 italic font-bold">Lesbian Philippines</span>
                    </div>
                </Link>
            </div>

            <div class="flex items-center gap-10">

                <Link :href="route('cart.index')" class="hidden md:block relative text-zinc-400 hover:text-[#10B981] transition-colors">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span v-if="cartCount > 0" class="absolute -top-1.5 -right-2 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-black">{{ cartCount }}</span>
                </Link>

                <Link :href="auth.user ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="text-zinc-400 hover:text-black transition-colors font-black">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left font-black">
            <div class="flex flex-col gap-0.5 mb-8 border-b border-zinc-900 pb-4">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic">Step 01: Acquisition</span>
                <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                    Archive <span class="text-zinc-200">Repository</span>
                </h1>
            </div>

            <header class="mb-10">
                <div class="flex overflow-x-auto no-scrollbar gap-2 pb-4 -mx-4 px-4 md:mx-0 md:px-0 md:flex-wrap">
                    <button @click="selectedCategory = ''" 
                            :class="[selectedCategory === '' ? 'bg-zinc-900 text-white border-zinc-900 shadow-lg' : 'bg-zinc-50 text-zinc-400 border-zinc-100 hover:text-black']"
                            class="whitespace-nowrap px-6 py-3 border text-[8px] font-black uppercase tracking-[0.4em] transition-all italic rounded-lg active:scale-95 shrink-0">
                        All Units
                    </button>
                    
                    <button v-for="cat in categories" :key="cat"
                            @click="selectedCategory = cat"
                            :class="[selectedCategory === cat ? 'bg-zinc-900 text-white border-zinc-900 shadow-lg' : 'bg-zinc-50 text-zinc-400 border-zinc-100 hover:text-black']"
                            class="whitespace-nowrap px-6 py-3 border text-[8px] font-black uppercase tracking-[0.4em] transition-all italic rounded-lg active:scale-95 shrink-0">
                        {{ cat }}
                    </button>
                </div>
            </header>

            <div v-if="products.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div v-for="product in products" :key="product.id">
                    <ProductCard :product="product" />
                </div>
            </div>

            <div v-else class="py-32 md:py-48 text-center space-y-6 max-w-sm mx-auto font-black">
                <div class="inline-block p-10 bg-zinc-50 rounded-2xl border-2 border-dashed border-zinc-100 opacity-40 grayscale">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h2 class="text-xl font-black uppercase italic tracking-tighter font-['Plus_Jakarta_Sans']">No Units Found</h2>
                    <p class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold">No results matching your current acquisition filter.</p>
                </div>
                <button @click="selectedCategory = ''" class="inline-block w-full bg-zinc-900 text-white px-10 py-4 rounded-lg text-[9px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl italic active:scale-95">
                    Clear All Filters
                </button>
            </div>
        </main>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-3 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.08)] font-black">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300']"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="[route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300']">Home</span>
            </Link>
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300']"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="[route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300']">Shop</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span v-if="cartCount > 0" class="absolute -top-1.5 right-0 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white">{{ cartCount }}</span>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']">Bag</span>
            </Link>
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300']"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="[route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300']">Orders</span>
            </Link>
        </nav>

        <StudioNotification />
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>