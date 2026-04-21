<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    stats: Object,
    recent_activity: Array,
    chart_data: Array,
    current_range: String
});

const formatCurrency = (val) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 }).format(val);
const canvasRef = ref(null);
const selectedRange = ref(props.current_range || '7days');
let chartInstance = null;

const updateFilter = () => {
    router.get(route('admin.dashboard'), { range: selectedRange.value }, { 
        preserveState: true, 
        preserveScroll: true 
    });
};

const initChart = () => {
    if (chartInstance) chartInstance.destroy();
    if (!canvasRef.value) return;

    const ctx = canvasRef.value.getContext('2d');
    
    // CREATE GRADIENT FOR AREA FILL
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels: props.chart_data.map(d => d.date),
            datasets: [{
                label: 'Revenue',
                data: props.chart_data.map(d => d.amount),
                
                // STYLING
                borderColor: '#10b981',
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#10b981',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3,
                
                // THE "SMOOTH" FACTOR
                tension: 0.4, // Heto yung nagpapasmooth (0.4 is sweet spot)
                fill: true,
                backgroundColor: gradient,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    titleFont: { family: 'Inter', size: 11, weight: 'bold' },
                    bodyFont: { family: 'JetBrains Mono', size: 10 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: (context) => ` REVENUE: ₱${context.parsed.y.toLocaleString()}`
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    border: { display: false },
                    grid: { 
                        color: 'rgba(241, 245, 249, 1)',
                        drawTicks: false
                    }, 
                    ticks: { 
                        padding: 10,
                        font: { family: 'Inter', size: 9, weight: '600' }, 
                        color: '#94a3b8',
                        callback: (value) => '₱' + (value >= 1000 ? (value/1000) + 'k' : value)
                    } 
                },
                x: { 
                    border: { display: false },
                    grid: { display: false }, 
                    ticks: { 
                        padding: 10,
                        font: { family: 'Inter', size: 9, weight: '800' }, 
                        color: '#64748b' 
                    } 
                }
            }
        }
    });
};

onMounted(() => initChart());
watch(() => props.chart_data, () => initChart());
</script>

<template>
    <Head title="Terminal // Overview" />

    <AdminLayout>
        <div class="max-w-[1400px] mx-auto space-y-6 md:space-y-8 font-['Inter'] py-4 md:py-6 px-2 md:px-0 text-left">
            
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-xl md:rounded-2xl border border-zinc-200 shadow-sm gap-6">
                <div class="space-y-1 text-center md:text-left w-full md:w-auto">
                    <span class="text-[9px] md:text-[10px] text-zinc-400 uppercase tracking-[0.4em] font-black italic">System Manifest // Operational</span>
                    <h2 class="text-2xl md:text-3xl font-black tracking-tighter text-zinc-900 uppercase font-['Plus_Jakarta_Sans']">Terminal</h2>
                </div>
                <Link :href="route('admin.inventory')" class="w-full md:w-auto bg-zinc-900 text-white px-8 py-4 md:py-3.5 text-[10px] md:text-[11px] font-black uppercase tracking-widest hover:bg-black transition-all rounded-lg shadow-xl active:scale-95 text-center">
                    Access Repository
                </Link>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
                <div v-for="stat in [
                    { label: 'Revenue', value: formatCurrency(props.stats.total_revenue), color: 'text-emerald-600', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.651 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.651-1M12 16V15' },
                    { label: 'Orders', value: String(props.stats.total_orders).padStart(3, '0'), color: 'text-zinc-900', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
                    { label: 'Inventory', value: String(props.stats.total_units).padStart(3, '0'), color: 'text-zinc-900', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
                    { label: 'Vouchers', value: String(props.stats.active_vouchers).padStart(2, '0'), color: 'text-zinc-900', icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' },
                    { label: 'Low Stock', value: String(props.stats.low_stock).padStart(2, '0'), color: props.stats.low_stock > 0 ? 'text-red-600' : 'text-zinc-300', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' }
                ]" :key="stat.label" class="p-4 md:p-6 bg-white border border-zinc-200 rounded-xl md:rounded-2xl shadow-sm hover:border-zinc-900 transition-all group text-left">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-zinc-50 rounded-lg group-hover:bg-zinc-900 group-hover:text-white transition-all duration-500">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path :d="stat.icon" />
                            </svg>
                        </div>
                        <p class="text-[9px] md:text-[10px] uppercase tracking-widest text-zinc-400 font-black italic">{{ stat.label }}</p>
                    </div>
                    <p :class="['text-xl md:text-2xl font-black tracking-tighter font-mono', stat.color]">{{ stat.value }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
                <div class="lg:col-span-8 bg-white border border-zinc-200 rounded-2xl p-5 md:p-8 shadow-sm">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div class="text-left">
                            <h4 class="text-[10px] md:text-[11px] uppercase tracking-[0.2em] text-zinc-900 font-black italic">Revenue Manifest</h4>
                            <p class="text-[8px] md:text-[9px] text-zinc-400 uppercase font-bold mt-1 tracking-widest italic">Temporal Activity Visualization</p>
                        </div>

                        <div class="relative w-full sm:w-auto min-w-[160px]">
                            <select v-model="selectedRange" @change="updateFilter" 
                                class="w-full bg-zinc-50 border border-zinc-100 rounded-xl text-[10px] font-black uppercase tracking-widest pl-4 pr-10 py-3 appearance-none focus:ring-2 focus:ring-zinc-900 outline-none cursor-pointer h-[48px] md:h-[45px]">
                                <option value="7days">7 Days Protocol</option>
                                <option value="this_month">Current Month</option>
                                <option value="last_month">Previous Month</option>
                                <option value="this_year">Annual Cycle</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="h-[250px] md:h-[350px] w-full text-left relative">
                        <canvas ref="canvasRef"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6 text-left">
                    <h4 class="text-[10px] md:text-[11px] uppercase tracking-[0.3em] text-zinc-900 font-black border-b-2 border-zinc-900 pb-3 ml-1 italic">Internal Protocols</h4>
                    <div class="space-y-4">
                        <Link v-for="link in [
                            { label: 'Execute Orders', desc: 'Fulfillment Flow', route: 'admin.orders.index' },
                            { label: 'Promotional Logic', desc: 'Voucher Config', route: 'admin.vouchers.index' }
                        ]" :key="link.label" :href="route(link.route)" class="flex items-center justify-between bg-white border border-zinc-100 p-6 md:p-8 group hover:border-zinc-900 transition-all rounded-2xl shadow-sm text-left">
                            <div class="flex flex-col gap-1">
                                <span class="text-[11px] md:text-[12px] font-black uppercase tracking-widest text-zinc-900">{{ link.label }}</span>
                                <span class="text-[8px] md:text-[9px] text-zinc-400 uppercase font-black tracking-widest italic">{{ link.desc }}</span>
                            </div>
                            <div class="p-2 bg-zinc-50 rounded-xl group-hover:bg-zinc-900 group-hover:text-white transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </div>
                        </Link>

                        <div class="p-8 bg-zinc-900 rounded-[2rem] border border-zinc-800 shadow-2xl relative overflow-hidden group">
                            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-all"></div>
                            <div class="relative z-10 space-y-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-[8px] md:text-[9px] text-zinc-500 uppercase tracking-[0.4em] font-black italic">Security Protocol</p>
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_12px_rgba(16,185,129,0.8)]"></div>
                                </div>
                                <div class="space-y-1 text-left">
                                    <p class="text-[10px] md:text-[11px] font-black text-white uppercase tracking-[0.2em]">Adonis Core // ACTIVE</p>
                                    <p class="text-[8px] md:text-[9px] text-zinc-500 uppercase font-black tracking-widest">Operational // Encrypted</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-sm text-left mt-8">
                <div class="px-5 py-5 border-b border-zinc-50 flex justify-between items-center bg-zinc-50/30">
                    <h4 class="text-[10px] md:text-[11px] uppercase tracking-[0.2em] text-zinc-900 font-black italic">Recent Logs</h4>
                    <Link :href="route('admin.orders.index')" class="text-[9px] font-black text-zinc-400 hover:text-black transition-colors uppercase tracking-widest">Full Archive</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                            <tr class="bg-zinc-50/50 border-b border-zinc-100 text-[9px] md:text-[10px] uppercase tracking-widest text-zinc-500 font-black italic">
                                <th class="px-8 py-4">ID</th>
                                <th class="px-8 py-4">Acquirer Identity</th>
                                <th class="px-8 py-4 text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50 text-[11px] md:text-[12px] font-black">
                            <tr v-for="log in props.recent_activity" :key="log.id" class="hover:bg-zinc-50/80 transition-all group">
                                <td class="px-8 py-4 font-mono text-zinc-400 group-hover:text-zinc-900 italic">#{{ String(log.id).padStart(5, '0') }}</td>
                                <td class="px-8 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-black text-zinc-900 uppercase tracking-tight">{{ log.user_name }}</span>
                                        <span class="text-[8px] text-zinc-400 uppercase tracking-tighter font-black italic">{{ new Date(log.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-right font-black text-zinc-900 font-mono">{{ formatCurrency(log.total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
</style>