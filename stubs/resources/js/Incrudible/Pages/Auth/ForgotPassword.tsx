import InputError from '@/Incrudible/Components/InputError'
import GuestLayout from '@/Incrudible/Layouts/GuestLayout'
import { Button } from '@/Incrudible/ui/button'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Incrudible/ui/card'
import { Input } from '@/Incrudible/ui/input'
import { Label } from '@/Incrudible/ui/label'
import { PageProps } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { FormEventHandler } from 'react'

export default function ForgotPassword({ status = '' }: Readonly<{ status?: string }>) {
  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { data, setData, post, processing, errors } = useForm({
    email: '',
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route(`${routePrefix}.auth.password.email`))
  }

  const disabled = status?.length > 0

  return (
    <GuestLayout>
      <Head title="Request new password" />

      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Request new password</CardTitle>
          <CardDescription>Enter your email to receive a password reset link.</CardDescription>
          {status && <CardDescription className="text-green-400">{status}</CardDescription>}
        </CardHeader>
        <CardContent>
          <form onSubmit={submit}>
            <div className="grid gap-4">
              <div className="grid gap-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  type="email"
                  name="email"
                  disabled={disabled}
                  value={data.email}
                  autoComplete="username"
                  onChange={(e) => setData('email', e.target.value)}
                />

                <InputError message={errors.email} className="mt-2" />
              </div>

              <div className="flex items-center justify-between mt-4">
                <Link
                  href={route(`${routePrefix}.auth.login`)}
                  className="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                  Back to login
                </Link>

                <Button className="" disabled={processing || disabled}>
                  Request Password Reset
                </Button>
              </div>
            </div>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
