<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ orders: Array });

const form = useForm({
    status: '',
    tracking_number: ''
});

const updateOrder = (order) => {
    form.put(route('admin.orders.update-status', order.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Selyadong feedback logic
        }
    });
};

const getStatusClass = (status) => {
    const map = {
        pending: 'bg-zinc-100 text-zinc-500',
        processing: 'bg-blue-50 text-blue-600',
        shipped: 'bg-emerald-50 text-emerald-600',
        delivered: 'bg-black text-white',
        cancelled: 'bg-red-50 text-red-600'
    };
    return map[status] || 'bg-zinc-100';
};
</script>

<template>
    <Head title="Fulfillment Control" />
    <AdminLayout>
        <div class="max-w-[1400px] mx-auto p-6 font-black text-left">
            <header class="mb-10">
                <h2 class="text-3xl font-black uppercase italic tracking-tighter">Order_Fulfillment</h2>
                <p class="text-[10px] text-zinc-400 uppercase tracking-[0.4em]">Logistics Management System</p>
            </header>

            <div class="space-y-4">
                <div v-for="order in orders" :key="order.id" class="bg-white border border-zinc-100 p-6 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 shadow-sm hover:shadow-md transition-all">
                    <div class="space-y-1">
                        <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Order Ref: #{{ String(order.id).padStart(5, '0') }}</span>
                        <h3 class="text-sm font-black uppercase">{{ order.user.name }}</h3>
                        <p class="text-[9px] text-zinc-500 uppercase">{{ order.items.length }} Items — ₱{{ parseFloat(order.total).toLocaleString() }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                        <div class="flex flex-col gap-1.5">
                             <label class="text-[7px] uppercase font-black text-zinc-400">Tracking_ID</label>
                             <input v-model="order.tracking_number" type="text" placeholder="NOT_ASSIGNED" class="bg-zinc-50 border-none rounded-lg py-2 px-3 text-[10px] font-black uppercase w-40">
                        </div>

                        <div class="flex flex-col gap-1.5">
                             <label class="text-[7px] uppercase font-black text-zinc-400">Current_Status</label>
                             <select v-model="order.fulfillment_status" @change="updateOrder(order)" :class="getStatusClass(order.fulfillment_status)" class="border-none rounded-lg py-2 px-4 text-[9px] font-black uppercase appearance-none cursor-pointer min-w-[120px]">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                             </select>
                        </div>

                        <button @click="updateOrder(order)" class="mt-4 md:mt-0 bg-black text-white px-6 py-2.5 rounded-xl text-[8px] font-black uppercase tracking-widest hover:bg-[#10B981] transition-all">
                            Sync_Fulfillment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>