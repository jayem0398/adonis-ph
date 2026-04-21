<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({ 
    order: Object 
});

const page = usePage();
const isScrolled = ref(false);

const cartCount = computed(() => page.props.cartCount || 0);
const auth = computed(() => page.props.auth || { user: null });
const isUserAdmin = computed(() => auth.value?.user?.role === 'admin' || auth.value?.user?.is_admin);

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { 
    style: 'currency', 
    currency: 'PHP', 
    minimumFractionDigits: 0 
}).format(val);

const subtotal = computed(() => {
    return props.order.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
});

const discountReceived = computed(() => {
    const diff = subtotal.value - props.order.total_amount;
    return diff > 0 ? diff : 0;
});

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="ACQUISITION SUCCESS // ADONIS" />

    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white font-black overflow-x-hidden">
        
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
            </div>

            <div class="flex items-center gap-10 font-black font-black">
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

        <main class="mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left font-black font-black">
            
            <div class="flex flex-col gap-0.5 mb-8 border-b border-zinc-900 pb-4 font-black font-black">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic font-black font-black">Step 04: Completion</span>
                <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900 font-black font-black">
                    Acquisition <span class="text-zinc-200 font-black">Confirmed</span>
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 font-black font-black">
                
                <div class="lg:col-span-7 space-y-12 font-black font-black">
                    <section class="space-y-6 font-black font-black font-black">
                        <h2 class="text-[9px] font-bold uppercase tracking-[0.3em] text-zinc-400 italic font-black font-black">Unit Itemization</h2>
                        <div class="divide-y divide-zinc-100 font-black font-black">
                            <div v-for="item in order.items" :key="item.id" class="py-5 flex justify-between items-center group font-black font-black">
                                <div class="space-y-1 font-black font-black">
                                    <h4 class="text-xs font-black uppercase italic tracking-tight font-black font-black">{{ item.product_name }}</h4>
                                    <p class="text-[7px] text-zinc-400 uppercase tracking-widest font-bold font-black font-black">Config: {{ item.variant_name }} // Qty: {{ item.quantity }}</p>
                                </div>
                                <span class="text-xs font-black italic font-black font-black">{{ formatCurrency(item.price * item.quantity) }}</span>
                            </div>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-6 border-t border-zinc-100 font-black font-black">
                        <div class="space-y-4 font-black font-black text-left">
                            <h2 class="text-[9px] font-bold uppercase tracking-[0.3em] text-zinc-400 italic font-black font-black">Dispatch Node</h2>
                            <div class="text-[10px] uppercase font-black leading-relaxed space-y-1 font-black font-black">
                                <p class="text-zinc-900 font-black font-black">{{ order.first_name }} {{ order.last_name }}</p>
                                <p class="text-zinc-500 font-black font-black">{{ order.address }}</p>
                                <p class="text-zinc-500 font-black font-black">{{ order.city }}, Philippines</p>
                                <p class="text-zinc-500 font-black font-black font-black">PH {{ order.phone }}</p>
                            </div>
                        </div>
                        <div class="space-y-4 font-black font-black text-left">
                            <h2 class="text-[9px] font-bold uppercase tracking-[0.3em] text-zinc-400 italic font-black font-black">Settlement Data</h2>
                            <div class="text-[10px] uppercase font-black leading-relaxed space-y-1 font-black font-black">
                                <p class="text-zinc-500 font-black font-black">Method: <span class="text-zinc-900 font-black font-black">{{ order.payment_method }} Protocol</span></p>
                                <p class="text-zinc-500 font-black font-black">Reference: <span class="text-zinc-900 font-black font-black">#{{ String(order.id).padStart(5, '0') }}</span></p>
                                <p class="text-zinc-500 font-black font-black font-black">Status: <span class="text-[#10B981] font-black italic font-black">Verified</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 font-black font-black">
                    <div class="bg-zinc-50 border border-zinc-100 rounded-2xl p-8 space-y-6 sticky top-24 font-black font-black italic shadow-sm">
                        <h2 class="text-[8px] font-bold uppercase tracking-[0.3em] text-zinc-400 border-b border-zinc-200 pb-3 font-black font-black">Final Valuation</h2>
                        
                        <div class="space-y-4 font-black font-black">
                            <div class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-zinc-400 font-black font-black">
                                <span>Gross Subtotal</span>
                                <span>{{ formatCurrency(subtotal) }}</span>
                            </div>
                            <div v-if="order.voucher_code" class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-red-500 font-black font-black font-black">
                                <span>Discount Protocol ({{ order.voucher_code }})</span>
                                <span>- {{ formatCurrency(discountReceived) }}</span>
                            </div>
                            <div class="flex justify-between text-[8px] font-bold uppercase tracking-widest text-zinc-400 font-black font-black font-black">
                                <span>Logistics Protocol</span>
                                <span class="text-[#10B981] font-black font-black">Included</span>
                            </div>
                            
                            <div class="pt-6 border-t border-dashed border-zinc-200 font-black font-black text-left font-black">
                                <span class="text-[7px] uppercase tracking-[0.2em] font-bold text-zinc-400 mb-2 block font-black font-black font-black">Total Acquisition Settlement</span>
                                <span class="text-4xl md:text-5xl font-black italic tracking-tighter leading-none text-[#10B981] font-['Plus_Jakarta_Sans'] font-black font-black font-black font-black">{{ formatCurrency(order.total_amount) }}</span>
                            </div>
                        </div>

                        <div class="pt-6 space-y-3 font-black font-black print:hidden">
                            <Link :href="route('orders.index')" class="flex items-center justify-center w-full h-12 bg-black text-white px-6 rounded-xl text-[9px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all italic font-black font-black font-black">
                                View My Orders
                            </Link>
                            <Link :href="route('archive.index')" class="flex items-center justify-center w-full h-12 bg-white border border-zinc-200 text-zinc-900 px-6 rounded-xl text-[9px] font-black uppercase tracking-[0.4em] hover:bg-zinc-50 transition-all italic font-black font-black font-black font-black">
                                Return to Archive
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-3 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.08)] font-black">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1 group font-black font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5 font-black"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z font-black"/><polyline points="9 22 9 12 15 12 15 22 font-black"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('welcome') ? 'text-zinc-900 font-black' : 'text-zinc-300 font-black font-black'">Home</span>
            </Link>
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1 group font-black font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5 font-black"><rect width="7" height="7" x="3" y="3" rx="1 font-black font-black"/><rect width="7" height="7" x="14" y="3" rx="1 font-black font-black"/><rect width="7" height="7" x="14" y="14" rx="1 font-black font-black"/><rect width="7" height="7" x="3" y="14" rx="1 font-black font-black font-black font-black"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold font-black" :class="route().current('archive.index') ? 'text-zinc-900 font-black' : 'text-zinc-300 font-black font-black'">Shop</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1 group font-black font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5 font-black font-black"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z font-black font-black"/><path d="M3 6h18 font-black font-black font-black"/><path d="M16 10a4 4 0 0 1-8 0 font-black font-black font-black font-black"/></svg>
                <span v-if="cartCount > 0" class="absolute -top-1.5 right-0 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-black font-black">{{ cartCount }}</span>
                <span class="text-[7px] uppercase tracking-widest font-bold text-zinc-300 font-black font-black font-black">Bag</span>
            </Link>
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 group font-black font-black font-black">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5 font-black font-black font-black"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2 font-black font-black font-black font-black"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1 font-black font-black font-black font-black font-black"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold font-black font-black font-black font-black font-black" :class="route().current('orders.*') ? 'text-zinc-900 font-black' : 'text-zinc-300 font-black font-black font-black'">Orders</span>
            </Link>
        </nav>

        <footer class="mt-auto py-8 border-t border-zinc-50 text-center font-black pb-24 md:pb-8 font-black font-black">
            <span class="text-[7px] uppercase tracking-[1em] text-zinc-200 italic font-black font-black font-black">Adonis Philippines Studio Manifest 2026</span>
        </footer>

        <StudioNotification />
    </div>
</template>

<style scoped>
.transition-all { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
::-webkit-scrollbar { display: none; }
@media print {
    nav, footer { display: none !important; }
    main { margin-top: 0 !important; }
}
</style>