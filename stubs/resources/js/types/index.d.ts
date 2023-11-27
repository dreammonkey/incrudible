export interface Admin {
    id: number;
    username: string;
    email: string;
    email_verified_at: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T &
    Readonly<{
        auth: {
            admin: Admin;
        };
        incrudible: {
            routePrefix: string;
        };
    }>;
