import GuestLayout from '@/Incrudible/Layouts/GuestLayout'
import { Button } from '@/Incrudible/ui/button'
import AuthenticatedSessionController from '@/actions/App/Incrudible/Http/Controllers/Auth/AuthenticatedSessionController'
import EmailVerificationNotificationController from '@/actions/App/Incrudible/Http/Controllers/Auth/EmailVerificationNotificationController'
import { Head, Link, useForm } from '@inertiajs/react'
import { FormEventHandler } from 'react'

export default function VerifyEmail({ status }: Readonly<{ status?: string }>) {
  const { post, processing } = useForm({})

  const submit: FormEventHandler = (e) => {
    e.preventDefault()

    post(EmailVerificationNotificationController.store().url)
  }

  return (
    <GuestLayout>
      <Head title="Email Verification" />

      <div className="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we
        just emailed to you? If you didn't receive the email, we will gladly send you another.
      </div>

      {status === 'verification-link-sent' && (
        <div className="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
          A new verification link has been sent to the email address you provided during registration.
        </div>
      )}

      <form onSubmit={submit}>
        <div className="mt-4 flex items-center justify-between">
          <Button disabled={processing}>Resend Verification Email</Button>

          <Link
            href={AuthenticatedSessionController.destroy()}
            as="button"
            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
          >
            Log Out
          </Link>
        </div>
      </form>
    </GuestLayout>
  )
}
