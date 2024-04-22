import { useEffect, FormEventHandler } from "react";
import GuestLayout from "@/Incrudible/Layouts/GuestLayout";
import InputError from "@/Incrudible/Components/InputError";
import { Head, useForm, usePage } from "@inertiajs/react";
import { PageProps } from "@/types";
import { Button } from "@/Incrudible/ui/button";
import { Input } from "@/Incrudible/ui/input";
import { Label } from "@/Incrudible/ui/label";

export default function ResetPassword({
    token,
    email,
}: Readonly<{
    token: string;
    email: string;
}>) {
    const { routePrefix } = usePage<PageProps>().props.incrudible;

    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: "",
        password_confirmation: "",
    });

    useEffect(() => {
        return () => {
            reset("password", "password_confirmation");
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route(`${routePrefix}.auth.password.store`));
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            <form onSubmit={submit}>
                <div>
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
                        autoComplete="new-password"
                        onChange={(e) => setData("password", e.target.value)}
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4">
                    <Label htmlFor="password_confirmation">
                        Confirm Password
                    </Label>

                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData("password_confirmation", e.target.value)
                        }
                    />

                    <InputError
                        message={errors.password_confirmation}
                        className="mt-2"
                    />
                </div>

                <div className="flex items-center justify-end mt-4">
                    <Button className="ms-4" disabled={processing}>
                        Reset Password
                    </Button>
                </div>
            </form>
        </GuestLayout>
    );
}
