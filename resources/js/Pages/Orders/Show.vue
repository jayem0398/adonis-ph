<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({ order: Object, auth: Object });
const page = usePage();
const isScrolled = ref(false);

const showCancelModal = ref(false);
const showReturnModal = ref(false);

const cartCount = computed(() => page.props.cartCount || 0);
const authUser = computed(() => page.props.auth?.user || null);
const isUserAdmin = computed(() => authUser.value && (authUser.value.role === 'admin' || authUser.value.is_admin));

const cancelForm = useForm({ reason_type: '', description: '' });
const returnForm = useForm({ reason_type: '', description: '', proof: null });

const isCancellable = computed(() => props.order.status?.toLowerCase() === 'pending');
const isReturnable = computed(() => props.order.status?.toLowerCase() === 'delivered');

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0 }).format(val);

const resolveImage = (item) => {
    const rawPath = item.product?.image_path || item.image_path;
    if (!rawPath) return 'https://placehold.co/400x500/18181b/ffffff?text=ADONIS+VOID';
    return rawPath.startsWith('http') ? rawPath : `/storage/${rawPath.replace(/^public\//, '')}`;
};

const getStatusTheme = (status) => {
    const s = status ? status.toLowerCase() : '';
    if (s.includes('pending')) return 'text-amber-500 border-amber-200 bg-amber-50';
    if (s.includes('process')) return 'text-blue-500 border-blue-200 bg-blue-50';
    if (s.includes('ship')) return 'text-indigo-500 border-indigo-200 bg-indigo-50';
    if (s.includes('deliver') || s.includes('complete')) return 'text-[#10B981] border-[#10B981]/20 bg-[#10B981]/5';
    if (s.includes('cancel') || s.includes('fail')) return 'text-red-500 border-red-200 bg-red-50';
    return 'text-zinc-500 border-zinc-200 bg-zinc-50';
};

const submitCancel = () => {
    cancelForm.post(route('orders.cancel', props.order.id), {
        onSuccess: () => { showCancelModal.value = false; cancelForm.reset(); }
    });
};

const submitReturn = () => {
    returnForm.post(route('orders.return', props.order.id), {
        onSuccess: () => { showReturnModal.value = false; returnForm.reset(); }
    });
};
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head :title="`Manifest #${order.id}`" />

    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 font-['Inter'] selection:bg-[#10B981] selection:text-white">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 border-b border-zinc-100 shadow-sm' : 'bg-white py-4']" 
             class="fixed top-0 w-full z-[110] transition-all duration-500 px-6 md:px-12 flex items-center justify-between">
            
            <div class="flex items-center gap-10">
                <Link :href="route('welcome')" class="relative flex items-center group/nav">
                    <div class="bg-zinc-900 text-white h-7 px-2 flex items-center justify-center italic text-sm font-black transition-all group-hover:bg-[#10B981]">
                        A<span class="max-w-0 overflow-hidden whitespace-nowrap text-[8px] tracking-[0.45em] transition-all group-hover:max-w-[100px] group-hover:ml-2 uppercase italic hidden md:block">STUDIO</span>
                    </div>
                    <div class="flex flex-col ml-3 leading-none text-left">
                        <span class="text-sm font-bold tracking-[-0.05em] uppercase font-['Plus_Jakarta_Sans']">ADONIS</span>
                        <span class="text-[5px] tracking-[0.45em] text-zinc-400 uppercase mt-0.5 italic font-bold">Philippines</span>
                    </div>
                </Link>

                <div class="hidden md:flex items-center gap-8 border-l border-zinc-100 pl-10 font-medium">
                    <Link :href="route('archive.index')" class="text-[9px] uppercase tracking-[0.3em] text-zinc-400 hover:text-black transition-colors font-medium">Shop</Link>
                    <Link :href="route('orders.index')" class="text-[9px] uppercase tracking-[0.3em] text-black border-b border-black pb-0.5 font-bold">History</Link>
                </div>
            </div>

            <div class="flex items-center gap-6 md:gap-10">
                <Link :href="route('cart.index')" class="hidden md:block relative text-zinc-400 hover:text-[#10B981] transition-colors">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span v-if="cartCount > 0" class="absolute -top-1 -right-2 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-bold">{{ cartCount }}</span>
                </Link>

                <Link :href="authUser ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="text-zinc-400 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left font-medium">
            
            <div class="flex flex-col gap-0.5 mb-8 border-b border-zinc-900 pb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic">Doc ID: #{{ String(order.id).padStart(6, '0') }}</span>
                    <div :class="getStatusTheme(order.status)" class="px-3 py-1 rounded-lg text-[8px] uppercase tracking-[0.2em] italic font-bold border transition-colors">
                        {{ order.status.replace('_', ' ') }}
                    </div>
                </div>
                <h1 class="text-2xl md:text-4xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                    Order <span class="text-zinc-200">Manifest</span>
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-8 space-y-4">
                    <div v-for="item in order.items" :key="item.id" class="flex gap-5 p-5 bg-zinc-50 border border-zinc-100 rounded-2xl group transition-all hover:bg-white hover:border-zinc-200 hover:shadow-xl hover:shadow-zinc-100">
                        <div class="w-16 md:w-24 aspect-[3/4] bg-white border border-zinc-100 rounded-xl overflow-hidden shrink-0 shadow-sm">
                            <img :src="resolveImage(item)" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000">
                        </div>
                        <div class="flex-1 flex flex-col justify-between py-1 font-medium">
                            <div>
                                <h3 class="text-xs md:text-sm font-black uppercase tracking-tight italic font-['Plus_Jakarta_Sans'] leading-none group-hover:text-[#10B981] transition-colors">{{ item.product_name }}</h3>
                                <p class="text-[7px] text-zinc-400 uppercase tracking-widest mt-2 font-bold">Config: {{ item.variant_name || 'Standard' }} // Units: {{ item.quantity }}</p>
                            </div>
                            <span class="text-sm font-black italic tracking-tighter">{{ formatCurrency(item.price * item.quantity) }}</span>
                        </div>
                    </div>

                    <div class="pt-8 flex flex-wrap gap-4">
                        <button v-if="isCancellable" @click="showCancelModal = true" 
                                class="px-6 py-3 border border-red-100 bg-red-50/30 text-red-500 text-[8px] uppercase tracking-[0.3em] font-bold rounded-xl hover:bg-red-500 hover:text-white transition-all">
                            Request Cancellation
                        </button>
                        <button v-if="isReturnable" @click="showReturnModal = true" 
                                class="px-6 py-3 border border-zinc-100 bg-zinc-50 text-zinc-500 text-[8px] uppercase tracking-[0.3em] font-bold rounded-xl hover:bg-zinc-900 hover:text-white transition-all">
                            File Return Request
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-zinc-900 text-white p-8 rounded-[2rem] space-y-8 italic border-b-4 border-[#10B981] shadow-2xl">
                        <div class="space-y-4">
                            <h4 class="text-[7px] font-bold uppercase tracking-[0.4em] text-zinc-500 border-b border-zinc-800 pb-4">Financial Log</h4>
                            <div class="flex flex-col">
                                <span class="text-[7px] text-zinc-500 uppercase italic font-medium mb-1 tracking-widest">Final Settlement Paid</span>
                                <span class="text-4xl font-black italic tracking-tighter text-[#10B981] font-['Plus_Jakarta_Sans']">{{ formatCurrency(order.total_amount || 0) }}</span>
                            </div>
                        </div>

                        <div class="space-y-4 pt-6 border-t border-zinc-800">
                            <h4 class="text-[7px] font-bold uppercase tracking-[0.4em] text-zinc-500 italic">Consignee Identity</h4>
                            <div class="text-[9px] uppercase font-medium tracking-widest leading-relaxed space-y-1">
                                <p class="text-white font-bold">{{ order.first_name }} {{ order.last_name }}</p>
                                <p class="text-zinc-400">{{ order.address }}, {{ order.city }}</p>
                                <p class="text-zinc-600 font-bold mt-2 tracking-tighter">Terminal Node: {{ order.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
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