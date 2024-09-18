import { PageProps } from "@/types/incrudible";
import { usePage } from "@inertiajs/react";

export const useIncrudible = () => {
    return usePage<PageProps>().props.incrudible;
};
