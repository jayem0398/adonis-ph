<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import StudioNotification from '@/Components/StudioNotification.vue';

const props = defineProps({ orders: Array });

const searchQuery = ref('');
const selectedOrder = ref(null);
const showModal = ref(false);

// Standard Search Protocol (IBINALIK SA ORIGINAL)
const filteredOrders = computed(() => {
    return props.orders.filter(order => {
        const search = searchQuery.value.toLowerCase().trim();
        const fullName = `${order.first_name} ${order.last_name}`.toLowerCase();
        const refId = `#${String(order.id).padStart(6, '0')}`.toLowerCase();
        return fullName.includes(search) || refId.includes(search) || order.email?.toLowerCase().includes(search);
    });
});

const updateStatus = (id, status) => {
    router.patch(route('admin.orders.update', id), { status }, { 
        preserveScroll: true,
        onSuccess: () => { if (selectedOrder.value) selectedOrder.value.status = status; }
    });
};

const openManifest = (order) => {
    selectedOrder.value = order;
    showModal.value = true;
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 }).format(val);

const cleanLabel = (t) => t ? t.replace(/_/g, ' ').toUpperCase() : '';

const getStatusConfig = (s) => {
    switch (s?.toLowerCase()) {
        case 'pending': return 'bg-orange-50 text-orange-600 border-orange-100';
        case 'packed': return 'bg-purple-50 text-purple-600 border-purple-100';
        case 'shipped': return 'bg-blue-50 text-blue-600 border-blue-100';
        case 'delivered': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
        case 'cancelled': return 'bg-red-50 text-red-600 border-red-100';
        case 'return_requested': return 'bg-rose-50 text-rose-600 border-rose-100 animate-pulse';
        default: return 'bg-zinc-50 text-zinc-600 border-zinc-100';
    }
};
</script>

<template>
    <Head title="Orders // Registry" />
    <AdminLayout>
        <StudioNotification class="z-[500]" />

        <div class="max-w-[1400px] mx-auto space-y-6 md:space-y-8 font-['Inter'] py-4 md:py-6 px-2 md:px-0 text-left font-black tracking-tighter italic">
            
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-xl md:rounded-2xl border border-zinc-200 shadow-sm gap-6">
                <div class="space-y-1 w-full md:w-auto">
                    <span class="text-[9px] md:text-[10px] text-zinc-400 uppercase tracking-[0.4em] font-black italic">Logistics Archive // Tracking</span>
                    <h2 class="text-2xl md:text-3xl font-black text-zinc-900 uppercase leading-none italic">Order Manifest</h2>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full sm:w-64">
                        <input v-model="searchQuery" type="text" placeholder="SCAN_ENTITY..." class="w-full bg-zinc-50 border border-zinc-100 rounded-xl px-10 py-3 text-[10px] md:text-[11px] font-black uppercase outline-none focus:ring-2 focus:ring-zinc-900 transition-all h-[48px] md:h-[50px]">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <div class="px-6 py-3 bg-zinc-900 rounded-xl text-right shadow-xl h-[48px] md:h-[50px] flex flex-col justify-center min-w-[120px]">
                        <p class="text-[8px] uppercase tracking-widest text-zinc-500 font-black leading-none italic">Total Count</p>
                        <p class="text-xl font-black text-white font-mono leading-none mt-1">{{ String(orders.length).padStart(3, '0') }}</p>
                    </div>
                </div>
            </div>

            <div class="hidden md:block bg-white border border-zinc-200 rounded-[2rem] overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/50 border-b border-zinc-100 text-[10px] uppercase tracking-widest text-zinc-400 font-black italic">
                            <th class="px-8 py-5">Ref ID</th>
                            <th class="px-8 py-5">Entity Identity</th>
                            <th class="px-8 py-5 text-center">Status Protocol</th>
                            <th class="px-8 py-5 text-right">Settlement</th>
                            <th class="px-8 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 text-[12px] font-black italic">
                        <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-zinc-50/50 transition-all group">
                            <td class="px-8 py-5 font-mono text-zinc-400 group-hover:text-zinc-900">#{{ String(order.id).padStart(6, '0') }}</td>
                            <td class="px-8 py-5">
                                <p class="text-zinc-900 uppercase tracking-tight">{{ order.first_name }} {{ order.last_name }}</p>
                                <p class="text-[9px] text-zinc-400 uppercase italic">{{ order.email }}</p>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span :class="getStatusConfig(order.status)" class="px-3 py-1 text-[8px] font-black uppercase tracking-widest border rounded-lg shadow-sm italic">
                                    {{ cleanLabel(order.status) }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right font-mono text-zinc-900">{{ formatCurrency(order.total_amount) }}</td>
                            <td class="px-8 py-5 text-right">
                                <button @click="openManifest(order)" class="bg-zinc-900 text-white px-6 py-2 rounded-xl text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-md italic">Review</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-zinc-900/90 backdrop-blur-sm p-4 transition-all">
            <div class="bg-white w-[98%] md:w-full md:max-w-4xl rounded-[2.5rem] shadow-2xl flex flex-col h-full max-h-[85vh] overflow-hidden border border-zinc-100 font-black tracking-tight italic">
                
                <div class="px-8 py-6 border-b border-zinc-100 flex justify-between items-center bg-white sticky top-0 z-10">
                    <div class="text-left font-black">
                        <p class="text-[9px] text-emerald-500 uppercase tracking-[0.4em] mb-1 italic">Terminal Verification</p>
                        <h2 class="text-xl md:text-2xl font-black uppercase tracking-tighter leading-none text-zinc-900">Record #{{ String(selectedOrder.id).padStart(6, '0') }}</h2>
                    </div>
                    <button @click="showModal = false" class="text-zinc-300 hover:text-red-500 transition-colors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-8 md:p-10 space-y-10 overflow-y-auto custom-scroll text-left">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                        <div class="md:col-span-5 space-y-8 font-black">
                            <div class="space-y-4">
                                <h3 class="text-[10px] uppercase text-zinc-300 tracking-[0.4em] italic border-b border-zinc-50 pb-2">Acquirer Node</h3>
                                <div class="space-y-1 italic">
                                    <p class="text-base font-black uppercase text-zinc-900 leading-tight italic">{{ selectedOrder.first_name }} {{ selectedOrder.last_name }}</p>
                                    <p class="text-[11px] text-zinc-400 uppercase tracking-widest font-black leading-none">{{ selectedOrder.email }}</p>
                                    <p class="text-[10px] text-zinc-500 uppercase mt-4 leading-relaxed font-black">
                                        {{ selectedOrder.address }}<br>
                                        {{ selectedOrder.city }}, {{ selectedOrder.state }} PH
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-[10px] uppercase text-zinc-300 tracking-[0.4em] italic border-b border-zinc-50 pb-2">Logistics Protocol</h3>
                                <div class="relative w-full sm:w-64">
                                    <select @change="updateStatus(selectedOrder.id, $event.target.value)" class="w-full bg-zinc-900 text-white text-[10px] font-black uppercase tracking-[0.2em] py-3.5 px-6 rounded-xl outline-none cursor-pointer h-[50px] appearance-none border-none shadow-xl italic">
                                        <optgroup label="CORE" class="bg-white text-zinc-900">
                                            <option v-for="s in ['pending', 'packed', 'shipped', 'delivered']" :key="s" :selected="selectedOrder.status === s" :value="s">{{ cleanLabel(s) }}</option>
                                        </optgroup>
                                        <optgroup label="INCIDENT" class="bg-white text-zinc-900">
                                            <option v-for="s in ['cancelled', 'return_requested', 'refunded']" :key="s" :selected="selectedOrder.status === s" :value="s">{{ cleanLabel(s) }}</option>
                                        </optgroup>
                                    </select>
                                    <svg class="absolute right-5 top-1/2 -translate-y-1/2 text-white pointer-events-none" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="5"><path d="m6 9 6 6 6-6"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-7 space-y-6">
                            <div class="flex justify-between items-end border-b border-zinc-100 pb-2 italic">
                                <h3 class="text-[10px] uppercase text-zinc-300 tracking-[0.4em] font-black">Allocation Manifest</h3>
                                <span class="text-[10px] font-black text-zinc-900 tracking-widest">{{ selectedOrder.items.length }} UNITS</span>
                            </div>
                            <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scroll italic font-black">
                                <div v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between items-center bg-zinc-50/50 p-4 rounded-2xl border border-zinc-100 transition-all hover:bg-zinc-50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-12 bg-white rounded-lg border border-zinc-200 overflow-hidden flex-shrink-0 flex items-center justify-center p-0.5">
                                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover rounded shadow-sm">
                                            <div v-else class="text-[7px] text-zinc-300 font-black italic uppercase leading-none">VOID</div>
                                        </div>
                                        <div class="space-y-0.5 text-left italic">
                                            <p class="text-[11px] font-black uppercase text-zinc-900 leading-tight tracking-tight">{{ item.product_name }}</p>
                                            <p class="text-[9px] text-emerald-600 font-black uppercase tracking-[0.1em] italic leading-none">SZ: {{ item.variant_name }} // QTY: {{ item.quantity }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs font-black font-mono text-zinc-900 italic tracking-tighter leading-none">{{ formatCurrency(item.price * item.quantity) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedOrder.return_reason || selectedOrder.cancel_reason" class="bg-rose-50/50 p-8 rounded-[2rem] border border-rose-100 italic font-black">
                        <div class="flex flex-col md:flex-row gap-8 items-start">
                            <div class="flex-1 space-y-4 text-left italic">
                                <h3 class="text-[10px] uppercase text-rose-500 tracking-[0.4em] border-b border-rose-200 pb-2 leading-none">Incident Report Log</h3>
                                <div class="space-y-2">
                                    <p class="text-sm font-black uppercase text-rose-600 tracking-tight leading-none italic">{{ cleanLabel(selectedOrder.return_reason || selectedOrder.cancel_reason) }}</p>
                                    <p class="text-[11px] text-zinc-500 uppercase italic leading-relaxed font-black">{{ selectedOrder.return_description || selectedOrder.cancel_description }}</p>
                                </div>
                            </div>
                            <div v-if="selectedOrder.return_proof_path" class="w-32 h-32 bg-white rounded-2xl overflow-hidden border-2 border-dashed border-rose-200 p-1 flex-shrink-0 flex items-center justify-center shadow-inner">
                                <img v-if="!selectedOrder.return_proof_path.endsWith('.mp4')" :src="'/storage/' + selectedOrder.return_proof_path" class="w-full h-full object-cover rounded-xl shadow-inner">
                                <video v-else :src="'/storage/' + selectedOrder.return_proof_path" class="w-full h-full object-cover rounded-xl" controls></video>
                            </div>
                        </div>

                        <div v-if="selectedOrder.status === 'return_requested'" class="grid grid-cols-2 gap-4 pt-8 border-t border-rose-100 font-black">
                            <button @click="router.post(route('admin.orders.approve-return', selectedOrder.id))" 
                                    class="bg-emerald-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-emerald-700 transition-all shadow-md italic">
                                Approve & Refund
                            </button>
                            <button @click="router.post(route('admin.orders.reject-return', selectedOrder.id))" 
                                    class="bg-white text-rose-600 border-2 border-rose-100 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-rose-100 transition-all italic">
                                Reject Request
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between items-end pt-10 border-t border-zinc-100 italic font-black">
                        <div class="space-y-1 text-left italic">
                            <p class="text-[10px] uppercase tracking-[0.5em] text-zinc-400 leading-none">Net Settlement</p>
                            <p class="text-4xl md:text-5xl font-black text-emerald-500 font-mono tracking-tighter leading-none italic">{{ formatCurrency(selectedOrder.total_amount) }}</p>
                        </div>
                        <button class="bg-zinc-100 text-zinc-500 px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-zinc-900 hover:text-white transition-all shadow-sm italic">Export_Manifest</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 3px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #111827; border-radius: 10px; }
.transition-all { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
</style>