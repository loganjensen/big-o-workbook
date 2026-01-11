<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Complexity {
    slug: string;
    title: string;
    shortTitle: string;
    description: string;
}

interface Props {
    complexities: Complexity[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Big-O Complexities',
        href: '/big-o',
    },
];
</script>

<template>
    <Head title="Big-O Notation — Learn Time Complexity">
        <meta
            name="description"
            content="Learn Big-O notation through clear explanations, intuitive analogies, and practical examples. Master time complexity from O(1) to O(n!) with our comprehensive guide."
        />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="space-y-4">
                <h1
                    class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl"
                >
                    Big-O Notation
                </h1>
                <p
                    class="text-lg leading-8 text-muted-foreground max-w-3xl"
                >
                    Big-O notation describes how algorithms scale as input grows.
                    It's not about exact timing—it's about understanding growth
                    patterns. Whether you're optimizing code or preparing for
                    interviews, these fundamentals will help you write more
                    efficient software.
                </p>
                <p class="text-base text-muted-foreground max-w-3xl">
                    Each complexity class below explains what it means, when
                    it's acceptable, and how to recognize it in real code. Start
                    with O(1) and work your way through—or jump to the
                    complexity you're curious about.
                </p>
            </div>

            <!-- Complexity Cards Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="complexity in complexities"
                    :key="complexity.slug"
                    :href="`/big-o/${complexity.slug}`"
                    class="group relative overflow-hidden rounded-lg border border-border bg-card p-6 transition-all hover:border-primary hover:shadow-md hover:shadow-primary/10 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                    <div class="space-y-2">
                        <h2
                            class="text-2xl font-semibold text-foreground group-hover:text-primary transition-colors"
                        >
                            {{ complexity.shortTitle }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            {{ complexity.description }}
                        </p>
                    </div>

                    <!-- Arrow icon -->
                    <div
                        class="absolute right-4 top-4 text-muted-foreground transition-all group-hover:translate-x-1 group-hover:text-primary"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                            />
                        </svg>
                    </div>
                </Link>
            </div>

            <!-- Footer Note -->
            <div
                class="mt-12 rounded-lg border border-border bg-muted/50 p-6"
            >
                <p class="text-sm text-muted-foreground">
                    <strong class="text-foreground">Note:</strong> These pages
                    focus on conceptual understanding, not mathematical proofs.
                    You'll see pseudocode examples and real-world analogies
                    designed to build intuition. Each page takes about 5-7
                    minutes to read.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
