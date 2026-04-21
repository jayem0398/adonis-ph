<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};
</script>

<template>
    <Head title="Studio Dashboard | ADONIS" />

    <AuthenticatedLayout>
        <div class="max-w-[1600px] mx-auto px-6 md:px-12 py-12 md:py-24 space-y-20 bg-white min-h-screen font-black antialiased">
            
            <div class="border-b border-zinc-100 pb-12">
                <h2 class="text-[9px] font-bold text-zinc-300 uppercase tracking-[0.6em] mb-4">Operations Control</h2>
                <h1 class="text-5xl md:text-7xl font-extralight tracking-tighter text-black italic uppercase">Archive <span class="text-zinc-200">Insights</span></h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-10 border border-zinc-100 bg-white hover:border-black transition-all duration-500 group">
                    <p class="text-[8px] font-bold uppercase tracking-[0.4em] text-zinc-400 mb-6 group-hover:text-zinc-900 transition-colors">Total Revenue</p>
                    <p class="text-3xl font-light tracking-tighter text-black">{{ formatCurrency(stats.total_sales || 0) }}</p>
                    <div class="h-[1px] w-0 group-hover:w-full bg-black mt-4 transition-all duration-700"></div>
                </div>

                <div class="p-10 border border-zinc-100 bg-white hover:border-black transition-all duration-500 group">
                    <p class="text-[8px] font-bold uppercase tracking-[0.4em] text-zinc-400 mb-6 group-hover:text-zinc-900 transition-colors">Pending Fulfillment</p>
                    <p class="text-3xl font-light tracking-tighter text-black">{{ stats.pending_orders || 0 }}</p>
                    <div class="h-[1px] w-0 group-hover:w-full bg-[#10B981] mt-4 transition-all duration-700"></div>
                </div>

                <div class="p-10 border border-zinc-100 bg-white hover:border-black transition-all duration-500 group">
                    <p class="text-[8px] font-bold uppercase tracking-[0.4em] text-zinc-400 mb-6 group-hover:text-zinc-900 transition-colors">Archival Stock</p>
                    <p class="text-3xl font-light tracking-tighter text-black">
                        {{ stats.total_stock || 0 }} <span class="text-[10px] text-zinc-300 italic uppercase ml-2">Units</span>
                    </p>
                    <div class="h-[1px] w-0 group-hover:w-full bg-black mt-4 transition-all duration-700"></div>
                </div>

                <div class="p-10 border border-zinc-100 bg-white hover:border-black transition-all duration-500 group">
                    <p class="text-[8px] font-bold uppercase tracking-[0.4em] text-zinc-400 mb-6 group-hover:text-zinc-900 transition-colors">Low Stock Alerts</p>
                    <p :class="(stats.low_stock_count || 0) > 0 ? 'text-red-500' : 'text-black'" class="text-3xl font-light tracking-tighter transition-colors">
                        {{ stats.low_stock_count || 0 }}
                    </p>
                    <div class="h-[1px] w-0 group-hover:w-full bg-red-500 mt-4 transition-all duration-700"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
                <div class="space-y-8">
                    <div class="flex justify-between items-end border-b border-zinc-900 pb-6">
                        <h3 class="text-[10px] font-bold uppercase tracking-[0.5em] text-black italic">Recent Acquisitions</h3>
                        <Link :href="route('admin.orders.index')" class="text-[8px] font-bold uppercase tracking-[0.3em] text-zinc-400 hover:text-black transition-colors">View All Records</Link>
                    </div>
                    <div class="space-y-1">
                        <div v-for="order in stats.recent_orders" :key="order.id" class="flex justify-between items-center py-5 border-b border-zinc-50 group hover:px-2 transition-all">
                            <span class="text-[10px] font-medium uppercase tracking-widest text-zinc-500 group-hover:text-black transition-colors">
                                #{{ String(order.id).padStart(5, '0') }} — {{ order.first_name }} {{ order.last_name }}
                            </span>
                            <span class="text-[10px] font-light italic text-black">{{ formatCurrency(order.total_amount) }}</span>
                        </div>
                        <div v-if="!stats.recent_orders?.length" class="py-10 text-center">
                            <span class="text-[8px] uppercase tracking-[1em] text-zinc-200 italic">No Active Logs</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="flex justify-between items-end border-b border-zinc-900 pb-6">
                        <h3 class="text-[10px] font-bold uppercase tracking-[0.5em] text-black italic">Inventory Warning</h3>
                        <Link :href="route('admin.products.index')" class="text-[8px] font-bold uppercase tracking-[0.3em] text-zinc-400 hover:text-black transition-colors">Manage Stock</Link>
                    </div>
                    <div class="space-y-1">
                        <div v-for="variant in stats.low_stock_items" :key="variant.id" class="flex justify-between items-center py-5 border-b border-zinc-50 group hover:px-2 transition-all">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-medium uppercase tracking-widest text-zinc-900">{{ variant.product?.name }}</span>
                                <span class="text-[7px] text-zinc-400 uppercase tracking-[0.4em] mt-1">Configuration: {{ variant.size }}</span>
                            </div>
                            <span class="text-[9px] font-black text-red-500 bg-red-50 px-3 py-1 uppercase tracking-tighter">{{ variant.stock }} Units Left</span>
                        </div>
                        <div v-if="!stats.low_stock_items?.length" class="py-10 text-center bg-zinc-50 border border-dashed border-zinc-100">
                            <span class="text-[8px] uppercase tracking-[1em] text-zinc-300 italic">Inventory Optimized</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-12 border-t border-zinc-100 flex justify-between items-center">
                <span class="text-[8px] text-zinc-300 uppercase tracking-[0.8em]">Adonis Studio OS // v.2026</span>
                <div class="flex gap-8">
                     <span class="text-[8px] text-zinc-400 uppercase tracking-[0.4em] italic font-medium">Terminal Secured</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Focus transition for better UX */
.transition-all {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>