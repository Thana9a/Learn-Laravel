import { DefineComponent } from 'vue';

declare module '*.vue' {
    const component: DefineComponent<{}, {}, any>;
    export default component;
}

declare global {
    function route(name?: string, params?: any, absolute?: boolean, config?: any): any;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof route;
    }
}

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: {
                id: number;
                name: string;
                email: string;
                email_verified_at: string | null;
                created_at: string;
                updated_at: string;
            };
        };
        [key: string]: any;
    }
}

export {};
