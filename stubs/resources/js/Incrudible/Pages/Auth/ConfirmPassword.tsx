import { useEffect, FormEventHandler } from "react";
import GuestLayout from "@/Incrudible/Layouts/GuestLayout";
import InputError from "@/Incrudible/Components/InputError";
import InputLabel from "@/Incrudible/Components/InputLabel";
import PrimaryButton from "@/Incrudible/Components/PrimaryButton";
import TextInput from "@/Incrudible/Components/TextInput";
import { Head, useForm, usePage } from "@inertiajs/react";
import { PageProps } from "@/types";

export default function ConfirmPassword() {
    const { routePrefix } = usePage<PageProps>().props.incrudible;

    const { data, setData, post, processing, errors, reset } = useForm({
        password: "",
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route(`${routePrefix}.password.confirm.post`));
    };

    return (
        <GuestLayout>
            <Head title="Confirm Password" />

            <div className="mb-4 text-sm text-gray-600 dark:text-gray-400">
                This is a secure area of the application. Please confirm your
                password before continuing.
            </div>

            <form onSubmit={submit}>
                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        isFocused={true}
                        onChange={(e) => setData("password", e.target.value)}
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="flex items-center justify-end mt-4">
                    <PrimaryButton className="ms-4" disabled={processing}>
                        Confirm
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
