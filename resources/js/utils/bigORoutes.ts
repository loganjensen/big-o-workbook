import bigO from '@/routes/big-o';

/**
 * Maps Big-O complexity slugs to their corresponding Wayfinder route functions.
 * This allows us to dynamically generate URLs based on slug names without hardcoding paths.
 */
export const getBigORoute = (slug: string): string => {
    const routes: Record<string, () => { url: string }> = {
        'o-1': bigO.o1,
        'o-log-n': bigO.oLogN,
        'o-n': bigO.oN,
        'o-n-log-n': bigO.oNLogN,
        'o-n-squared': bigO.oNSquared,
        'o-2-n': bigO.o2N,
        'o-n-factorial': bigO.oNFactorial,
    };

    const routeFn = routes[slug];
    if (!routeFn) {
        console.error(`No route found for slug: ${slug}`);
        return '/';
    }

    return routeFn().url;
};
