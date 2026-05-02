<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({ product: Object });
const page = usePage();
const isScrolled = ref(false);

const activeImage = ref(props.product.image_path);

const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth || { user: null });
const isUserAdmin = computed(() => auth.value?.user && (auth.value.user.role === 'admin' || auth.value.user.is_admin));

const form = useForm({
    variant_id: props.product.variants?.find(v => v.stock > 0)?.id || (props.product.variants?.[0]?.id || null),
    quantity: 1
});

const isFullySoldOut = computed(() => {
    return props.product.variants && props.product.variants.every(v => v.stock <= 0);
});

const selectedVariant = computed(() => 
    props.product.variants.find(v => v.id === form.variant_id)
);

const isSelectedVariantSoldOut = computed(() => {
    return selectedVariant.value ? selectedVariant.value.stock <= 0 : true;
});

const getImageUrl = (path) => {
    if (!path) return '/images/unit-placeholder.jpg';
    return path.startsWith('http') ? path : '/storage/' + path;
};

const addToCart = () => {
    if (!form.variant_id || isSelectedVariantSoldOut.value || isFullySoldOut.value) return;
    form.post(route('cart.add'), { 
        preserveScroll: true,
        onSuccess: () => {
            // Optional Feedback logic
        }
    });
};

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head :title="`${product.name} // Manifest`" />

    <div class="min-h-screen bg-white font-black antialiased text-zinc-900 selection:bg-[#10B981] selection:text-white font-['Inter'] flex flex-col overflow-x-hidden">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 shadow-sm border-b border-zinc-100' : 'bg-white py-5 md:py-8 border-b border-zinc-50']" 
             class="fixed top-0 w-full z-[110] transition-all duration-500 px-6 md:px-12 flex items-center justify-between">
            
            <div class="flex items-center gap-10">
                <Link :href="route('welcome')" class="relative flex items-center group/nav">
                    <div class="bg-zinc-900 text-white h-7 md:h-9 px-2 flex items-center justify-center transition-all duration-500 group-hover/nav:md:px-5 relative overflow-hidden text-lg font-black">
                        A<span class="max-w-0 overflow-hidden whitespace-nowrap text-[8px] tracking-[0.4em] transition-all duration-500 group-hover/nav:max-w-[100px] group-hover/nav:ml-2 uppercase hidden md:block">STUDIO</span>
                    </div>
                    <div class="flex flex-col ml-3 leading-none text-left">
                        <span class="text-base md:text-lg font-black tracking-[-0.05em] uppercase transition-all group-hover/nav:tracking-widest duration-500 text-zinc-900">ADONIS</span>
                        <span class="text-[5px] md:text-[6px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 italic font-bold">Philippines</span>
                    </div>
                </Link>

                <div class="hidden md:flex items-center gap-8 border-l border-zinc-100 pl-10">
                    <Link :href="route('archive.index')" class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400 hover:text-zinc-900 transition-colors">Archive</Link>
                </div>
            </div>

            <div class="flex items-center gap-6 md:gap-10">
                <Link :href="route('cart.index')" class="hidden md:block relative text-zinc-400 hover:text-[#10B981] transition-colors">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span v-if="cartCount > 0" class="absolute -top-1.5 -right-2 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-black shadow-sm">{{ cartCount }}</span>
                </Link>

                <Link :href="auth.user ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="p-1 text-zinc-400 hover:text-zinc-900 transition-colors">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="flex-grow mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left font-black">
            
            <div class="flex flex-col gap-0.5 mb-8 border-b border-zinc-900 pb-4">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic">Step 01: Acquisition</span>
                <h1 class="text-2xl md:text-3xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                    Unit <span class="text-zinc-200">Manifest</span>
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start font-black">
                
                <div class="lg:col-span-7 flex flex-col md:flex-row gap-4 font-black">
                    <div v-if="product.images?.length > 0" 
                         class="flex md:flex-col gap-2 overflow-x-auto md:overflow-y-auto no-scrollbar shrink-0 md:w-16 md:max-h-[480px]">
                        
                        <button @click="activeImage = product.image_path"
                                class="w-14 h-18 bg-zinc-50 border transition-all overflow-hidden shrink-0 rounded-lg font-black"
                                :class="activeImage === product.image_path ? 'border-zinc-900 ring-2 ring-zinc-900/10' : 'border-zinc-100'">
                            <img :src="getImageUrl(product.image_path)" class="w-full h-full object-cover font-black">
                        </button>

                        <button v-for="img in product.images" :key="img.id" @click="activeImage = img.image_path"
                                class="w-14 h-18 bg-zinc-50 border transition-all overflow-hidden shrink-0 rounded-lg font-black"
                                :class="activeImage === img.image_path ? 'border-zinc-900 ring-2 ring-zinc-900/10' : 'border-zinc-100'">
                            <img :src="getImageUrl(img.image_path)" class="w-full h-full object-cover font-black">
                        </button>
                    </div>

                    <div class="flex-1 bg-zinc-50 border border-zinc-100 aspect-[3/4] overflow-hidden group/img relative rounded-xl max-w-[420px] mx-auto lg:mx-0 shadow-sm font-black">
                        <div v-if="isFullySoldOut" class="absolute inset-0 z-20 flex items-center justify-center bg-zinc-900/60 backdrop-blur-sm">
                            <div class="border border-white/50 px-5 py-2.5 transform -rotate-12 font-black">
                                <span class="text-white text-sm uppercase font-black italic tracking-[0.4em]">Sold Out</span>
                            </div>
                        </div>
                        <img :src="getImageUrl(activeImage)" 
                             class="w-full h-full object-cover transition-all duration-700 group-hover/img:scale-105 font-black">
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-8 lg:sticky lg:top-40 font-black">
                    <div class="space-y-3 font-black">
                        <div class="flex items-center gap-2 font-black">
                            <span class="text-[7px] uppercase tracking-[0.5em] text-[#10B981] italic font-black">Ref: {{ String(product.id).padStart(4, '0') }}</span>
                            <span class="text-zinc-200">//</span>
                            <span class="text-[7px] uppercase tracking-[0.4em] text-zinc-400 italic font-black">{{ product.category }}</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tighter leading-[0.9] italic font-['Plus_Jakarta_Sans']">{{ product.name }}</h2>
                        <p class="text-[9px] text-zinc-500 uppercase tracking-widest leading-relaxed italic max-w-sm font-black">
                            {{ product.description || 'Archival studio piece designed for identity and movement.' }}
                        </p>
                    </div>

                    <div class="space-y-6 pt-6 border-t border-zinc-100 font-black">
                        <div class="space-y-3 font-black">
                            <label class="text-[7px] uppercase tracking-[0.5em] text-zinc-400 font-black">Configuration Selection</label>
                            <div class="grid grid-cols-4 gap-2 font-black">
                                <button v-for="v in product.variants" :key="v.id" @click="form.variant_id = v.id"
                                        :disabled="v.stock <= 0"
                                        :class="[
                                            form.variant_id === v.id ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white border-zinc-100 text-zinc-400 hover:text-black hover:border-zinc-300',
                                            v.stock <= 0 ? 'opacity-30 border-dashed cursor-not-allowed' : ''
                                        ]"
                                        class="h-9 border text-[8px] font-black uppercase transition-all italic rounded-lg font-black">
                                    {{ v.size }}
                                </button>
                            </div>
                        </div>

                        <div class="pt-6 space-y-5 font-black">
                            <div class="flex justify-between items-end font-black">
                                <span class="text-[8px] uppercase tracking-[0.4em] text-zinc-400 italic font-black">Valuation</span>
                                <span class="text-2xl font-black tracking-tighter italic font-['Plus_Jakarta_Sans']">
                                    ₱{{ selectedVariant ? parseFloat(selectedVariant.price).toLocaleString() : '—' }}
                                </span>
                            </div>

                            <button @click="addToCart" :disabled="isSelectedVariantSoldOut || form.processing" 
                                    class="group flex items-center justify-between w-full h-12 px-6 text-[8px] font-black uppercase tracking-[0.5em] transition-all active:scale-95 disabled:cursor-not-allowed rounded-xl shadow-lg font-black"
                                    :class="[isSelectedVariantSoldOut ? 'bg-zinc-100 text-zinc-400' : 'bg-black text-white hover:bg-[#10B981]']">
                                <span>{{ isSelectedVariantSoldOut ? 'Depleted' : (form.processing ? 'Syncing...' : 'Acquire Piece') }}</span>
                                <svg v-if="!isSelectedVariantSoldOut" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform font-black"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="hidden md:block bg-zinc-50 py-8 px-6 md:px-12 border-t border-zinc-200 mt-auto">
            <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-between items-center gap-6 text-[8px] text-zinc-400 uppercase tracking-widest font-black">
                <p>Copyright © 2026 Adonis Studio | All Rights Reserved</p>
                <div class="flex gap-6">
                    <Link href="#" class="hover:text-zinc-900 transition-colors">Privacy Policy</Link>
                    <Link href="#" class="hover:text-zinc-900 transition-colors">Terms of Service</Link>
                </div>
            </div>
        </footer>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-4 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.08)]">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1.5">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300']"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-black" :class="[route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300']">Home</span>
            </Link>
            
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1.5">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300']"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-black" :class="[route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300']">Shop</span>
            </Link>
            
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1.5">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300']"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-black" :class="[route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300']">Orders</span>
            </Link>
            
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1.5">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span v-if="cartCount > 0" class="absolute -top-1.5 right-0 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-black shadow-sm">{{ cartCount }}</span>
                <span class="text-[7px] uppercase tracking-widest font-black" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']">Bag</span>
            </Link>
        </nav>

        <StudioNotification />
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>