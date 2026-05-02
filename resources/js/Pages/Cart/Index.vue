<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({
    cartItems: { type: Array, default: () => [] },
    cartTotal: { type: Number, default: 0 }
});

const page = usePage();
const isScrolled = ref(false);

const selectedItems = ref(props.cartItems.map(item => item.id)); 

const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth || { user: null });
const isUserAdmin = computed(() => auth.value?.user?.role === 'admin' || auth.value?.user?.is_admin);

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };

onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

const selectedSubtotal = computed(() => {
    return props.cartItems
        .filter(item => selectedItems.value.includes(item.id))
        .reduce((acc, item) => acc + (item.price * item.quantity), 0);
});

const toggleSelectAll = () => {
    if (selectedItems.value.length === props.cartItems.length) {
        selectedItems.value = [];
    } else {
        selectedItems.value = props.cartItems.map(item => item.id);
    }
};

const updateQuantity = (id, quantity) => {
    if (quantity < 1) return;
    router.put(route('cart.update', id), { quantity }, { preserveScroll: true });
};

const removeItem = (id) => {
    if (confirm('Remove this item from your cart?')) {
        router.delete(route('cart.destroy', id), { preserveScroll: true });
    }
};

const proceedToCheckout = () => {
    if (selectedItems.value.length === 0) {
        alert('Please select at least one item to proceed.');
        return;
    }
    // BULLETPROOF FIX: Iko-convert natin ang proxy array to plain comma-separated string
    const safeIds = Array.from(selectedItems.value).join(',');
    router.get(route('checkout.index'), { ids: safeIds });
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { 
    style: 'currency', currency: 'PHP', minimumFractionDigits: 0 
}).format(val);
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Your Cart | ADONIS STUDIO" />

    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white font-black overflow-x-hidden">
        
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

                <div v-if="cartItems.length > 0" class="hidden md:flex items-center gap-8 border-l border-zinc-100 pl-10">
                    <Link :href="route('archive.index')" class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400 hover:text-zinc-900 transition-colors">Shop All</Link>
                </div>
            </div>

            <div class="flex items-center gap-6 md:gap-10">
                <Link :href="route('cart.index')" class="hidden md:flex items-center text-[#10B981] transition-colors">
                    <div class="relative inline-block">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        <span v-if="cartCount > 0" class="absolute -top-2 -right-2.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] bg-[#10B981] text-white rounded-full border-[2px] border-white font-black shadow-sm z-10">
                            {{ cartCount }}
                        </span>
                    </div>
                </Link>

                <Link :href="auth.user ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="p-1 text-zinc-400 hover:text-zinc-900 transition-colors">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="flex-grow mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left">
            
            <div class="flex justify-between items-end mb-8 border-b border-zinc-900 pb-4">
                <div class="flex flex-col gap-0.5">
                    <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic">Your Cart</span>
                    <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                        Shopping <span class="text-zinc-200">Cart</span>
                    </h1>
                </div>

                <div v-if="cartItems.length > 0" class="flex items-center gap-3 mb-1 cursor-pointer select-none" @click="toggleSelectAll">
                    <span class="text-[9px] uppercase tracking-[0.3em] text-zinc-400">
                        {{ selectedItems.length === cartItems.length ? 'Deselect All' : 'Select All' }}
                    </span>
                    <input type="checkbox" 
                           :checked="selectedItems.length === cartItems.length"
                           class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-black cursor-pointer accent-black transition-all">
                </div>
            </div>

            <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                
                <div class="lg:col-span-7 space-y-3">
                    <div v-for="item in cartItems" :key="item.id" 
                         class="flex gap-4 p-4 bg-zinc-50 border border-zinc-100 rounded-xl group transition-all duration-500 relative overflow-hidden">
                        
                        <div class="flex items-center pr-1">
                            <input type="checkbox" :value="item.id" v-model="selectedItems" 
                                   class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-black cursor-pointer accent-black transition-all">
                        </div>

                        <div class="w-20 md:w-28 aspect-[3/4] bg-white border border-zinc-100 rounded-lg shrink-0 overflow-hidden shadow-sm">
                            <img :src="item.image.startsWith('http') ? item.image : '/storage/' + item.image" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000" />
                        </div>

                        <div class="flex flex-col justify-between flex-grow py-0.5 text-left">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div class="space-y-0.5">
                                        <h3 class="text-[10px] md:text-xs uppercase tracking-tight italic font-['Plus_Jakarta_Sans'] leading-none">{{ item.name }}</h3>
                                        <p class="text-[7px] text-zinc-400 uppercase tracking-widest font-bold">Size: {{ item.size }}</p>
                                    </div>
                                    <button @click="removeItem(item.id)" class="text-zinc-300 hover:text-red-500 transition-colors">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </div>
                                <p class="text-sm md:text-base italic tracking-tighter font-['Plus_Jakarta_Sans']">{{ formatCurrency(item.price) }}</p>
                            </div>

                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center bg-white border border-zinc-200 rounded-lg overflow-hidden">
                                    <button @click="updateQuantity(item.id, item.quantity - 1)" class="px-3 py-1.5 hover:bg-zinc-50 border-r border-zinc-200 text-[10px]">-</button>
                                    <span class="w-8 text-center text-[9px] font-bold">{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item.id, item.quantity + 1)" class="px-3 py-1.5 hover:bg-zinc-50 border-l border-zinc-200 text-[10px]">+</button>
                                </div>
                                <span class="text-[7px] uppercase tracking-widest text-zinc-400 font-bold italic">Adjust Quantity</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="bg-zinc-900 text-white p-8 rounded-xl space-y-6 sticky top-32 shadow-2xl relative overflow-hidden border-b-4 border-[#10B981] italic">
                        
                        <h2 class="text-[9px] uppercase tracking-[0.3em] text-zinc-500 border-b border-zinc-800 pb-3">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-[8px] uppercase tracking-widest text-zinc-500">
                                <span>Selected Items Subtotal</span>
                                <span class="text-white">{{ formatCurrency(selectedSubtotal) }}</span>
                            </div>
                            
                            <div class="pt-6 border-t border-dashed border-zinc-800 flex justify-between items-end">
                                <div class="flex flex-col">
                                    <span class="text-[8px] uppercase tracking-[0.2em] font-bold text-zinc-500 mb-0.5">Total</span>
                                    <span class="text-3xl italic tracking-tighter leading-none text-[#10B981] font-['Plus_Jakarta_Sans']">{{ formatCurrency(selectedSubtotal) }}</span>
                                </div>
                            </div>
                        </div>

                        <button @click="proceedToCheckout" 
                                class="group flex items-center justify-between w-full h-14 bg-white text-zinc-900 px-6 rounded-lg text-[9px] uppercase tracking-[0.4em] hover:bg-[#10B981] hover:text-white transition-all shadow-xl active:scale-95">
                            <span>Proceed to Checkout ({{ selectedItems.length }} Items)</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="py-32 md:py-48 text-center space-y-6 max-w-sm mx-auto">
                <div class="inline-block p-10 bg-zinc-50 rounded-2xl border-2 border-dashed border-zinc-200 opacity-60 grayscale">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="21" r="2"/><circle cx="19" cy="21" r="2"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl uppercase italic tracking-tighter font-['Plus_Jakarta_Sans']">Cart is Empty</h2>
                    <p class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold">You have no items in your shopping cart.</p>
                </div>
                <Link :href="route('archive.index')" class="inline-block w-full bg-zinc-900 text-white px-10 py-4.5 rounded-xl text-[9px] uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl italic mt-4">
                    Continue Shopping
                </Link>
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
                <div class="relative inline-block">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span v-if="cartCount > 0" class="absolute -top-2 -right-2.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] bg-[#10B981] text-white rounded-full border-[2px] border-white font-black shadow-sm z-20">
                        {{ cartCount }}
                    </span>
                </div>
                <span class="text-[7px] uppercase tracking-widest font-black" :class="[route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300']">Cart</span>
            </Link>
        </nav>

        <StudioNotification />
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
input[type="checkbox"] { -webkit-tap-highlight-color: transparent; }
</style>