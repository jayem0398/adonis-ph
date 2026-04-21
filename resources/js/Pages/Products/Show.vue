<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Optional: Use this if you want admin/user nav
import Navigation from '@/Components/Navigation.vue'; // Or your custom Nav component

const props = defineProps({
    product: Object,
    cartCount: Number
});

const isSoldOut = computed(() => props.product.stock <= 0);

const form = useForm({
    product_id: props.product.id,
    variant_id: props.product.variants && props.product.variants.length > 0 ? props.product.variants[0].id : null,
    quantity: 1
});

const selectedVariant = computed(() => {
    return props.product.variants.find(v => v.id === form.variant_id);
});

const addToCart = () => {
    if (isSoldOut.value) return;
    if (!form.variant_id) {
        alert('Archive Error: Select unit configuration.');
        return;
    }
    form.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success notification handled by StudioNotification
        }
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};
</script>

<template>
    <Head :title="`${product.name} | ADONIS ARCHIVE`" />

    <div class="bg-white min-h-screen selection:bg-[#10B981] selection:text-white font-black antialiased text-zinc-900">
        
        <nav class="fixed top-0 w-full z-[100] px-6 md:px-12 py-8 flex justify-between items-center mix-blend-difference text-white">
            <Link :href="route('archive.index')" class="text-[9px] uppercase tracking-[0.4em] flex items-center gap-4 group">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:-translate-x-2 transition-transform"><path d="m15 18-6-6 6-6"/></svg>
                Return_to_Archive
            </Link>
            <Link :href="route('cart.index')" class="text-[9px] uppercase tracking-[0.4em] relative">
                Bag_Acquisition [{{ cartCount }}]
            </Link>
        </nav>

        <main class="flex flex-col lg:flex-row min-h-screen">
            <section class="w-full lg:w-1/2 bg-[#FBFBFB] relative overflow-hidden h-[70vh] lg:h-screen">
                <div v-if="isSoldOut" class="absolute inset-0 z-20 bg-zinc-900/40 backdrop-blur-md flex items-center justify-center">
                    <span class="text-white text-4xl font-black uppercase italic tracking-[0.5em] border-4 border-white p-8">Sold Out</span>
                </div>
                <img 
                    :src="product.image_path.startsWith('http') ? product.image_path : '/storage/' + product.image_path" 
                    :class="isSoldOut ? 'grayscale brightness-50' : 'grayscale hover:grayscale-0'"
                    class="w-full h-full object-cover transition-all duration-1000"
                >
                <div class="absolute bottom-12 left-12 text-white mix-blend-difference z-10 hidden lg:block">
                    <span class="text-[8px] uppercase tracking-[1em] block mb-2">Visual_Ref_001</span>
                    <span class="text-xs uppercase tracking-widest italic font-bold">Studio documentation // Manila</span>
                </div>
            </section>

            <section class="w-full lg:w-1/2 px-6 md:px-20 py-20 lg:py-0 flex flex-col justify-center bg-white">
                <div class="max-w-xl space-y-12">
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] text-[#10B981] tracking-[0.6em] uppercase italic">{{ product.category }}</span>
                            <span class="h-[1px] w-8 bg-zinc-100"></span>
                            <span class="text-[10px] text-zinc-300 uppercase tracking-widest">REF:{{ String(product.id).padStart(5, '0') }}</span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black uppercase tracking-tighter italic leading-[0.9]">{{ product.name }}</h1>
                        <p class="text-xs text-zinc-400 uppercase tracking-widest leading-relaxed italic max-w-sm">
                            {{ product.description || 'Engineered for high-compression architecture. Documenting identity through form and function.' }}
                        </p>
                    </div>

                    <div class="space-y-8 pt-8 border-t border-zinc-100">
                        <div class="flex justify-between items-end">
                            <span class="text-[9px] uppercase tracking-[0.4em] text-zinc-400">Unit_Price</span>
                            <span class="text-3xl font-black italic" :class="isSoldOut ? 'text-zinc-200 line-through' : ''">
                                {{ selectedVariant ? formatCurrency(selectedVariant.price) : 'Select Config' }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[8px] uppercase tracking-[0.5em] text-zinc-400">Select_Configuration (Size)</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button 
                                    v-for="v in product.variants" 
                                    :key="v.id"
                                    @click="form.variant_id = v.id"
                                    :disabled="isSoldOut"
                                    :class="[
                                        form.variant_id === v.id ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-400 border-zinc-100',
                                        isSoldOut ? 'opacity-30 cursor-not-allowed' : 'hover:border-black'
                                    ]"
                                    class="py-4 border text-[10px] font-black uppercase tracking-widest transition-all"
                                >
                                    {{ v.size }}
                                </button>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button 
                                @click="addToCart"
                                :disabled="form.processing || isSoldOut"
                                :class="isSoldOut ? 'bg-zinc-100 text-zinc-300 cursor-not-allowed' : 'bg-zinc-900 text-white hover:bg-[#10B981] shadow-2xl'"
                                class="w-full py-6 text-[10px] font-black uppercase tracking-[0.6em] transition-all flex items-center justify-center gap-4 group"
                            >
                                <template v-if="!isSoldOut">
                                    {{ form.processing ? 'Syncing_Archive...' : 'Acquire Unit' }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-2 transition-transform"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                                </template>
                                <template v-else>
                                    Archive Status: Sold Out
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8 pt-12 border-t border-zinc-50">
                        <div class="space-y-2">
                            <span class="text-[8px] uppercase tracking-widest text-zinc-400">Material_Protocol</span>
                            <p class="text-[9px] font-black uppercase">Heavy-duty Industrial Blend</p>
                        </div>
                        <div class="space-y-2">
                            <span class="text-[8px] uppercase tracking-widest text-zinc-400">Fulfillment</span>
                            <p class="text-[9px] font-black uppercase italic">2-4 Business Days</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
/* Custom scroll behavior */
.transition-all {
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

button:disabled {
    filter: grayscale(1);
}
</style>