/**
 * Ziggy's route() helper, as reachable from a template.
 *
 * Kept apart from vanguard.d.ts because module augmentation only works in a
 * file that is itself a module, and that file has to stay a global script for
 * its ambient `@vanguard/ui` declaration to resolve.
 */
declare module 'vue' {
    interface ComponentCustomProperties {
        route: (name: string, params?: unknown, absolute?: boolean) => string;
    }
}

export {};
