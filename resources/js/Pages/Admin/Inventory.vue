<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminProductCard from '@/Components/AdminProductCard.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({ products: Array, filters: Object });
const showModal = ref(false);
const isEditing = ref(false);

const filters = ref({
    category: props.filters?.category || '',
    sort: props.filters?.sort || 'latest',
    search: props.filters?.search || ''
});

const applyFilters = () => {
    router.get(route('admin.inventory'), filters.value, { 
        preserveState: true, replace: true, preserveScroll: true 
    });
};

watch([() => filters.value.category, () => filters.value.sort], applyFilters);
let searchTimer;
watch(() => filters.value.search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 500);
});

const form = useForm({
    id: null, name: '', category: 'Tops', description: '', files: [], existing_images: [], 
    main_file_index: 0, is_featured: false,
    variants: [{ id: null, size: '', price: '', stock: '', discount_price: '', discount_percentage: 0 }]
});

const updateVariantPrice = (index, type, val) => {
    const v = form.variants[index];
    const num = parseFloat(val) || 0;
    if (type === 'price') v.price = num;
    if (type === 'percent') v.discount_percentage = num;
    if (v.price > 0) v.discount_price = (v.price * (1 - (v.discount_percentage || 0) / 100)).toFixed(2);
};

const handleFileUpload = (e) => {
    const uploadedFiles = Array.from(e.target.files);
    form.files = [...form.files, ...uploadedFiles];
};

const getUrl = (file) => {
    if (file instanceof File) return URL.createObjectURL(file);
    return file.image_path ? (file.image_path.startsWith('http') ? file.image_path : '/storage/' + file.image_path) : '';
};

const openModal = (product = null) => {
    isEditing.value = !!product;
    if (product) {
        Object.assign(form, { 
            ...product, files: [], existing_images: product.images || [], main_file_index: 0,
            variants: product.variants?.length ? product.variants.map(v => ({...v, discount_price: (v.price * (1 - (v.discount_percentage || 0) / 100)).toFixed(2)})) : [{ id: null, size: '', price: '', stock: '', discount_price: '', discount_percentage: 0 }]
        });
    } else {
        form.reset();
        form.files = []; form.existing_images = []; 
        form.variants = [{ id: null, size: '', price: '', stock: '', discount_price: '', discount_percentage: 0 }];
    }
    showModal.value = true;
};

const deleteProduct = (id) => {
    if (confirm('PROTOCOL ALERT: Eject this unit from registry?')) {
        router.delete(route('admin.products.destroy', id), { preserveScroll: true });
    }
};

const submit = () => {
    const requestRoute = isEditing.value ? route('admin.products.update', form.id) : route('admin.products.store');
    router.post(requestRoute, form.data(), { forceFormData: true, preserveScroll: true, onSuccess: () => { showModal.value = false; form.reset(); } });
};

const removeFile = (idx) => { form.files.splice(idx, 1); if(form.main_file_index >= form.files.length) form.main_file_index = 0; };
const removeExistingImage = (idx) => form.existing_images.splice(idx, 1);
</script>

<template>
    <Head title="Inventory Registry // Adonis" />
    <AdminLayout>
        <div class="max-w-[1400px] mx-auto space-y-6 md:space-y-8 font-['Inter'] py-4 md:py-6 px-2 md:px-0 text-left">
            
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-xl md:rounded-2xl border border-zinc-200 shadow-sm gap-6 text-left">
                <div class="space-y-1 text-center md:text-left w-full md:w-auto">
                    <span class="text-[9px] md:text-[10px] text-zinc-400 uppercase tracking-[0.4em] font-black italic">Archive Protocol // Sync</span>
                    <h2 class="text-2xl md:text-3xl font-black tracking-tighter text-zinc-900 uppercase font-['Plus_Jakarta_Sans']">Inventory</h2>
                </div>
                <button @click="openModal()" class="w-full md:w-auto bg-zinc-900 text-white px-8 py-4 md:py-3.5 text-[10px] md:text-[11px] font-black uppercase tracking-widest hover:bg-black transition-all rounded-lg shadow-xl active:scale-95 text-center">
                    + New Registration
                </button>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-zinc-50/50 p-2 md:p-2.5 rounded-xl md:rounded-2xl border border-zinc-100">
                <div class="relative w-full md:w-96 group">
                    <input v-model="filters.search" type="text" placeholder="Scan Archive..." class="w-full bg-white border border-zinc-200 rounded-lg md:rounded-xl py-3.5 pl-11 pr-6 focus:ring-2 focus:ring-zinc-900 outline-none text-[10px] md:text-[11px] font-black uppercase h-[48px] md:h-[50px] transition-all">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>

                <div class="grid grid-cols-2 md:flex items-center gap-2 md:gap-4 w-full md:w-auto">
                    <div class="relative group min-w-[140px]">
                        <select v-model="filters.category" class="w-full bg-white border border-zinc-200 rounded-lg md:rounded-xl py-2 pl-4 pr-10 text-[9px] md:text-[10px] font-black uppercase h-[48px] md:h-[50px] appearance-none focus:ring-2 focus:ring-zinc-900 outline-none cursor-pointer">
                            <option value="">All Categories</option>
                            <option v-for="cat in ['Tops', 'Bottoms', 'Accessories', 'Tank Binder', 'Half Binder']" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>

                    <div class="relative group min-w-[140px]">
                        <select v-model="filters.sort" class="w-full bg-white border border-zinc-200 rounded-lg md:rounded-xl py-2 pl-4 pr-10 text-[9px] md:text-[10px] font-black uppercase h-[48px] md:h-[50px] appearance-none focus:ring-2 focus:ring-zinc-900 outline-none cursor-pointer">
                            <option value="latest">Sort: Latest</option>
                            <option value="stock_asc">Critical Stock (Low-High)</option>
                            <option value="stock_desc">Massive Stock (High-Low)</option>
                            <option value="price_desc">Value: MAX</option>
                            <option value="price_asc">Value: MIN</option>
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="products.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
                <AdminProductCard v-for="product in products" :key="product.id" :product="product" @edit="openModal" @delete="deleteProduct" class="hover:-translate-y-1 transition-transform" />
            </div>
            <div v-else class="py-24 md:py-32 text-center bg-zinc-50 border-2 border-dashed border-zinc-200 rounded-2xl md:rounded-[2rem]">
                <p class="text-zinc-400 text-[10px] md:text-[11px] font-black uppercase tracking-[0.5em]">No data records found</p>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-[200] flex items-end md:items-center justify-center bg-zinc-900/90 backdrop-blur-md p-0 md:p-6 transition-all">
            <div class="bg-white w-full max-w-5xl md:rounded-2xl shadow-2xl flex flex-col h-[92vh] md:h-auto md:max-h-[90vh] overflow-hidden border border-zinc-200 text-left">
                <div class="px-6 md:px-10 py-5 md:py-6 border-b border-zinc-100 flex justify-between items-center bg-zinc-50/50 backdrop-blur-sm sticky top-0 z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-zinc-900 rounded-full"></div>
                        <h2 class="text-lg md:text-xl font-black text-zinc-900 uppercase tracking-tighter italic">{{ isEditing ? 'Configure Profile' : 'Unit Registration' }}</h2>
                    </div>
                    <button @click="showModal = false" class="bg-white p-2 rounded-lg border border-zinc-200 hover:text-red-500 transition-all">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="overflow-y-auto p-6 md:p-10 space-y-10 text-left font-black pb-32 md:pb-10 custom-scroll">
                    <div class="space-y-5">
                        <div class="flex items-center gap-3">
                            <span class="text-[9px] font-black uppercase text-zinc-900 bg-zinc-100 px-3 py-1 rounded-md">Step 01</span>
                            <h3 class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Visual Assets</h3>
                        </div>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:flex flex-wrap gap-3 md:gap-4">
                            <div v-for="(img, idx) in form.existing_images" :key="'ex-'+idx" class="relative aspect-square w-full md:w-32 bg-zinc-100 rounded-xl overflow-hidden group border border-zinc-200 shadow-sm transition-all hover:ring-2 hover:ring-zinc-900">
                                <img :src="getUrl(img)" class="w-full h-full object-cover">
                                <button @click.prevent="removeExistingImage(idx)" class="absolute inset-0 bg-red-600/90 text-white opacity-0 group-hover:opacity-100 text-[8px] flex items-center justify-center uppercase font-black">Eject</button>
                            </div>
                            <div v-for="(file, idx) in form.files" :key="'new-'+idx" :class="[form.main_file_index === idx ? 'ring-4 ring-emerald-500' : '']" class="relative aspect-square w-full md:w-32 bg-emerald-50 rounded-xl overflow-hidden group shadow-md transition-all">
                                <img :src="getUrl(file)" class="w-full h-full object-cover">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 gap-2 transition-all">
                                    <button @click.prevent="form.main_file_index = idx" class="bg-emerald-500 text-white p-2 rounded-lg shadow-lg"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></button>
                                    <button @click.prevent="removeFile(idx)" class="bg-red-500 text-white p-2 rounded-lg shadow-lg"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                                </div>
                                <div v-if="form.main_file_index === idx" class="absolute top-2 right-2 bg-emerald-500 text-white text-[7px] px-2 py-0.5 rounded-full uppercase font-black">Main</div>
                            </div>
                            <label class="aspect-square w-full md:w-32 border-2 border-dashed border-zinc-200 flex flex-col items-center justify-center rounded-xl cursor-pointer hover:bg-zinc-50 transition-all gap-2 group text-left">
                                <input type="file" multiple @change="handleFileUpload" class="hidden">
                                <svg class="text-zinc-300 group-hover:text-zinc-900 transition-colors" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="M12 5v14M5 12h14"/></svg>
                                <span class="text-[8px] uppercase font-black text-zinc-400">Append</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16">
                        <div class="space-y-8">
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black uppercase text-zinc-900 bg-zinc-100 px-3 py-1 rounded-md">Step 02</span>
                                <h3 class="text-[10px] md:text-[11px] font-black uppercase text-zinc-400 tracking-widest">Base Identity</h3>
                            </div>
                            <div class="space-y-5">
                                <div class="space-y-2 text-left">
                                    <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest ml-1">Product Name</label>
                                    <input v-model="form.name" type="text" class="w-full bg-zinc-50 border-none rounded-xl py-4 px-5 text-sm font-black focus:ring-2 focus:ring-zinc-900 uppercase transition-all">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2 text-left">
                                        <label class="text-[9px] uppercase text-zinc-400 font-black tracking-widest ml-1">Classification</label>
                                        <div class="relative">
                                            <select v-model="form.category" class="w-full bg-zinc-50 border-none rounded-xl py-4 pl-5 pr-10 text-[10px] font-black uppercase appearance-none cursor-pointer focus:ring-2 focus:ring-zinc-900 outline-none transition-all">
                                                <option v-for="cat in ['Tops', 'Bottoms', 'Accessories', 'Tank Binder', 'Half Binder']" :key="cat" :value="cat">{{ cat }}</option>
                                            </select>
                                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><path d="m6 9 6 6 6-6"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex items-end">
                                        <div @click="form.is_featured = !form.is_featured" class="flex items-center justify-between gap-3 cursor-pointer group bg-zinc-50 hover:bg-zinc-100 px-5 py-4 rounded-xl w-full h-[54px] border border-transparent hover:border-zinc-200 transition-all">
                                            <span class="text-[10px] font-black uppercase text-zinc-400 tracking-tight group-hover:text-zinc-900">Featured</span>
                                            <div :class="form.is_featured ? 'bg-zinc-900' : 'bg-zinc-200'" class="w-8 h-4 rounded-full relative transition-colors duration-300">
                                                <div :class="form.is_featured ? 'translate-x-4' : 'translate-x-0'" class="absolute top-1 left-1 w-2 h-2 bg-white rounded-full transition-transform duration-300"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8 text-left">
                            <div class="flex justify-between items-center border-b border-zinc-100 pb-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-[9px] font-black uppercase text-zinc-900 bg-zinc-100 px-3 py-1 rounded-md">Step 03</span>
                                    <h3 class="text-[10px] md:text-[11px] font-black uppercase text-zinc-400 tracking-widest">Variant Manifest</h3>
                                </div>
                                <button type="button" @click="form.variants.push({size:'', price:'', stock:'', discount_percentage: 0})" class="bg-zinc-900 text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase hover:bg-black shadow-lg">+ Add row</button>
                            </div>
                            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scroll text-left">
                                <div v-for="(v, i) in form.variants" :key="i" class="bg-zinc-50/50 p-5 rounded-2xl border border-zinc-100 space-y-4 transition-all hover:bg-zinc-50 hover:border-zinc-200">
                                    <div class="flex gap-2 text-left">
                                        <input v-model="v.size" placeholder="SZ" class="w-16 md:w-20 bg-white rounded-xl py-3 text-center text-[11px] font-black border-none uppercase shadow-sm">
                                        <input v-model="v.price" @input="updateVariantPrice(i, 'price', $event.target.value)" placeholder="Price" class="flex-1 bg-white rounded-xl py-3 px-4 text-[11px] font-black border-none font-mono shadow-sm">
                                        <input v-model="v.stock" placeholder="Qty" class="w-20 md:w-24 bg-white rounded-xl py-3 text-center text-[11px] font-black border-none shadow-sm">
                                        <button @click="form.variants.splice(i,1)" type="button" class="text-zinc-200 hover:text-red-500 transition-all"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4.5"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-zinc-200/50 pt-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <label class="text-[8px] font-black text-emerald-600 uppercase tracking-widest">Discount %</label>
                                            <input v-model="v.discount_percentage" @input="updateVariantPrice(i, 'percent', $event.target.value)" type="number" class="w-16 bg-white rounded-lg py-1.5 text-center text-[11px] font-black border-none text-emerald-600 outline-none">
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[8px] text-zinc-400 uppercase font-black tracking-widest">Final Value</p>
                                            <p class="text-lg font-black font-mono tracking-tight text-zinc-900">₱{{ Math.round(v.discount_price || v.price || 0).toLocaleString() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fixed md:relative bottom-0 left-0 w-full md:w-auto bg-white/90 backdrop-blur-md p-6 md:p-0 md:pt-6 border-t md:border-none border-zinc-100 flex justify-end gap-3 z-30">
                        <button type="button" @click="showModal = false" class="hidden md:block px-8 py-4 text-[10px] font-black uppercase text-zinc-400 tracking-widest hover:text-zinc-900 transition-all">Discard</button>
                        <button type="submit" :disabled="form.processing" class="w-full md:w-auto bg-zinc-900 text-white px-12 py-4 md:py-3.5 text-[10px] md:text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-black transition-all shadow-xl active:scale-95 disabled:opacity-50 transition-all">
                            {{ form.processing ? 'Syncing...' : 'Commit Registration' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #111827; }
.transition-all { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@media (max-width: 768px) { .pb-32 { padding-bottom: 8rem; } }
</style>