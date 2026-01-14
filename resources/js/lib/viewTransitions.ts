import { router } from '@inertiajs/vue3';

/**
 * Enable view transitions for all Inertia page navigations.
 * Gracefully falls back to standard transitions in browsers that don't support the View Transitions API.
 */
export function enableViewTransitions(): void {
    // Check if the browser supports the View Transitions API
    if (!('startViewTransition' in document)) {
        return;
    }

    // Store the original visit method
    const originalVisit = router.visit;

    // Override the visit method to always include viewTransition option
    router.visit = (url, options = {}) => {
        return originalVisit.call(router, url, {
            ...options,
            viewTransition: true,
        });
    };
}
