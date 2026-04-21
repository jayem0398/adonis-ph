<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';
import StudioNotification from '@/Components/StudioNotification.vue';
import { ref, onMounted, computed, onUnmounted } from 'vue';

const props = defineProps({ 
    products: { type: Array, default: () => [] }, 
    allProducts: { type: Array, default: () => [] },
    cartCount: { type: Number, default: 0 }
});

const page = usePage();
const displayCartCount = computed(() => page.props.cartCount !== undefined ? page.props.cartCount : props.cartCount);
const auth = computed(() => page.props.auth || {});
const isUserAdmin = computed(() => {
    const user = auth.value?.user;
    return user && (user.role === 'admin' || user.is_admin);
});

const isScrolled = ref(false);
const showBackToTop = ref(false); 
const scrollContainer = ref(null); 
const canScrollLeft = ref(false);

const collections = [
    { name: 'BASIC COLLECTION', slug: 'basic', desc: 'EVERYDAY WEAR', img: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=800' },
    { name: 'SEAMLESS SERIES', slug: 'seamless', desc: 'COMFORT AND STYLE', img: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=800' },
    { name: 'SPORTSWEAR', slug: 'sports', desc: 'ACTIVE LIFESTYLE', img: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=800' }
];

const updateScrollButtons = () => {
    if (scrollContainer.value) {
        canScrollLeft.value = scrollContainer.value.scrollLeft > 30;
    }
};

const scrollSide = (direction) => {
    if (scrollContainer.value) {
        const jump = window.innerWidth < 768 ? window.innerWidth * 0.75 : 450;
        scrollContainer.value.scrollBy({ left: direction === 'left' ? -jump : jump, behavior: 'smooth' });
        setTimeout(updateScrollButtons, 500);
    }
};

const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
    showBackToTop.value = window.scrollY > 500;
};

const filterByCategory = (category) => {
    router.get(route('archive.index'), { category: category });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    if (scrollContainer.value) {
        scrollContainer.value.addEventListener('scroll', updateScrollButtons);
    }
});
onUnmounted(() => window.removeEventListener('scroll', handleScroll));
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Playfair+Display:ital,wght@1,900&display=swap" rel="stylesheet">
    <Head title="ADONIS STUDIO | ENGINEERED FOR IDENTITY" />

    <div class="bg-white min-h-screen selection:bg-[#10B981] selection:text-white font-black antialiased text-zinc-900 overflow-x-hidden font-['Inter']">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 shadow-sm border-b border-zinc-100' : 'bg-transparent py-5 md:py-8']" 
             class="fixed top-0 w-full z-[100] transition-all duration-500 px-6 md:px-12 flex items-center justify-between">
            <Link :href="route('welcome')" class="relative flex items-center group/nav">
                <div class="bg-zinc-900 text-white h-7 md:h-9 px-2 flex items-center justify-center transition-all duration-500 group-hover/nav:md:px-5 relative overflow-hidden text-lg">
                    A<span class="max-w-0 overflow-hidden whitespace-nowrap text-[8px] tracking-[0.4em] transition-all duration-500 group-hover/nav:max-w-[100px] group-hover/nav:ml-2 uppercase hidden md:block">STUDIO</span>
                </div>
                <div class="flex flex-col ml-3 leading-none text-left font-black">
                    <span class="text-base md:text-lg tracking-[-0.05em] uppercase transition-all group-hover/nav:tracking-widest duration-500 group-hover/nav:text-[#10B981]">ADONIS</span>
                    <span class="text-[5px] md:text-[6px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 italic font-bold">Philippines</span>
                </div>
            </Link>
            <div class="flex items-center gap-10">
                <Link :href="auth.user ? (isUserAdmin ? route('admin.products.index') : route('profile.edit')) : route('login')" class="p-1 hover:text-[#10B981] transition-colors">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <section class="relative min-h-[85vh] md:min-h-screen flex flex-col lg:flex-row items-center justify-start lg:justify-center overflow-hidden bg-white">
            <div class="w-full lg:w-1/2 h-[50vh] lg:h-screen relative order-1 lg:order-2 overflow-hidden">
                <img src="/images/hero-model.jpg" class="w-full h-full object-cover grayscale brightness-110 contrast-125 transition-transform duration-[4s] hover:scale-105" />
                <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent lg:hidden font-black"></div>
            </div>
            <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left order-2 lg:order-1 px-6 mt-[-40px] lg:mt-0 relative z-10 lg:pl-24 font-black">
                <div class="space-y-2">
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-4 lg:mb-12">
                        <span class="h-[1px] w-12 bg-[#10B981]"></span>
                        <span class="text-[10px] uppercase tracking-[0.8em] text-zinc-900 font-black">Release 001 // v.2026</span>
                    </div>
                    <div class="relative flex flex-col items-center lg:items-start leading-none font-black">
                        <h1 class="text-[16vw] lg:text-[11rem] tracking-[-0.02em] uppercase absolute top-[30%] lg:left-24 z-0 opacity-[0.05] italic pointer-events-none" style="-webkit-text-stroke: 1.5px #000; color: transparent;">STUDIO</h1>
                        <h1 class="text-[20vw] lg:text-[12rem] tracking-[-0.05em] uppercase relative z-10 text-zinc-900 flex items-baseline drop-shadow-2xl">
                            <span class="text-[28vw] lg:text-[17rem] leading-[0.6] mr-[-0.02em] font-['Playfair_Display'] italic uppercase">A</span>DONIS
                        </h1>
                        <h1 class="text-[13vw] lg:text-[10rem] tracking-[-0.04em] uppercase text-zinc-200 italic absolute top-[55%] lg:left-36 z-20 mix-blend-darken pointer-events-none">STUDIO</h1>
                    </div>
                </div>
                <div class="max-w-md space-y-8 mt-12 lg:mt-32 lg:ml-36">
                    <p class="text-[10px] md:text-sm text-zinc-400 leading-relaxed uppercase tracking-[0.3em] italic">Engineered for high-compression architecture.<br/><span class="text-zinc-900 not-italic font-bold">Identity documented in Manila.</span></p>
                    <Link :href="route('archive.index')" class="bg-zinc-900 text-white px-12 py-5 text-[10px] uppercase tracking-[0.5em] hover:bg-[#10B981] transition-all flex items-center gap-4 w-fit group mx-auto lg:mx-0 font-black shadow-lg">
                        Shop Collection
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                    </Link>
                </div>
            </div>
        </section>

        <div class="bg-zinc-900 py-6 overflow-hidden border-y border-zinc-800 flex mt-[-1px]">
            <div class="flex animate-marquee whitespace-nowrap gap-16 text-[#10B981] font-black uppercase tracking-[0.8em] text-[10px]">
                <span v-for="i in 10" :key="i">ADONIS STUDIO // NEW ARRIVALS // FREE SHIPPING // SYSTEM ACTIVE //</span>
            </div>
        </div>

        <section class="bg-white py-20 md:py-32 px-6 md:px-12 border-b border-zinc-100 group/section overflow-hidden">
            <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <div class="lg:col-span-6 space-y-8 text-left font-black">
                    <div class="space-y-2">
                        <span class="text-[9px] text-[#10B981] uppercase tracking-[0.8em] font-black italic block font-black">Brand Protocol | 001</span>
                        <h2 class="text-4xl md:text-6xl tracking-tighter leading-[0.9] italic font-black uppercase font-black">DESIGNED FOR <br/> <span class="text-zinc-200 not-italic">EVERY MOVEMENT</span></h2>
                    </div>
                    <p class="text-[11px] text-zinc-500 uppercase tracking-widest leading-loose max-w-lg font-bold italic font-black">Adonis Studio is dedicated to creating premium apparel that adapts to your modern lifestyle. Quality Manila documented.</p>
                </div>
                <div class="lg:col-span-6 relative flex justify-center items-center h-[400px] md:h-[500px]">
                    <div class="relative w-full max-w-[320px] h-full flex items-center justify-center font-black">
                        <div class="absolute w-full aspect-[3/4] bg-white border-4 border-white shadow-2xl transition-all duration-700 ease-out z-10 grayscale group-hover/section:grayscale-0 group-hover/section:-translate-x-32 group-hover/section:-rotate-6">
                            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=1000" class="w-full h-full object-cover" />
                        </div>
                        <div class="absolute w-full aspect-[3/4] bg-white border-4 border-white shadow-2xl transition-all duration-700 ease-out z-20 grayscale group-hover/section:grayscale-0 group-hover/section:translate-x-32 group-hover/section:rotate-6 translate-y-12">
                            <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=1000" class="w-full h-full object-cover" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-16 md:py-20 px-6 md:px-12 max-w-[1400px] mx-auto border-b border-zinc-100 font-black">
            <div class="flex justify-between items-end mb-8 font-black font-black">
                <div class="space-y-1">
                    <span class="text-[9px] text-[#10B981] tracking-[0.5em] uppercase font-black">Archive Index</span>
                    <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tighter italic leading-none">UNIT COLLECTIONS</h2>
                </div>
                <Link :href="route('archive.index')" class="text-[8px] uppercase tracking-[0.3em] border-b border-zinc-900 pb-1">Shop All</Link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-8">
                <div v-for="col in collections" :key="col.name" @click="filterByCategory(col.slug)" 
                    class="group relative aspect-[4/5] md:aspect-square bg-zinc-100 overflow-hidden cursor-pointer border border-zinc-100 rounded-2xl shadow-sm transition-all duration-500">
                    <img :src="col.img" class="w-full h-full object-cover grayscale opacity-70 group-hover:scale-105 group-hover:blur-[2px] transition-all duration-700" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 bg-black/30 md:bg-black/20 md:opacity-0 md:group-hover:opacity-100 transition-all duration-500">
                        <div class="text-center md:translate-y-4 md:group-hover:translate-y-0 transition-all duration-500">
                            <span class="text-[8px] text-[#10B981] tracking-[0.5em] uppercase mb-1 block font-black">Explore</span>
                            <h3 class="text-xl md:text-2xl text-white font-black uppercase italic tracking-tighter leading-none">{{ col.name }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="products && products.length > 0" class="bg-white py-16 md:py-20 border-b border-zinc-100 font-black">
            <div class="max-w-[1400px] mx-auto px-6 md:px-12">
                <div class="flex flex-col gap-2 text-left mb-8 font-black">
                    <span class="text-[9px] text-[#10B981] tracking-[0.8em] uppercase italic font-black">Acquisition List</span>
                    <h2 class="text-2xl md:text-5xl uppercase tracking-tighter italic leading-none">MOST <span class="text-zinc-200 not-italic">WANTED</span></h2>
                </div>
                <div class="relative flex items-center group">
                    <button v-show="canScrollLeft" @click="scrollSide('left')" class="absolute -left-2 md:-left-12 lg:left-[-40px] top-1/2 -translate-y-1/2 bg-white w-10 h-10 md:w-14 md:h-14 rounded-full border border-zinc-100 flex items-center justify-center z-10 hover:bg-zinc-900 hover:text-white transition-all shadow-xl font-black">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <div ref="scrollContainer" class="flex overflow-x-auto gap-4 md:gap-8 pb-4 scrollbar-hide snap-x snap-mandatory font-black w-full font-black">
                        <div v-for="product in products" :key="product.id" class="min-w-[75vw] md:min-w-[calc(33.33%-20px)] lg:min-w-[calc(25%-24px)] snap-center font-black">
                            <ProductCard :product="product" :isAdmin="isUserAdmin" />
                        </div>
                    </div>
                    <button @click="scrollSide('right')" class="absolute -right-2 md:-right-12 lg:right-[-40px] top-1/2 -translate-y-1/2 bg-white w-10 h-10 md:w-14 md:h-14 rounded-full border border-zinc-100 flex items-center justify-center z-10 hover:bg-zinc-900 hover:text-white transition-all shadow-xl font-black">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </section>

        <footer class="bg-white py-16 md:py-20 px-6 md:px-12 border-t border-zinc-100 font-black text-left uppercase text-[9px] tracking-widest font-black">
            <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 font-black">
                <div class="space-y-4">
                    <span class="text-2xl font-black uppercase tracking-tighter block italic font-black font-black">ADONIS<span class="text-zinc-200">©</span></span>
                    <p class="text-zinc-400 font-bold leading-relaxed max-w-xs font-black">Studio archive dedicated to documenting identity. Manila documented.</p>
                </div>
                <div class="space-y-4 font-black">
                    <h4 class="text-zinc-900 border-b border-zinc-50 pb-2 italic">Repository</h4>
                    <ul class="space-y-2 text-zinc-500 font-bold font-black">
                        <li><Link :href="route('archive.index')" class="hover:text-[#10B981]">Index Units</Link></li>
                        <li v-for="col in collections" :key="col.slug">
                            <Link :href="route('archive.index', { category: col.slug })" class="hover:text-[#10B981]">{{ col.name }}</Link>
                        </li>
                    </ul>
                </div>
                <div class="space-y-4 font-black">
                    <h4 class="text-zinc-900 border-b border-zinc-50 pb-2 italic">Follow</h4>
                    <div class="flex items-center gap-6 font-black font-black">
                        <a href="#" class="hover:text-[#10B981] transition-all font-black"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                        <a href="#" class="hover:text-[#10B981] transition-all font-black"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg></a>
                    </div>
                </div>
                <div class="space-y-6 font-black">
                    <h4 class="text-zinc-900 border-b border-zinc-100 pb-2 italic">Update Log</h4>
                    <form @submit.prevent="subscribeNewsletter" class="flex items-center w-full border-b border-zinc-900">
                        <input type="email" placeholder="EMAIL@ADONIS.COM" class="bg-transparent border-none text-[9px] px-0 h-10 w-full focus:ring-0 placeholder:text-zinc-200 font-black">
                        <button type="submit" class="text-[8px] hover:text-[#10B981] font-black">JOIN</button>
                    </form>
                </div>
            </div>
        </footer>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-4 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.05)] font-black">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300']"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                <span class="text-[7px] uppercase font-black" :class="[route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300']">Home</span>
            </Link>
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1 font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300']"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="text-[7px] uppercase font-black" :class="[route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300']">Shop</span>
            </Link>
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('orders.index') ? 'text-zinc-900' : 'text-zinc-300']"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/></svg>
                <span class="text-[7px] uppercase font-black" :class="[route().current('orders.index') ? 'text-zinc-900' : 'text-zinc-300']">Orders</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1 font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span v-if="displayCartCount > 0" class="absolute -top-1 right-0 text-[6px] bg-[#10B981] text-white px-1 py-0.5 rounded-full z-20 border border-white font-black">{{ displayCartCount }}</span>
                <span class="text-[7px] uppercase font-black" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']">Bag</span>
            </Link>
        </nav>

        <button v-show="showBackToTop" @click="scrollToTop" class="fixed bottom-24 right-6 md:bottom-10 md:right-10 z-[110] w-12 h-12 rounded-full bg-zinc-900 text-white shadow-xl hover:bg-[#10B981] transition-all flex items-center justify-center font-black">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m18 15-6-6-6 6"/></svg>
        </button>

        <StudioNotification />
    </div>
</template>

<style scoped>
@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
.animate-marquee { animation: marquee 35s linear infinite; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>