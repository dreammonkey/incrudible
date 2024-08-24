import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { PageProps } from '@/types/incrudible'
import { Head } from '@inertiajs/react'

export default function Settings({ auth }: PageProps<{}>) {
  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Settings</h2>}
    >
      <Head title="Settings" />

      <div className="py-12">
        <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
          <div className="bg-white p-4 shadow dark:bg-gray-800 sm:rounded-lg sm:p-8">Secure Settings go here...</div>
        </div>
      </div>
    </AuthenticatedLayout>
  )
}
