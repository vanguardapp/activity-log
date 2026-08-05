<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { AppLayout, Input, Pagination, Tooltip, useTranslations } from '@vanguard/ui';
import { ref, watch } from 'vue';

interface ActivityRow {
    id: number;
    userId: number;
    userName: string;
    userUrl: string | null;
    ipAddress: string;
    userAgent: string;
    description: string;
    createdAt: string;
}

const props = defineProps<{
    activities: {
        data: ActivityRow[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    /** Admin views list the user column; the profile view does not. */
    adminView: boolean;
    user: { id: number; nameOrEmail: string } | null;
    filters: { search: string };
}>();

const { __ } = useTranslations();

const search = ref(props.filters.search);

/** Replaces the GET form the Blade page submitted on every search. */
let timer: ReturnType<typeof setTimeout> | undefined;

watch(search, () => {
    clearTimeout(timer);

    timer = setTimeout(() => {
        router.get(
            window.location.pathname,
            { search: search.value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});

const breadcrumbs =
    props.adminView && props.user
        ? [
              { label: __('Activity Log'), href: route('activity.index') },
              { label: props.user.nameOrEmail },
          ]
        : [{ label: __('Activity Log') }];
</script>

<template>
    <AppLayout
        :title="__('Activity Log')"
        :heading="user ? user.nameOrEmail : __('Activity Log')"
        :breadcrumbs="breadcrumbs"
    >
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 border-b border-slate-100 pb-4">
                <div class="relative md:w-96">
                    <label for="search" class="sr-only">{{ __('Search for Action') }}</label>
                    <Input
                        id="search"
                        v-model="search"
                        class="w-full pr-9"
                        :placeholder="__('Search for Action')"
                    />
                    <button
                        v-if="search"
                        type="button"
                        class="absolute inset-y-0 right-0 flex w-9 cursor-pointer items-center justify-center text-slate-400 hover:text-slate-600"
                        :aria-label="__('Cancel')"
                        @click="search = ''"
                    >
                        <i class="fas fa-times" aria-hidden="true" />
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th v-if="adminView" class="px-4 py-3 font-medium">{{ __('User') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('IP Address') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Message') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Log Time') }}</th>
                            <th class="px-4 py-3 text-center font-medium">{{ __('More Info') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="!activities.data.length">
                            <td :colspan="adminView ? 5 : 4" class="px-4 py-3">
                                <em class="text-slate-500">{{ __('No records found.') }}</em>
                            </td>
                        </tr>

                        <tr
                            v-for="activity in activities.data"
                            :key="activity.id"
                            class="border-b border-slate-50 odd:bg-slate-50/60"
                        >
                            <td v-if="adminView" class="px-4 py-3">
                                <Tooltip v-if="activity.userUrl" :text="__('View Activity Log')">
                                    <Link
                                        :href="activity.userUrl"
                                        class="text-primary-600 hover:underline"
                                    >
                                        {{ activity.userName }}
                                    </Link>
                                </Tooltip>
                                <template v-else>{{ activity.userName }}</template>
                            </td>
                            <td class="px-4 py-3">{{ activity.ipAddress }}</td>
                            <td class="px-4 py-3">{{ activity.description }}</td>
                            <td class="px-4 py-3">{{ activity.createdAt }}</td>
                            <td class="px-4 py-3 text-center">
                                <Tooltip :text="activity.userAgent">
                                    <button
                                        type="button"
                                        class="cursor-pointer text-slate-400 hover:text-slate-600"
                                        :aria-label="__('User Agent')"
                                    >
                                        <i class="fas fa-info-circle" aria-hidden="true" />
                                    </button>
                                </Tooltip>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="activities.links" />
        </div>
    </AppLayout>
</template>
