<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ 
    vouchers: { type: Array, default: () => [] } 
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const showDeleteModal = ref(false);
const searchQuery = ref('');

const form = useForm({
    code: '',
    type: 'fixed',
    value: '',
    usage_limit: 1,
    expires_at: null 
});

const filteredVouchers = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.vouchers;
    return props.vouchers.filter(v => v.code.toLowerCase().includes(query));
});

const isExpired = (date) => date && new Date(date) < new Date();
const isUsedUp = (v) => v.used_count >= v.usage_limit;

const stats = computed(() => {
    return {
        total: props.vouchers.length,
        active: props.vouchers.filter(v => !isExpired(v.expires_at) && !isUsedUp(v)).length,
        used: props.vouchers.reduce((acc, v) => acc + (v.used_count || 0), 0)
    };
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    showModal.value = true;
};

const openEditModal = (voucher) => {
    isEditing.value = true;
    editingId.value = voucher.id;
    form.code = voucher.code;
    form.type = voucher.type;
    form.value = voucher.value;
    form.usage_limit = voucher.usage_limit;
    form.expires_at = voucher.expires_at ? voucher.expires_at.split('T')[0] : null;
    showModal.value = true;
};

const submit = () => {
    const requestRoute = isEditing.value ? route('admin.vouchers.update', editingId.value) : route('admin.vouchers.store');
    const method = isEditing.value ? 'put' : 'post';
    
    form.submit(method, requestRoute, {
        preserveScroll: true,
        onSuccess: () => { 
            showModal.value = false;
            form.reset();
        },
    });
};

const executeDelete = () => {
    router.delete(route('admin.vouchers.destroy', editingId.value), { 
        preserveScroll: true,
        onSuccess: () => { 
            showDeleteModal.value = false; 
            showModal.value = false;
        }
    });
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val);
</script>

<template>
    <Head title="Vouchers // Promos" />

    <AdminLayout>
        <div class="max-w-[1400px] mx-auto space-y-6 md:space-y-8 font-['Inter'] py-4 md:py-6 px-2 md:px-0 text-left font-black">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                <div v-for="stat in [
                    { label: 'Total Registry', value: stats.total, color: 'text-zinc-900', bg: 'bg-white' },
                    { label: 'Active Codes', value: stats.active, color: 'text-emerald-500', bg: 'bg-white' },
                    { label: 'Redemptions', value: stats.used, color: 'text-white', bg: 'bg-zinc-900' }
                ]" :key="stat.label" :class="[stat.bg, 'p-6 rounded-2xl border border-zinc-200 shadow-sm flex flex-col space-y-1 transition-all hover:border-zinc-400']">
                    <span class="text-[8px] md:text-[9px] uppercase tracking-[0.3em] text-zinc-400 font-black italic">{{ stat.label }}</span>
                    <span :class="[stat.color, 'text-2xl md:text-3xl font-black font-mono tracking-tighter']">{{ String(stat.value).padStart(3, '0') }}</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-xl md:rounded-2xl border border-zinc-200 shadow-sm gap-6">
                <div class="space-y-1 text-center md:text-left w-full md:w-auto">
                    <span class="text-[9px] md:text-[10px] text-zinc-400 uppercase tracking-[0.4em] font-black italic">Promotional Logic // Distribution</span>
                    <h2 class="text-2xl md:text-3xl font-black tracking-tighter text-zinc-900 uppercase font-['Plus_Jakarta_Sans']">Vouchers</h2>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full sm:w-64 group">
                        <input v-model="searchQuery" type="text" placeholder="Scan by code..." 
                            class="w-full bg-zinc-50 border border-zinc-100 rounded-xl px-10 py-3 text-[10px] md:text-[11px] font-black uppercase outline-none focus:ring-2 focus:ring-zinc-900 focus:bg-white transition-all h-[48px] md:h-[50px]">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <button @click="openCreateModal" class="w-full sm:w-auto bg-zinc-900 text-white px-8 py-3.5 text-[10px] md:text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-black active:scale-95 transition-all shadow-xl h-[48px] md:h-[50px]">
                        + New Code
                    </button>
                </div>
            </div>

            <div class="hidden md:block bg-white border border-zinc-200 rounded-[2rem] overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse font-black">
                    <thead>
                        <tr class="bg-zinc-50/50 border-b border-zinc-100 text-[10px] uppercase tracking-widest text-zinc-400 font-black italic">
                            <th class="px-8 py-5">Code & Protocol</th>
                            <th class="px-8 py-5 text-center">Value</th>
                            <th class="px-8 py-5 text-center">Expiry</th>
                            <th class="px-8 py-5 text-center">Usage Progress</th>
                            <th class="px-8 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="filteredVouchers.length > 0" class="divide-y divide-zinc-50 text-[12px] text-zinc-900 font-black">
                        <tr v-for="v in filteredVouchers" :key="v.id" class="hover:bg-zinc-50/50 transition-all group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-black text-sm uppercase group-hover:italic transition-all underline decoration-zinc-100 underline-offset-4">#{{ v.code }}</span>
                                    <span v-if="isUsedUp(v)" class="px-2 py-0.5 rounded bg-zinc-100 text-zinc-400 text-[8px] uppercase font-black">Sold Out</span>
                                    <span v-else-if="isExpired(v.expires_at)" class="px-2 py-0.5 rounded bg-red-50 text-red-400 text-[8px] uppercase font-black">Expired</span>
                                    <span v-else class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-500 text-[8px] uppercase font-black animate-pulse">Active</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center uppercase font-mono italic">{{ v.type === 'fixed' ? formatCurrency(v.value) : v.value + '%' }} OFF</td>
                            <td class="px-8 py-5 text-center">
                                <span :class="isExpired(v.expires_at) ? 'text-red-400' : 'text-zinc-400'" class="text-[10px] uppercase font-black italic">
                                    {{ v.expires_at ? new Date(v.expires_at).toLocaleDateString() : 'Permanent' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 min-w-[180px]">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-[8px] uppercase font-black tracking-widest italic">
                                        <span class="text-zinc-900">{{ v.used_count || 0 }} Used</span>
                                        <span class="text-zinc-400">Cap: {{ v.usage_limit }}</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-1.5 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-zinc-900 transition-all duration-700" :style="`width: ${Math.min((v.used_count / v.usage_limit) * 100, 100)}%` "></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right font-black">
                                <button @click="openEditModal(v)" class="bg-zinc-50 text-zinc-900 px-5 py-2.5 rounded-xl text-[10px] uppercase tracking-widest hover:bg-zinc-900 hover:text-white transition-all border border-zinc-200">
                                    Configure
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="filteredVouchers.length === 0" class="py-24 text-center bg-zinc-50/50">
                    <p class="text-zinc-400 text-[10px] uppercase tracking-[0.4em] font-black italic">No matching protocol codes</p>
                </div>
            </div>

            <div class="md:hidden space-y-3">
                <div v-for="v in filteredVouchers" :key="v.id" @click="openEditModal(v)" class="bg-white border border-zinc-200 p-5 rounded-2xl shadow-sm space-y-5 active:scale-[0.98] transition-all">
                    <div class="flex justify-between items-center">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-mono font-black uppercase tracking-widest">#{{ v.code }}</span>
                            <p class="text-[8px] text-zinc-400 uppercase font-black">{{ v.type === 'fixed' ? formatCurrency(v.value) : v.value + '%' }} DISCOUNT</p>
                        </div>
                        <span v-if="isUsedUp(v)" class="text-[8px] font-black text-zinc-300 uppercase">SOLD_OUT</span>
                        <span v-else-if="isExpired(v.expires_at)" class="text-[8px] font-black text-red-400 uppercase">EXPIRED</span>
                        <span v-else class="text-[8px] font-black text-emerald-500 uppercase">ACTIVE</span>
                    </div>
                    <div class="w-full bg-zinc-100 h-1 rounded-full overflow-hidden">
                        <div class="h-full bg-zinc-900" :style="`width: ${Math.min((v.used_count / v.usage_limit) * 100, 100)}%` "></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[200] flex items-end md:items-center justify-center bg-zinc-900/90 backdrop-blur-md p-0 md:p-6 transition-all">
            <div class="bg-white w-full max-w-lg md:rounded-2xl shadow-2xl overflow-hidden border border-zinc-200 flex flex-col h-[85vh] md:h-auto">
                
                <div class="px-6 md:px-8 py-5 md:py-6 border-b border-zinc-100 flex justify-between items-center bg-zinc-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-zinc-900 rounded-full"></div>
                        <h2 class="text-lg md:text-xl font-black uppercase font-['Plus_Jakarta_Sans'] italic tracking-tighter">
                            {{ isEditing ? 'Edit Protocol' : 'Code Registration' }}
                        </h2>
                    </div>
                    <button @click="showModal = false" class="bg-white p-2 rounded-lg border border-zinc-200 hover:text-red-500 transition-all">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-6 md:p-8 space-y-6 overflow-y-auto">
                    <div class="space-y-2">
                        <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest italic">Voucher Code</label>
                        <input v-model="form.code" type="text" class="w-full bg-zinc-50 border-none rounded-xl px-5 py-4 text-sm font-black uppercase focus:ring-2 focus:ring-zinc-900 transition-all h-[52px]">
                        <p v-if="form.errors.code" class="text-red-500 text-[10px] italic">{{ form.errors.code }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest italic">Type</label>
                            <div class="relative">
                                <select v-model="form.type" class="w-full bg-zinc-50 border-none rounded-xl px-5 py-4 text-[11px] font-black uppercase h-[52px] appearance-none focus:ring-2 focus:ring-zinc-900 outline-none">
                                    <option value="fixed">Fixed Value (₱)</option>
                                    <option value="percentage">Percentage (%)</option>
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest italic">Value</label>
                            <input v-model="form.value" type="number" class="w-full bg-zinc-50 border-none rounded-xl px-5 py-4 text-sm font-black h-[52px] focus:ring-2 focus:ring-zinc-900 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest italic">Usage Limit</label>
                            <input v-model="form.usage_limit" type="number" class="w-full bg-zinc-50 border-none rounded-xl px-5 py-4 text-sm font-black h-[52px] focus:ring-2 focus:ring-zinc-900 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest italic">Expiry Node</label>
                            <input v-model="form.expires_at" type="date" class="w-full bg-zinc-50 border-none rounded-xl px-5 py-4 text-[11px] font-black uppercase h-[52px] focus:ring-2 focus:ring-zinc-900 outline-none">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-8 border-t border-zinc-50">
                        <button v-if="isEditing" type="button" @click="showDeleteModal = true" class="text-red-500 text-[10px] font-black uppercase tracking-widest hover:underline italic">Eject Code</button>
                        <div v-else></div>
                        <button type="submit" :disabled="form.processing" class="bg-zinc-900 text-white px-10 py-4 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-black transition-all active:scale-95 shadow-xl">
                            {{ isEditing ? 'Commit Changes' : 'Execute Register' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-[300] flex items-center justify-center bg-zinc-900/80 backdrop-blur-lg p-4">
            <div class="bg-white w-full max-w-xs rounded-2xl shadow-2xl p-8 text-center space-y-6 border border-zinc-100">
                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-lg font-black uppercase font-['Plus_Jakarta_Sans'] italic tracking-tighter">Eject Protocol?</h2>
                    <p class="text-[10px] text-zinc-400 font-black uppercase italic tracking-widest">This cannot be reversed.</p>
                </div>
                <div class="flex flex-col gap-2">
                    <button @click="executeDelete" class="w-full py-4 bg-red-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all">Confirm Eject</button>
                    <button @click="showDeleteModal = false" class="w-full py-4 bg-zinc-100 text-zinc-500 rounded-xl text-[10px] font-black uppercase tracking-widest">Return</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Industry Standard Customizations */
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-thumb { background: #111827; border-radius: 10px; }
.transition-all { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

/* Remove default date/select styles for a cleaner look */
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(0.5);
}
</style>