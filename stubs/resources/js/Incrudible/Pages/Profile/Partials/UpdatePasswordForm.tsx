import InputError from '@/Incrudible/Components/InputError'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { Button } from '@/Incrudible/ui/button'
import { Input } from '@/Incrudible/ui/input'
import { Label } from '@/Incrudible/ui/label'
import { Transition } from '@headlessui/react'
import { useForm } from '@inertiajs/react'
import { FormEventHandler, useRef } from 'react'

export default function UpdatePasswordForm({
  className = '',
}: Readonly<{
  className?: string
}>) {
  const { routePrefix } = useIncrudible()

  const passwordInput = useRef<HTMLInputElement>(null!)
  const currentPasswordInput = useRef<HTMLInputElement>(null!)

  const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  })

  const updatePassword: FormEventHandler = (e) => {
    e.preventDefault()

    put(route(`${routePrefix}.password.update`), {
      preserveScroll: true,
      onSuccess: () => reset(),
      onError: (errors) => {
        if (errors.password) {
          reset('password', 'password_confirmation')
          passwordInput.current?.focus()
        }

        if (errors.current_password) {
          reset('current_password')
          currentPasswordInput.current?.focus()
        }
      },
    })
  }

  return (
    <section className={className}>
      <header>
        <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Update Password</h2>

        <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Ensure your account is using a long, random password to stay secure.
        </p>
      </header>

      <form onSubmit={updatePassword} className="mt-6 space-y-6">
        <div>
          <Label htmlFor="current_password">Current Password</Label>

          <Input
            id="current_password"
            ref={currentPasswordInput}
            type="password"
            name="current_password"
            value={data.current_password}
            autoComplete="current-password"
            onChange={(e) => setData('current_password', e.target.value)}
          />

          <InputError message={errors.current_password} className="mt-2" />
        </div>

        <div>
          <Label htmlFor="password">New Password</Label>

          <Input
            ref={passwordInput}
            id="password"
            type="password"
            name="password"
            value={data.password}
            autoComplete="new-password"
            onChange={(e) => setData('password', e.target.value)}
          />

          <InputError message={errors.password} className="mt-2" />
        </div>

        <div>
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

        <div className="flex items-center gap-4">
          <Button disabled={processing}>Save</Button>

          <Transition
            show={recentlySuccessful}
            enter="transition ease-in-out"
            enterFrom="opacity-0"
            leave="transition ease-in-out"
            leaveTo="opacity-0"
          >
            <p className="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
          </Transition>
        </div>
      </form>
    </section>
  )
}
