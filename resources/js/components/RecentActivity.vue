<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Tooltip, useTranslations } from '@vanguard/ui';

defineProps<{
    activities: Array<{ id: number; description: string; createdAt: string }>;
    viewAllUrl: string | null;
}>();

const { __ } = useTranslations();
</script>

<template>
    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <h6 class="flex items-center justify-between border-b border-slate-100 px-6 py-4 font-medium">
            {{ __('Latest Activity') }}

            <small v-if="activities.length && viewAllUrl">
                <Tooltip :text="__('Complete Activity Log')">
                    <Link :href="viewAllUrl" class="text-primary-600 hover:underline">
                        {{ __('View All') }}
                    </Link>
                </Tooltip>
            </small>
        </h6>

        <div class="p-6">
            <table v-if="activities.length" class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 font-medium">{{ __('Action') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="activity in activities"
                        :key="activity.id"
                        class="border-b border-slate-50 odd:bg-slate-50/60"
                    >
                        <td class="px-4 py-3">{{ activity.description }}</td>
                        <td class="px-4 py-3">{{ activity.createdAt }}</td>
                    </tr>
                </tbody>
            </table>

            <p v-else class="font-light text-slate-500">
                <em>{{ __('No activity from this user yet.') }}</em>
            </p>
        </div>
    </div>
</template>
