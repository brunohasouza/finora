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

export type CategoryResponse = {
    current_page: number;
    data: Category[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: { active: boolean; label: string; url: string | null; page: number | null }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
};

export enum CATEGORY_TYPE {
    INCOME = 'income',
    EXPENSE = 'expense',
}
