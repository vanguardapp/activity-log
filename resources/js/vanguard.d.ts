/**
 * The pieces of the host application a plugin bundle links against.
 *
 * These are externalised at build time onto the global Vanguard runtime, so
 * they are declared here rather than installed as dependencies.
 *
 * This file must stay a global script: adding a top level import or export
 * would turn `declare module` into an augmentation of a module that does not
 * exist on disk, and the declarations below would stop resolving.
 */
declare module '@vanguard/ui' {
    type UiComponent = import('vue').DefineComponent<Record<string, unknown>, object, unknown>;

    export const AppLayout: UiComponent;
    export const Button: UiComponent;
    export const Checkbox: UiComponent;
    export const Dialog: UiComponent;
    export const Input: UiComponent;
    export const LineChart: UiComponent;
    export const Menu: UiComponent;
    export const MenuItem: UiComponent;
    export const MenuSeparator: UiComponent;
    export const Message: UiComponent;
    export const Pagination: UiComponent;
    export const PasswordInput: UiComponent;
    export const Select: UiComponent;
    export const Switch: UiComponent;
    export const Tabs: UiComponent;
    export const Textarea: UiComponent;
    export const Tooltip: UiComponent;

    export function useTranslations(): {
        __(key: string, replacements?: Record<string, string | number>): string;
    };
    export function usePermissions(): {
        can(...permissions: string[]): boolean;
        canAny(...permissions: string[]): boolean;
    };
    export function useConfirmDelete(): {
        confirmDelete(options: {
            url: string;
            message: string;
            title?: string;
            confirmLabel?: string;
            method?: 'delete' | 'put' | 'post';
            onSuccess?: () => void;
        }): void;
    };
}

interface VanguardRegistrar {
    page(name: string, component: unknown): void;
    slot(name: string, component: unknown): void;
    widget(name: string, component: unknown): void;
}

interface Window {
    Vanguard: {
        plugin(slug: string, callback: (registrar: VanguardRegistrar) => void): void;
    };
}

/** Provided by the host's ZiggyVue plugin. */
declare const route: (name: string, params?: unknown, absolute?: boolean) => string;
