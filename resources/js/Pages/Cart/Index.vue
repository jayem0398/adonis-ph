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

// SELYADO: Selection Logic
const selectedItems = ref(props.cartItems.map(item => item.id)); 

const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth || { user: null });
const isUserAdmin = computed(() => auth.value?.user?.role === 'admin' || auth.value?.user?.is_admin);

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };

onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

// SELYADO: Compute total base lang sa mga naka-check
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
    if (confirm('Remove this unit from your bag?')) {
        router.delete(route('cart.destroy', id), { preserveScroll: true });
    }
};

const proceedToCheckout = () => {
    if (selectedItems.value.length === 0) {
        alert('Please select at least one unit to proceed.');
        return;
    }
    router.get(route('checkout.index'), { ids: selectedItems.value });
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { 
    style: 'currency', currency: 'PHP', minimumFractionDigits: 0 
}).format(val);
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Shopping Bag // Adonis Studio" />

    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white overflow-x-hidden font-black">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 border-b border-zinc-100 shadow-sm' : 'bg-white py-4 border-b border-zinc-50']" 
             class="fixed top-0 w-full z-[110] transition-all duration-500 px-6 md:px-12 flex items-center justify-between font-black">
            
            <div class="flex items-center gap-10 font-black">
                <Link :href="route('welcome')" class="relative flex items-center group/nav font-black">
                    <div class="bg-zinc-900 text-white h-7 px-2 flex items-center justify-center transition-all duration-500 group-hover/nav:md:px-4 text-sm font-black italic">
                        A<span class="max-w-0 overflow-hidden whitespace-nowrap text-[8px] tracking-[0.4em] transition-all duration-500 group-hover/nav:max-w-[100px] group-hover/nav:ml-2 uppercase italic hidden md:block">STUDIO</span>
                    </div>
                    <div class="flex flex-col ml-3 leading-none text-left font-black">
                        <span class="text-sm font-black tracking-[-0.05em] uppercase font-['Plus_Jakarta_Sans'] font-black">ADONIS</span>
                        <span class="text-[5px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 italic font-bold font-black">Lesbian Philippines</span>
                    </div>
                </Link>

                <div v-if="cartItems.length > 0" class="hidden md:flex items-center gap-8 border-l border-zinc-100 pl-10 font-black">
                    <Link :href="route('archive.index')" class="text-[9px] uppercase tracking-[0.3em] text-zinc-400 hover:text-black transition-colors font-black">Shop</Link>
                </div>
            </div>

            <div class="flex items-center gap-10 font-black">
                <Link :href="route('cart.index')" class="hidden md:block relative text-[#10B981] transition-colors font-black">
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
            <div class="flex justify-between items-end mb-8 border-b border-zinc-900 pb-4 font-black">
                <div class="flex flex-col gap-0.5 font-black">
                    <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold font-black">Step 02: Verification</span>
                    <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900 font-black">
                        Shopping <span class="text-zinc-200 font-black">Bag</span>
                    </h1>
                </div>

                <div v-if="cartItems.length > 0" class="flex items-center gap-3 mb-1 cursor-pointer select-none" @click="toggleSelectAll">
                    <span class="text-[9px] uppercase tracking-[0.3em] text-zinc-400 font-black">
                        {{ selectedItems.length === cartItems.length ? 'Deselect All' : 'Select All' }}
                    </span>
                    <input type="checkbox" 
                           :checked="selectedItems.length === cartItems.length"
                           class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-black cursor-pointer accent-black transition-all">
                </div>
            </div>

            <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 font-black">
                
                <div class="lg:col-span-7 space-y-3 font-black">
                    <div v-for="item in cartItems" :key="item.id" 
                         class="flex gap-4 p-4 bg-zinc-50 border border-zinc-100 rounded-xl group transition-all duration-500 font-black relative overflow-hidden">
                        
                        <div class="flex items-center pr-1">
                            <input type="checkbox" :value="item.id" v-model="selectedItems" 
                                   class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-black cursor-pointer accent-black transition-all">
                        </div>

                        <div class="w-20 md:w-28 aspect-[3/4] bg-white border border-zinc-100 rounded-lg shrink-0 overflow-hidden shadow-sm font-black">
                            <img :src="item.image.startsWith('http') ? item.image : '/storage/' + item.image" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000" />
                        </div>

                        <div class="flex flex-col justify-between flex-grow py-0.5 font-black text-left">
                            <div class="space-y-3 font-black">
                                <div class="flex justify-between items-start font-black">
                                    <div class="space-y-0.5 font-black">
                                        <h3 class="text-[10px] md:text-xs font-black uppercase tracking-tight italic font-['Plus_Jakarta_Sans'] leading-none font-black">{{ item.name }}</h3>
                                        <p class="text-[7px] text-zinc-400 uppercase tracking-widest font-bold font-black">Size: {{ item.size }} // Unit Index</p>
                                    </div>
                                    <button @click="removeItem(item.id)" class="text-zinc-300 hover:text-red-500 transition-colors font-black">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </div>
                                <p class="text-sm md:text-base font-black italic tracking-tighter font-['Plus_Jakarta_Sans'] font-black">{{ formatCurrency(item.price) }}</p>
                            </div>

                            <div class="flex items-center gap-4 mt-2 font-black">
                                <div class="flex items-center bg-white border border-zinc-200 rounded-lg overflow-hidden font-black">
                                    <button @click="updateQuantity(item.id, item.quantity - 1)" class="px-3 py-1.5 hover:bg-zinc-50 border-r border-zinc-200 text-[10px] font-black">-</button>
                                    <span class="w-8 text-center text-[9px] font-bold font-black">{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item.id, item.quantity + 1)" class="px-3 py-1.5 hover:bg-zinc-50 border-l border-zinc-200 text-[10px] font-black">+</button>
                                </div>
                                <span class="text-[7px] uppercase tracking-widest text-zinc-400 font-bold italic font-black">Adjust Units</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 font-black">
                    <div class="bg-zinc-900 text-white p-6 rounded-xl space-y-6 sticky top-24 shadow-2xl relative overflow-hidden border-b-4 border-[#10B981] font-black italic">
                        <div class="absolute top-4 right-4 text-[6px] text-zinc-700 font-mono italic tracking-widest uppercase font-black">ADX-NODE-BAG</div>
                        
                        <h2 class="text-[8px] font-bold uppercase tracking-[0.3em] text-zinc-500 italic border-b border-zinc-800 pb-3 font-black">Bag Summary</h2>
                        
                        <div class="space-y-4 font-black">
                            <div class="flex justify-between items-center text-[8px] font-bold uppercase tracking-widest text-zinc-500 font-black">
                                <span>Selected Val.</span>
                                <span class="text-white">{{ formatCurrency(selectedSubtotal) }}</span>
                            </div>
                            
                            <div class="pt-6 border-t border-dashed border-zinc-800 flex justify-between items-end font-black">
                                <div class="flex flex-col font-black">
                                    <span class="text-[7px] uppercase tracking-[0.2em] font-bold text-zinc-500 mb-0.5 font-black">Selection Total</span>
                                    <span class="text-3xl font-black italic tracking-tighter leading-none text-[#10B981] font-['Plus_Jakarta_Sans'] font-black">{{ formatCurrency(selectedSubtotal) }}</span>
                                </div>
                            </div>
                        </div>

                        <button @click="proceedToCheckout" 
                                class="group flex items-center justify-between w-full h-12 bg-white text-zinc-900 px-6 rounded-lg text-[9px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] hover:text-white transition-all shadow-xl active:scale-95 italic font-black">
                            <span>Proceed ({{ selectedItems.length }}) Items</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform font-black"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="py-32 md:py-48 text-center space-y-6 font-black max-w-sm mx-auto">
                <div class="inline-block p-10 bg-zinc-50 rounded-2xl border-2 border-dashed border-zinc-100 opacity-40 grayscale font-black">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div class="space-y-1 font-black">
                    <h2 class="text-xl font-black uppercase italic tracking-tighter font-['Plus_Jakarta_Sans'] font-black">Bag Empty</h2>
                    <p class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold font-black">No units detected in persistent storage.</p>
                </div>
                <Link :href="route('archive.index')" class="inline-block w-full bg-zinc-900 text-white px-10 py-4 rounded-lg text-[9px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl italic font-black">
                    Return to Store
                </Link>
            </div>
        </main>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-3 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.08)] font-black">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1 group font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300'"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold font-black" :class="route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300'">Home</span>
            </Link>
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1 group font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300'"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold font-black" :class="route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300'">Shop</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1 group font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300'"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span v-if="cartCount > 0" class="absolute -top-1.5 right-0 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full font-black border border-white">{{ cartCount }}</span>
                <span class="text-[7px] uppercase tracking-widest font-bold font-black" :class="route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300'">Bag</span>
            </Link>
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 group font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300'"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold font-black" :class="route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300'">Orders</span>
            </Link>
        </nav>

        <StudioNotification />
    </div>
</template>

<style scoped>
.transition-all { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
::-webkit-scrollbar { display: none; }
</style>