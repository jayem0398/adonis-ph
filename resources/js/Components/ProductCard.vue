<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ product: Object, isAdmin: { type: Boolean, default: false } });

const isFullySoldOut = computed(() => {
    return props.product.variants && props.product.variants.every(v => v.stock <= 0);
});

const form = useForm({
    variant_id: props.product.variants?.find(v => v.stock > 0)?.id || (props.product.variants?.[0]?.id || null),
    quantity: 1
});

const addToCart = () => {
    if (isFullySoldOut.value || form.processing) return;
    form.post(route('cart.add'), { preserveScroll: true });
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0 }).format(val);

const getPriceRange = (product) => {
    if (!product.variants || product.variants.length === 0) return 0;
    const prices = product.variants.map(v => parseFloat(v.price));
    return Math.min(...prices);
};

const resolveImage = (path) => {
    if (!path) return 'https://placehold.co/400x500/18181b/ffffff?text=ADONIS';
    return path.startsWith('http') ? path : '/storage/' + path;
};
</script>

<template>
    <div :class="[isFullySoldOut ? 'opacity-90' : '']" 
         class="group/card relative bg-white flex flex-col transition-all duration-500 h-full overflow-hidden border border-zinc-100 rounded-2xl hover:shadow-[20px_20px_60px_-15px_rgba(0,0,0,0.1)] hover:border-zinc-300 font-black font-['Inter'] shadow-sm hover:shadow-lg hover:border-zinc-200">
        
        <div class="relative aspect-[3/4] overflow-hidden bg-zinc-50 shrink-0 font-black">
            <div v-if="isFullySoldOut" class="absolute inset-0 z-30 flex items-center justify-center bg-zinc-900/40 backdrop-blur-[1px] pointer-events-none font-black">
                <div class="border-2 border-white px-4 py-1.5 transform -rotate-12 bg-zinc-900/20 font-black">
                    <span class="text-white text-[10px] tracking-[0.4em] uppercase font-black">Sold Out</span>
                </div>
            </div>

            <Link :href="route('products.show', product.id)" class="block h-full w-full font-black">
                <img :src="resolveImage(product.image_path)" 
                     :class="[isFullySoldOut ? 'grayscale brightness-75' : 'grayscale group-hover/card:grayscale-0 group-hover/card:scale-110 transition-all duration-1000 ease-out']"
                     class="w-full h-full object-cover">
            </Link>

            <div v-if="!isAdmin && !isFullySoldOut" 
                 class="hidden md:block absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover/card:translate-y-0 transition-all duration-500 bg-white/95 backdrop-blur-xl z-10 border-t border-zinc-100 font-black">
                <div class="space-y-3 font-black">
                    <div class="flex flex-col gap-1 font-black">
                        <span class="text-[7px] uppercase tracking-[0.3em] text-zinc-400 font-black">Size</span>
                        <select v-model="form.variant_id" class="w-full bg-zinc-100 border-none rounded-lg text-[9px] font-black uppercase py-2 px-3 outline-none focus:ring-1 focus:ring-zinc-900 transition-all cursor-pointer font-black">
                            <option v-for="v in product.variants" :key="v.id" :value="v.id" :disabled="v.stock <= 0">
                                {{ v.size }} // {{ formatCurrency(v.price) }}
                            </option>
                        </select>
                    </div>
                    <button @click.stop="addToCart" :disabled="form.processing" 
                            class="w-full bg-zinc-900 text-white text-[9px] font-black uppercase tracking-[0.4em] py-3.5 rounded-xl hover:bg-[#10B981] transition-all active:scale-95 shadow-lg font-black">
                        Add to Bag
                    </button>
                </div>
            </div>
        </div>
        
        <div class="p-5 flex flex-col flex-grow bg-white relative z-20 font-black text-left">
            <div class="space-y-1.5 mb-6 font-black">
                <div class="flex items-center justify-between font-black">
                    <p class="text-[7px] text-zinc-400 uppercase tracking-[0.4em] font-black">{{ product.category }}</p>
                    <span v-if="!isFullySoldOut" class="w-1.5 h-1.5 rounded-full bg-[#10B981] animate-pulse font-black"></span>
                </div>
                <h3 class="text-[11px] font-black uppercase tracking-tight leading-tight line-clamp-2 transition-colors group-hover/card:text-[#10B981] font-black">
                    {{ product.name }}
                </h3>
            </div>
            
            <div class="mt-auto pt-4 border-t border-zinc-50 flex justify-between items-end font-black">
                <div class="flex flex-col font-black">
                    <span class="text-[7px] uppercase text-zinc-300 tracking-widest font-bold mb-0.5 font-black">Valuation</span>
                    <p class="text-[12px] md:text-[14px] font-black tracking-tighter text-zinc-900 leading-none font-black">
                        {{ formatCurrency(getPriceRange(product)) }}
                    </p>
                </div>
                <div class="flex flex-col items-end opacity-20 group-hover/card:opacity-100 transition-opacity font-black">
                    <span class="text-[6px] md:text-[7px] uppercase font-bold text-zinc-400 font-mono tracking-tighter font-black">Ref Code</span>
                    <span class="text-[8px] font-black text-zinc-900 font-mono font-black">#{{ String(product.id).padStart(4, '0') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>