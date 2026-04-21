<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Success | ADONIS PH" />
    
    <div class="min-h-screen bg-white flex flex-col items-center justify-center px-8">
        <div class="max-w-xl w-full text-center space-y-16">
            <div class="space-y-6">
                <span class="text-[9px] font-bold uppercase tracking-[1em] text-gray-300 block">Status: Confirmed</span>
                <h1 class="text-5xl md:text-7xl font-extralight tracking-tighter italic">Thank you.</h1>
            </div>

            <div class="h-[1px] w-12 bg-black mx-auto"></div>

            <div class="space-y-8">
                <p class="text-[11px] leading-relaxed text-gray-500 uppercase tracking-[0.3em] max-w-sm mx-auto">
                    Your order has been logged into the studio archive. A notification will follow once shipment is initialized.
                </p>
                <p class="text-[9px] font-bold uppercase tracking-[0.5em] text-black">Reference #{{ Math.floor(Math.random() * 1000000) }}</p>
            </div>

            <Link href="/" class="inline-block text-[9px] font-bold uppercase tracking-[0.4em] py-6 px-12 border border-gray-100 hover:border-black transition-all duration-700">
                Back to Collection
            </Link>
        </div>
    </div>
</template>