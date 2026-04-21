<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    product: Object
});

const emit = defineEmits(['edit', 'delete']);

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { 
    style: 'currency', 
    currency: 'PHP',
    maximumFractionDigits: 0 
}).format(val);

const displayImage = computed(() => {
    if (!props.product.image_path) return '/images/unit-placeholder.jpg';
    return props.product.image_path.startsWith('http') 
        ? props.product.image_path 
        : '/storage/' + props.product.image_path;
});

const totalStock = computed(() => {
    return props.product.total_stock ?? props.product.total_qty ?? 
           (props.product.variants?.reduce((acc, v) => acc + (parseInt(v.stock) || 0), 0) || 0);
});

const currentPrice = computed(() => {
    if (props.product.variants?.length > 0) {
        const prices = props.product.variants.map(v => parseFloat(v.discount_price || v.price));
        return Math.min(...prices);
    }
    return props.product.min_price || 0;
});

const handleEdit = () => {
    emit('edit', JSON.parse(JSON.stringify(props.product)));
};

const toggleFeatured = () => {
    router.patch(route('admin.products.toggle_featured', props.product.id), {}, { preserveScroll: true });
};

const handleDelete = () => {
    emit('delete', props.product.id);
};
</script>

<template>
    <div class="group/card relative flex flex-col h-full bg-white transition-all duration-500 cursor-pointer border border-zinc-100 hover:border-zinc-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl font-black font-['Inter']">
        
        <div class="relative aspect-square overflow-hidden bg-zinc-50 border-b border-zinc-100 font-black">
            <img :src="displayImage" 
                 class="w-full h-full object-cover transition-all duration-700 md:grayscale md:group-hover/card:grayscale-0 group-hover/card:scale-105" />
            
            <div class="absolute inset-x-0 bottom-0 p-3 translate-y-0 md:translate-y-full md:group-hover/card:translate-y-0 transition-transform duration-500 bg-white/95 backdrop-blur-md flex gap-2 z-40 border-t border-zinc-100 font-black">
                <button @click.stop="handleEdit" 
                        class="flex-1 py-3 bg-zinc-900 text-white text-[9px] font-black uppercase tracking-[0.4em] rounded-xl hover:bg-[#10B981] active:scale-95 transition-all shadow-lg">
                    Configure
                </button>
                <button @click.stop="handleDelete" 
                        class="px-4 py-3 border border-zinc-200 text-zinc-400 hover:text-red-600 hover:border-red-200 active:bg-red-50 transition-all rounded-xl">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
            </div>

            <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-20 text-left">
                <span v-if="product.variants?.some(v => v.discount_percentage > 0)" 
                      class="text-[8px] font-black px-2 py-1 bg-zinc-900 text-white rounded-md tracking-[0.2em] uppercase shadow-lg">
                    SALE
                </span>
                <button @click.stop="toggleFeatured" 
                        class="p-2 bg-white/90 backdrop-blur-md rounded-full shadow-sm transition-all active:scale-125 border border-zinc-100"
                        :class="[ product.is_featured ? 'text-red-500' : 'text-zinc-300 md:group-hover/card:text-red-200 hover:!text-red-500']">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
                </button>
            </div>

            <div class="absolute top-3 right-3 text-[9px] font-black px-2 py-1 bg-white border border-zinc-100 rounded-lg text-zinc-900 shadow-sm font-mono tracking-tighter">
                UNIT_STK: {{ totalStock }}
            </div>
        </div>

        <div class="p-5 flex flex-col gap-2 font-black text-left">
            <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-[0.4em] text-zinc-400">
                <span>{{ product.category }}</span>
                <span class="font-mono opacity-50">#{{ String(product.id).padStart(4, '0') }}</span>
            </div>

            <h3 class="text-[11px] font-black leading-tight uppercase truncate text-zinc-900 group-hover/card:text-[#10B981] transition-colors">
                {{ product.name }}
            </h3>

            <div class="flex items-center justify-between pt-4 border-t border-zinc-50 mt-1">
                <div class="flex flex-col text-left">
                    <div class="h-3 overflow-hidden">
                        <span v-if="product.variants?.some(v => v.discount_percentage > 0)" 
                              class="text-[9px] line-through text-zinc-300 font-black italic">
                            {{ formatCurrency(product.variants[0].price) }}
                        </span>
                    </div>
                    <span class="text-[14px] font-black text-zinc-900 tracking-tighter font-mono">
                        {{ formatCurrency(currentPrice) }}
                    </span>
                </div>

                <div class="flex items-center gap-1.5">
                    <div v-if="totalStock <= 5 && totalStock > 0" 
                         class="flex items-center gap-1.5 bg-red-50 px-2 py-1 rounded-md border border-red-100 shadow-sm">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-red-500"></span>
                        </span>
                        <span class="text-[8px] font-black text-red-600 uppercase tracking-tighter italic">Low_Stock</span>
                    </div>
                    <div v-else-if="totalStock === 0" 
                         class="flex items-center gap-1.5 bg-zinc-100 px-2 py-1 rounded-md border border-zinc-200">
                        <span class="text-[8px] font-black text-zinc-400 uppercase tracking-tighter italic leading-none">Depleted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Selyado: Mobile optimization for non-hover states */
@media (max-width: 768px) {
    .md\:grayscale { filter: none !important; }
    .md\:translate-y-full { transform: translateY(0) !important; }
    .bg-white\/95 { background-color: rgba(255, 255, 255, 0.98) !important; }
}
</style>