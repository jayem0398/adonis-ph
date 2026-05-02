<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({ 
    cart: { type: Array, default: () => [] }, 
    auth: Object 
}); 

const page = usePage();
const isScrolled = ref(false);

const cartCount = computed(() => page.props.cartCount || props.cart?.length || 0);
const isUserAdmin = computed(() => props.auth.user && (props.auth.user.role === 'admin' || props.auth.user.is_admin));

// INTEGRATION STATES
const shippingFee = ref(0); 
const voucherInput = ref('');
const activeVoucher = ref(null);
const voucherError = ref(''); 
const isValidating = ref(false);

const subtotal = computed(() => props.cart?.reduce((acc, item) => acc + (item.price * item.quantity), 0) || 0);

const discountAmount = computed(() => {
    if (!activeVoucher.value) return 0;
    const { type, value } = activeVoucher.value;
    return type === 'percentage' ? subtotal.value * (value / 100) : value;
});

const finalTotal = computed(() => {
    const total = subtotal.value - discountAmount.value + shippingFee.value;
    return Math.max(0, total);
});

// FORM PROTOCOL (Auto-filled from props.auth.user)
const form = useForm({
    first_name: props.auth.user?.first_name || '', 
    last_name: props.auth.user?.last_name || '', 
    email: props.auth.user?.email || '',
    phone: props.auth.user?.phone || '', 
    address: props.auth.user?.address || '', 
    city: props.auth.user?.city || '', 
    payment_method: 'GCash',
    voucher_code: null, 
    shipping_fee: 0, 
    total_amount: 0,
    cart_ids: props.cart?.map(item => item.id) || []
});

// VOUCHER SYNC: Flash data watcher
watch(() => page.props.flash, (flash) => {
    if (flash?.success_voucher) {
        activeVoucher.value = flash.success_voucher;
        form.voucher_code = flash.success_voucher.code;
        voucherError.value = '';
    }
    if (flash?.error_voucher) {
        voucherError.value = flash.error_voucher;
        activeVoucher.value = null;
        form.voucher_code = null;
    }
}, { deep: true, immediate: true });

// REAL-TIME VOUCHER VALIDATION (DEBOUNCED)
let debounceTimer = null;
watch(voucherInput, (newVal) => {
    clearTimeout(debounceTimer);
    
    if (!newVal || newVal.trim() === '') {
        activeVoucher.value = null;
        form.voucher_code = null;
        voucherError.value = '';
        isValidating.value = false;
        return;
    }

    isValidating.value = true;
    voucherError.value = '';

    debounceTimer = setTimeout(() => {
        router.post(route('checkout.validate_voucher'), { 
            code: newVal.toUpperCase() 
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                const success = page.props.flash?.success_voucher;
                if (!success) {
                    activeVoucher.value = null;
                    form.voucher_code = null;
                }
            },
            onError: (errors) => {
                voucherError.value = errors.code || 'Invalid Voucher Code';
                activeVoucher.value = null;
                form.voucher_code = null;
            },
            onFinish: () => {
                isValidating.value = false;
            }
        });
    }, 600);
});

const submit = () => {
    form.shipping_fee = shippingFee.value;
    form.total_amount = finalTotal.value;
    
    form.post(route('checkout.store'), { 
        preserveScroll: true,
        onSuccess: () => { console.log('Acquisition Committed.'); },
        onError: (err) => { console.error('Acquisition Failed:', err); }
    });
};

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { 
    style: 'currency', currency: 'PHP', minimumFractionDigits: 0 
}).format(val);
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Settlement Protocol | ADONIS STUDIO" />

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

                <div v-if="props.cart?.length > 0" class="hidden md:flex items-center gap-8 border-l border-zinc-100 pl-10">
                    <Link :href="route('archive.index')" class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400 hover:text-zinc-900 transition-colors">Shop All</Link>
                </div>
            </div>

            <div class="flex items-center gap-6 md:gap-10">
                <Link :href="auth.user ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="p-1 text-zinc-400 hover:text-zinc-900 transition-colors">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="flex-grow mt-24 md:mt-32 max-w-[1200px] mx-auto w-full px-4 md:px-6 pb-28 text-left">
            
            <Link :href="route('cart.index')" class="flex items-center gap-2 text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400 hover:text-zinc-900 mb-6 transition-all group w-fit">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:-translate-x-1 transition-transform"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                <span>Return to Bag</span>
            </Link>

            <div class="flex flex-col gap-0.5 mb-8 border-b border-zinc-900 pb-4">
                <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-black italic">Step 03: Settlement</span>
                <h1 class="text-3xl md:text-5xl uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                    Checkout <span class="text-zinc-200">Protocol</span>
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <div class="lg:col-span-7">
                    <form @submit.prevent="submit" class="space-y-8">
                        <section class="space-y-6">
                            <h2 class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Dispatch Details</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">First Name</label>
                                    <input v-model="form.first_name" type="text" required class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-bold uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">Last Name</label>
                                    <input v-model="form.last_name" type="text" required class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-bold uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all">
                                </div>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">Full Address</label>
                                <input v-model="form.address" type="text" required class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-bold uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">City / Region</label>
                                    <input v-model="form.city" type="text" required class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-bold uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">Phone Number</label>
                                    <input v-model="form.phone" type="text" required class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-bold uppercase p-4 focus:bg-white focus:border-zinc-900 outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">Settlement Method</label>
                                <select v-model="form.payment_method" class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-bold uppercase p-4 outline-none focus:border-zinc-900 transition-all cursor-pointer appearance-none">
                                    <option value="GCash">GCash // Paymongo</option>
                                    <option value="Maya">Maya // Paymongo</option>
                                    <option value="COD">Cash on Delivery</option>
                                </select>
                            </div>

                            <div class="space-y-1.5 pt-2">
                                <label class="text-[8px] uppercase tracking-[0.2em] text-zinc-400 font-black">Voucher Protocol</label>
                                <div class="relative">
                                    <input v-model="voucherInput" type="text" placeholder="ENTER CODE" 
                                           class="w-full border bg-zinc-50 rounded-xl text-[10px] font-bold uppercase p-4 pr-12 focus:bg-white focus:border-zinc-900 outline-none transition-all"
                                           :class="voucherError ? 'border-red-500' : (activeVoucher ? 'border-[#10B981]' : 'border-zinc-100')">
                                    
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center justify-center pointer-events-none">
                                        <div v-if="isValidating" class="w-3.5 h-3.5 border-2 border-zinc-300 border-t-zinc-900 rounded-full animate-spin"></div>
                                        <svg v-else-if="activeVoucher" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                                        <svg v-else-if="voucherError" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="4"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                    </div>
                                </div>
                                <div v-if="voucherError" class="text-[7px] text-red-500 font-black uppercase mt-2 tracking-widest">{{ voucherError }}</div>
                                <div v-else-if="activeVoucher" class="text-[7px] text-[#10B981] font-black uppercase mt-2 tracking-widest">Protocol Active: {{ activeVoucher.code }}</div>
                            </div>
                        </section>

                        <button type="submit" :disabled="form.processing" class="group flex items-center justify-between w-full h-16 bg-zinc-900 text-white px-8 rounded-xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-[#10B981] transition-all shadow-xl active:scale-95 mt-8">
                            <span>{{ form.processing ? 'Processing...' : 'Commit Acquisition' }}</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-5 font-black">
                    <div class="bg-zinc-50 border border-zinc-100 rounded-2xl p-6 sticky top-32 space-y-6 shadow-sm">
                        <h2 class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-400 border-b border-zinc-100 pb-3">Order Manifest</h2>
                        
                        <div class="divide-y divide-zinc-100 max-h-[350px] overflow-y-auto no-scrollbar">
                            <div v-for="item in props.cart" :key="item.id" class="py-4 flex gap-4 items-center group">
                                <div class="w-14 h-18 bg-white border border-zinc-100 rounded-lg overflow-hidden shrink-0">
                                    <img :src="item.image?.startsWith('http') ? item.image : '/storage/' + item.image" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                </div>
                                <div class="flex-1 text-left">
                                    <h4 class="text-[10px] font-black uppercase tracking-tight">{{ item.name }}</h4>
                                    <span class="block text-[7px] text-zinc-400 uppercase font-bold mt-1">{{ item.quantity }} units // size {{ item.size }}</span>
                                </div>
                                <span class="text-[10px] font-black italic">{{ formatCurrency(item.price * item.quantity) }}</span>
                            </div>
                        </div>

                        <div class="pt-5 border-t border-zinc-200 space-y-4">
                            <div class="flex justify-between text-[8px] font-black uppercase tracking-widest text-zinc-400">
                                <span>Sub-Valuation</span>
                                <span>{{ formatCurrency(subtotal) }}</span>
                            </div>
                            <div v-if="activeVoucher" class="flex justify-between text-[8px] font-black uppercase tracking-widest text-[#10B981]">
                                <span>Protocol Discount</span>
                                <span>- {{ formatCurrency(discountAmount) }}</span>
                            </div>
                            <div class="flex justify-between items-end pt-5 border-t border-dashed border-zinc-300">
                                <div class="flex flex-col">
                                    <span class="text-[7px] uppercase tracking-[0.2em] font-black text-zinc-400 mb-1">Total Settlement</span>
                                    <span class="text-4xl font-black italic tracking-tighter text-[#10B981] font-['Plus_Jakarta_Sans'] leading-none">
                                        {{ formatCurrency(finalTotal) }}
                                    </span>
                                </div>
                            </div>
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
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
input, select { -webkit-tap-highlight-color: transparent; }
</style>