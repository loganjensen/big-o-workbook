import { o1, oLogN, oN, oNLogN, oNSquared, o2N, oNFactorial } from '@/routes/big-o';

/**
 * Maps Big-O complexity slugs to their corresponding Wayfinder route functions.
 * This allows us to dynamically generate URLs based on slug names without hardcoding paths.
 */
export const getBigORoute = (slug: string): string => {
    const routes: Record<string, () => { url: string }> = {
        'o-1': o1,
        'o-log-n': oLogN,
        'o-n': oN,
        'o-n-log-n': oNLogN,
        'o-n-squared': oNSquared,
        'o-2-n': o2N,
        'o-n-factorial': oNFactorial,
    };

    const routeFn = routes[slug];
    if (!routeFn) {
        console.error(`No route found for slug: ${slug}`);
        return '/';
    }

    return routeFn().url;
};
