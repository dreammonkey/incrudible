import InputError from '@/Incrudible/Components/InputError'
import GuestLayout from '@/Incrudible/Layouts/GuestLayout'
import { Button } from '@/Incrudible/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Incrudible/ui/card'
import { Input } from '@/Incrudible/ui/input'
import { Label } from '@/Incrudible/ui/label'
import { PageProps } from '@/types/incrudible'
import { Head, useForm, usePage } from '@inertiajs/react'
import { FormEventHandler, useEffect } from 'react'

export default function ResetPassword({
  token,
  email,
}: Readonly<{
  token: string
  email: string
}>) {
  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { data, setData, post, processing, errors, reset } = useForm({
    token: token,
    email: email,
    password: '',
    password_confirmation: '',
  })

  useEffect(() => {
    return () => {
      reset('password', 'password_confirmation')
    }
  }, [])

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route(`${routePrefix}.auth.password.store`))
  }

  return (
    <GuestLayout>
      <Head title="Reset Password" />

      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Reset your password</CardTitle>
          <CardDescription>Reset your password</CardDescription>
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
                  value={data.email}
                  autoComplete="username"
                  onChange={(e) => setData('email', e.target.value)}
                />

                <InputError message={errors.email} className="mt-2" />
              </div>

              <div className="grid gap-2">
                <Label htmlFor="password">Password</Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  value={data.password}
                  autoComplete="new-password"
                  onChange={(e) => setData('password', e.target.value)}
                />

                <InputError message={errors.password} className="mt-2" />
              </div>

              <div className="grid gap-2">
                <Label htmlFor="password_confirmation">Confirm Password</Label>

                <Input
                  id="password_confirmation"
                  type="password"
                  name="password_confirmation"
                  value={data.password_confirmation}
                  autoComplete="new-password"
                  onChange={(e) => setData('password_confirmation', e.target.value)}
                />

                <InputError message={errors.password_confirmation} className="mt-2" />
              </div>

              <Button className="w-full" disabled={processing}>
                Reset Password
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
