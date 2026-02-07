import { PageProps as InertiaPageProps } from '@inertiajs/core';

export type User = {
    email: string;
    first_name: string;
    last_name: string;
    full_name?: string;
    id: number;
};

export type PaginatedResponse<T> = {
    current_page: number;
    data: T[];
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

export type CategoryResponse = PaginatedResponse<Category>;

export enum CATEGORY_TYPE {
    INCOME = 'income',
    EXPENSE = 'expense',
}

export type Bank = {
    id: number | string;
    name: string;
    created_at: Date | string;
    updated_at: Date | string;
    shortname: string;
    code: string;
};

export type Account = {
    id: number | string;
    name: string;
    bank: Bank;
    balance: number;
    created_at: Date | string;
    updated_at: Date | string;
};

export type AccountResponse = Account[];

export type Transaction = {
    id: number | string;
    description: string;
    amount: number;
    date: string;
    type: CATEGORY_TYPE;
    category: Category;
    wallet: Account;
    created_at: Date | string;
    updated_at: Date | string;
};

export type TransactionResponse = PaginatedResponse<Transaction>;

export type Balance = {
    total: number;
    expenses: number;
    incomes: number;
};
