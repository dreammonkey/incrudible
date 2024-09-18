import InputError from '@/Incrudible/Components/InputError'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import GuestLayout from '@/Incrudible/Layouts/GuestLayout'
import { Button } from '@/Incrudible/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Incrudible/ui/card'
import { Input } from '@/Incrudible/ui/input'
import { Label } from '@/Incrudible/ui/label'
import { Head, Link, useForm } from '@inertiajs/react'
import { FormEventHandler } from 'react'

export default function ForgotPassword({ status = '' }: Readonly<{ status?: string }>) {
  const { routePrefix } = useIncrudible()

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

              <div className="mt-4 flex items-center justify-between">
                <Link
                  href={route(`${routePrefix}.auth.login`)}
                  className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
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
