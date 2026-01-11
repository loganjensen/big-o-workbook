<script setup lang="ts">
interface Props {
    takeaways: string[];
}

defineProps<Props>();
</script>

<template>
    <div
        class="rounded-lg border-l-4 border-l-primary bg-primary/5 p-6 space-y-4"
    >
        <h3 class="text-lg font-semibold text-foreground">Key Takeaways</h3>

        <ul class="space-y-3">
            <li
                v-for="(takeaway, index) in takeaways"
                :key="index"
                class="flex gap-3 text-sm leading-relaxed text-foreground"
            >
                <!-- Bullet point -->
                <span class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-primary" />

                <!-- Takeaway text -->
                <span>{{ takeaway }}</span>
            </li>
        </ul>
    </div>
</template>
