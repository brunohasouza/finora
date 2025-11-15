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

export type Category = {
    id: number | string;
    name: string;
    color: string;
    type: CATEGORY_TYPE;
    created_at: Date | string;
    updated_at: Date | string;
};

export enum CATEGORY_TYPE {
    INCOME = 'income',
    EXPENSE = 'expense',
}
