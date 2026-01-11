<script setup lang="ts">
interface Props {
    title: string;
    pseudocode: string;
    explanation: string;
}

defineProps<Props>();
</script>

<template>
    <div class="space-y-3">
        <h4 class="text-sm font-semibold text-foreground">{{ title }}</h4>

        <!-- Pseudocode card with subtle styling -->
        <div
            class="overflow-x-auto rounded-lg border border-border bg-muted/50 p-4"
        >
            <pre
                class="text-sm leading-relaxed text-foreground font-mono"
            ><code>{{ pseudocode }}</code></pre>
        </div>

        <!-- Explanation -->
        <p class="text-sm text-muted-foreground leading-relaxed">
            {{ explanation }}
        </p>
    </div>
</template>
