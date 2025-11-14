import { PageProps as InertiaPageProps } from '@inertiajs/core';

export type User = {
    email: string;
    name: string;
    id: number;
};

export interface PageProps extends InertiaPageProps {
    auth: {
        user?: User;
    };
}
