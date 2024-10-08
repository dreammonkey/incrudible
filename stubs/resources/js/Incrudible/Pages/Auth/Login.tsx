import InputError from '@/Incrudible/Components/InputError'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import GuestLayout from '@/Incrudible/Layouts/GuestLayout'
import { Button } from '@/Incrudible/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Incrudible/ui/card'
import { Checkbox } from '@/Incrudible/ui/checkbox'
import { Input } from '@/Incrudible/ui/input'
import { Label } from '@/Incrudible/ui/label'
import { Head, Link, useForm } from '@inertiajs/react'
import { FormEventHandler, useEffect } from 'react'

export default function Login({
  status,
  canResetPassword,
  canRegister = true,
}: Readonly<{
  status?: string
  canResetPassword: boolean
  canRegister?: boolean
}>) {
  const { routePrefix } = useIncrudible()

  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  })

  useEffect(() => {
    return () => {
      reset('password')
    }
  }, [])

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route(`${routePrefix}.auth.login`))
  }

  return (
    <GuestLayout>
      <Head title="Log in" />
      <Card className="mx-auto max-w-sm">
        <CardHeader>
          <CardTitle className="text-2xl">Login</CardTitle>
          <CardDescription>Enter your email below to login to your account</CardDescription>
          {status && <div className="mb-4 text-sm font-medium text-green-600">{status}</div>}
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
                  required
                  onChange={(e) => setData('email', e.target.value)}
                />

                <InputError message={errors.email} className="mt-2" />
              </div>

              <div className="grid gap-2">
                <div className="flex items-center">
                  <Label htmlFor="password">Password</Label>
                  <Link
                    disabled={!canResetPassword}
                    href={route(`${routePrefix}.auth.password.request`)}
                    className="ml-auto inline-block text-sm underline"
                  >
                    Forgot your password?
                  </Link>
                </div>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  value={data.password}
                  autoComplete="current-password"
                  required
                  onChange={(e) => setData('password', e.target.value)}
                />

                <InputError message={errors.password} className="mt-2" />
              </div>

              <div className="grid gap-2">
                <div className="flex items-center space-x-2">
                  <Checkbox
                    id="remember"
                    checked={data.remember}
                    onCheckedChange={(checked) => setData('remember', checked ? true : false)}
                  />
                  <Label
                    htmlFor="remember"
                    className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    Remember me
                  </Label>
                </div>
              </div>

              <div className="mt-4 flex items-center justify-end">
                <Button className="" disabled={processing}>
                  Log in
                </Button>
              </div>
            </div>
          </form>
          {canRegister && (
            <div className="mt-4 text-center text-sm">
              Don&apos;t have an account?{' '}
              <Link
                href="#"
                // href={route(`${routePrefix}.register`)}
                className="underline"
              >
                Sign up
              </Link>
            </div>
          )}
        </CardContent>
      </Card>
    </GuestLayout>
  )
}
