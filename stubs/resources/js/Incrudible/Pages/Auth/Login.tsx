import { useEffect, FormEventHandler } from "react";
import GuestLayout from "@/Incrudible/Layouts/GuestLayout";
import InputError from "@/Incrudible/Components/InputError";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import { PageProps } from "@/types";
import { Button } from "@/Incrudible/ui/button";
import { Input } from "@/Incrudible/ui/input";
import { Checkbox } from "@/Incrudible/ui/checkbox";
import { Label } from "@/Incrudible/ui/label";

export default function Login({
    status,
    canResetPassword,
}: Readonly<{
    status?: string;
    canResetPassword: boolean;
}>) {
    const { routePrefix } = usePage<PageProps>().props.incrudible;

    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        console.log(data);

        post(route(`${routePrefix}.auth.login`));
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 font-medium text-sm text-green-600">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div className="bg-blue">
                    <Label htmlFor="email">Email</Label>

                    <Input
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        autoComplete="username"
                        onChange={(e) => setData("email", e.target.value)}
                    />

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <Label htmlFor="password">Password</Label>

                    <Input
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        autoComplete="current-password"
                        onChange={(e) => setData("password", e.target.value)}
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="flex space-x-2 mt-4">
                    <Checkbox
                        id="remember"
                        checked={data.remember}
                        onCheckedChange={(checked) =>
                            setData("remember", checked ? true : false)
                        }
                    />
                    <label
                        htmlFor="remember"
                        className="text-sm pt-1 font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                        Remember me
                    </label>
                </div>

                <div className="flex items-center justify-end mt-4">
                    {canResetPassword && (
                        <Link
                            href={route(`${routePrefix}.auth.password.request`)}
                            className="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            Forgot your password?
                        </Link>
                    )}

                    <Link
                        className="mr-auto text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        href={route(`${routePrefix}.auth.password.request`)}
                    >
                        Forgot password ?
                    </Link>

                    <Button className="" disabled={processing}>
                        Log in
                    </Button>
                </div>
            </form>
        </GuestLayout>
    );
}
