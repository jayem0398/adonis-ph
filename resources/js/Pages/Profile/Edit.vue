<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const page = usePage();
const isScrolled = ref(false);

const cartCount = computed(() => page.props.cartCount || 0);
const authUser = computed(() => page.props.auth?.user || null);
const isUserAdmin = computed(() => authUser.value && (authUser.value.role === 'admin' || authUser.value.is_admin));

const handleScroll = () => { isScrolled.value = window.scrollY > 20; };
onMounted(() => { window.addEventListener('scroll', handleScroll); });
onUnmounted(() => { window.removeEventListener('scroll', handleScroll); });
</script>

<template>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <Head title="Account Settings // Adonis" />

    <div class="min-h-screen bg-white flex flex-col antialiased text-zinc-900 selection:bg-[#10B981] selection:text-white font-['Inter']">
        
        <nav :class="[isScrolled ? 'bg-white/95 backdrop-blur-md py-3 border-b border-zinc-100 shadow-sm' : 'bg-white py-5']" 
             class="fixed top-0 w-full z-[110] transition-all duration-500 px-4 md:px-12 flex items-center justify-between">
            
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
            </div>

            <div class="flex items-center gap-6">
                <Link :href="authUser ? (isUserAdmin ? route('admin.dashboard') : route('profile.edit')) : route('login')" 
                      class="text-zinc-400 hover:text-black transition-colors"
                      :class="{'text-black': route().current('profile.edit')}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </Link>
            </div>
        </nav>

        <main class="mt-24 md:mt-32 max-w-[1000px] mx-auto w-full px-4 md:px-6 pb-28 text-left">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12 border-b border-zinc-900 pb-8">
                <div class="flex flex-col gap-1">
                    <span class="text-[8px] text-[#10B981] tracking-[0.6em] uppercase font-bold italic">User Configuration</span>
                    <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tighter leading-none italic font-['Plus_Jakarta_Sans'] text-zinc-900">
                        Profile <span class="text-zinc-200 font-black">Settings</span>
                    </h1>
                </div>

                <Link :href="route('logout')" method="post" as="button" 
                      class="flex items-center justify-center gap-2 px-5 py-2.5 w-max bg-white border border-red-500 text-red-500 text-[9px] font-bold uppercase tracking-[0.2em] rounded-xl hover:bg-red-500 hover:text-white transition-all italic active:scale-95 group">
                    <span>Logout</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="group-hover:translate-x-0.5 transition-transform"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </Link>
            </div>

            <div class="space-y-12">
                <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-10 items-start">
                    <div class="lg:col-span-4 space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-900 text-[8px] font-bold italic">01 //</span>
                            <h2 class="text-[10px] font-bold uppercase tracking-[0.3em]">Identity markers</h2>
                        </div>
                        <p class="text-[9px] text-zinc-400 uppercase tracking-widest leading-relaxed">Modify record identity.</p>
                    </div>
                    <div class="lg:col-span-8 bg-zinc-50 p-6 md:p-10 rounded-2xl border border-zinc-100/50">
                        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                    </div>
                </section>

                <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-10 items-start">
                    <div class="lg:col-span-4 space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-900 text-[8px] font-bold italic">02 //</span>
                            <h2 class="text-[10px] font-bold uppercase tracking-[0.3em]">Security Access</h2>
                        </div>
                        <p class="text-[9px] text-zinc-400 uppercase tracking-widest leading-relaxed">Update access keys.</p>
                    </div>
                    <div class="lg:col-span-8 bg-zinc-50 p-6 md:p-10 rounded-2xl border border-zinc-100/50">
                        <UpdatePasswordForm />
                    </div>
                </section>

                <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-10 items-start pt-12 border-t border-zinc-100">
                    <div class="lg:col-span-4 space-y-2">
                        <div class="flex items-center gap-3 text-red-500">
                            <span class="text-[8px] font-bold italic">03 //</span>
                            <h2 class="text-[10px] font-bold uppercase tracking-[0.3em]">Termination</h2>
                        </div>
                        <p class="text-[9px] text-zinc-400 uppercase tracking-widest leading-relaxed font-medium">Delete identity data.</p>
                    </div>
                    <div class="lg:col-span-8 border border-red-50 p-6 md:p-10 rounded-2xl">
                        <DeleteUserForm />
                    </div>
                </section>
            </div>
        </main>

        <footer class="py-12 pb-24 border-t border-zinc-50 text-center">
            <p class="text-[7px] uppercase tracking-[1em] text-zinc-300 italic font-medium">ADONIS PHILIPPINES — 2026</p>
        </footer>

        <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-zinc-100 z-[120] flex items-center justify-around py-3 px-4 shadow-[0_-15px_40px_rgba(0,0,0,0.08)]">
            <Link :href="route('welcome')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300'"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('welcome') ? 'text-zinc-900' : 'text-zinc-300'">Home</span>
            </Link>
            <Link :href="route('archive.index')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300'"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('archive.index') ? 'text-zinc-900' : 'text-zinc-300'">Shop</span>
            </Link>
            <Link :href="route('cart.index')" class="relative flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300'"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span v-if="cartCount > 0" class="absolute -top-1.5 right-0 text-[6px] bg-[#10B981] text-white px-1.5 py-0.5 rounded-full border border-white font-bold">{{ cartCount }}</span>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('cart.index') ? 'text-[#10B981]' : 'text-zinc-300'">Bag</span>
            </Link>
            <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 group">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :class="route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300'"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                <span class="text-[7px] uppercase tracking-widest font-bold" :class="route().current('orders.*') ? 'text-zinc-900' : 'text-zinc-300'">Orders</span>
            </Link>
        </nav>
    </div>
</template>

<style scoped>
:deep(input) {
    background-color: white !important;
    border: 1px solid #f4f4f5 !important;
    border-radius: 12px !important;
    padding: 12px 14px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    text-transform: uppercase !important;
    transition: all 0.3s ease;
    width: 100%;
}
:deep(input:focus) { border-color: #18181b !important; outline: none !important; }
:deep(label) { font-size: 8px !important; text-transform: uppercase !important; letter-spacing: 0.3em !important; color: #a1a1aa !important; font-weight: 700 !important; margin-bottom: 6px !important; display: block; }
:deep(button[type="submit"]) { background-color: #18181b !important; color: white !important; border-radius: 12px !important; padding: 12px 28px !important; font-size: 9px !important; font-weight: 800 !important; letter-spacing: 0.3em !important; text-transform: uppercase !important; transition: all 0.3s ease !important; width: 100%; }
@media (min-width: 768px) { :deep(button[type="submit"]) { width: auto !important; } }
</style>