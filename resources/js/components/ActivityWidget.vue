<script setup lang="ts">
import { LineChart } from '@vanguard/ui';

const props = defineProps<{
    title: string;
    labels: string[];
    values: number[];
    /** Singular and plural forms, kept apart so the existing catalogs apply. */
    units: { action: string; actions: string };
}>();

/** Reproduces the old Chart.js tooltip: "3 actions" / "1 action". */
function formatValue(value: number): string {
    return `${value} ${value === 1 ? props.units.action : props.units.actions}`;
}
</script>

<template>
    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <h6 class="border-b border-slate-100 px-6 py-4 font-medium">{{ title }}</h6>

        <div class="px-4 py-6">
            <LineChart
                :labels="labels"
                :values="values"
                :label="title"
                :format-value="formatValue"
            />
        </div>
    </div>
</template>
