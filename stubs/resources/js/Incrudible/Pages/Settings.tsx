import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { PageProps } from '@/types/incrudible'
import { Head } from '@inertiajs/react'

export default function Settings({ auth }: PageProps<{}>) {
  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Settings</h2>}
    >
      <Head title="Settings" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto space-y-6">
          <div className="shadow sm:rounded-lg">Secure settings go here... </div>
        </div>
      </div>
    </AuthenticatedLayout>
  )
}
