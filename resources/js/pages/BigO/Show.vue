<script setup lang="ts">
import KeyTakeaways from '@/components/BigO/KeyTakeaways.vue';
import PseudocodeBlock from '@/components/BigO/PseudocodeBlock.vue';
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
import bigO from '@/routes/big-o';
import { getBigORoute } from '@/utils/bigORoutes';
import { onMounted } from 'vue';

interface Example {
    title: string;
    pseudocode: string;
    explanation: string;
}

interface Complexity {
    title: string;
    description: string;
    intuition: string;
    examples: Example[];
    whatCausesThis: string;
    whyItMatters: string;
    keyTakeaways: string[];
}

interface ComplexityListItem {
    slug: string;
    title: string;
    shortTitle: string;
    description: string;
}

interface Props {
    complexity: Complexity;
    slug: string;
    allComplexities: ComplexityListItem[];
}

const props = defineProps<Props>();

// Mark this page as visited in localStorage for progress tracking
onMounted(() => {
    let visited: string[] = [];

    try {
        const storedValue = localStorage.getItem('bigOVisited');
        visited = JSON.parse(storedValue || '[]') as string[];
    } catch (error) {
        console.warn('Failed to parse bigOVisited from localStorage:', error);
        visited = [];
    }

    if (!visited.includes(props.slug)) {
        visited.push(props.slug);
        localStorage.setItem('bigOVisited', JSON.stringify(visited));
    }
});

// Find current index for previous/next navigation
const currentIndex = props.allComplexities.findIndex(
    (c) => c.slug === props.slug
);
const prevComplexity =
    currentIndex > 0 ? props.allComplexities[currentIndex - 1] : null;
const nextComplexity =
    currentIndex < props.allComplexities.length - 1
        ? props.allComplexities[currentIndex + 1]
        : null;

// Generate meta description from the description field
const metaDescription = props.complexity.description.substring(0, 160);
</script>

<template>
    <Head :title="`${complexity.title} | Big-O Workbook`">
        <meta name="description" :content="metaDescription" />
    </Head>

    <div class="min-h-screen bg-background">
        <!-- Simple Header -->
        <header class="border-b border-border bg-card">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="bigO.index().url" class="text-xl font-bold text-foreground">
                    Big-O Workbook
                </Link>
                <Link
                    v-if="!$page.props.auth?.user"
                    :href="login()"
                    class="rounded-md border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                    Log in
                </Link>
            </div>
        </header>
        <article class="mx-auto max-w-4xl space-y-12 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Title -->
            <header class="space-y-4 border-b border-border pb-6">
                <h1
                    class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl"
                >
                    {{ complexity.title }}
                </h1>
            </header>

            <!-- Description -->
            <section class="prose prose-slate dark:prose-invert max-w-none">
                <p class="text-lg leading-8 text-foreground">
                    {{ complexity.description }}
                </p>
            </section>

            <!-- Intuition -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold text-foreground">
                    Intuition
                </h2>
                <p class="text-base leading-7 text-muted-foreground whitespace-pre-line">
                    {{ complexity.intuition }}
                </p>
            </section>

            <!-- Common Examples -->
            <section class="space-y-6">
                <h2 class="text-2xl font-semibold text-foreground">
                    Common Examples
                </h2>

                <div class="space-y-8">
                    <PseudocodeBlock
                        v-for="(example, index) in complexity.examples"
                        :key="index"
                        :title="example.title"
                        :pseudocode="example.pseudocode"
                        :explanation="example.explanation"
                    />
                </div>
            </section>

            <!-- What Causes This Complexity -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold text-foreground">
                    What Causes This Complexity
                </h2>
                <div
                    class="prose prose-slate dark:prose-invert max-w-none text-muted-foreground whitespace-pre-line"
                >
                    {{ complexity.whatCausesThis }}
                </div>
            </section>

            <!-- Why It Matters -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold text-foreground">
                    Why It Matters
                </h2>
                <div
                    class="prose prose-slate dark:prose-invert max-w-none text-muted-foreground whitespace-pre-line"
                >
                    {{ complexity.whyItMatters }}
                </div>
            </section>

            <!-- Key Takeaways -->
            <section>
                <KeyTakeaways :takeaways="complexity.keyTakeaways" />
            </section>

            <!-- Navigation -->
            <nav
                class="flex items-center justify-between border-t border-border pt-8"
            >
                <div>
                    <Link
                        v-if="prevComplexity"
                        :href="getBigORoute(prevComplexity.slug)"
                        class="group inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-md px-3 py-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"
                            />
                        </svg>
                        <div class="text-left">
                            <div class="text-xs text-muted-foreground">
                                Previous
                            </div>
                            <div class="font-semibold">
                                {{ prevComplexity.shortTitle }}
                            </div>
                        </div>
                    </Link>
                </div>

                <div>
                    <Link
                        v-if="nextComplexity"
                        :href="getBigORoute(nextComplexity.slug)"
                        class="group inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-md px-3 py-2"
                    >
                        <div class="text-right">
                            <div class="text-xs text-muted-foreground">
                                Next
                            </div>
                            <div class="font-semibold">
                                {{ nextComplexity.shortTitle }}
                            </div>
                        </div>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-4 w-4 transition-transform group-hover:translate-x-1"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                            />
                        </svg>
                    </Link>
                </div>
            </nav>
        </article>
    </div>
</template>
