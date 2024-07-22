import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { PageProps } from '@/types/incrudible'
import { Head } from '@inertiajs/react'
import { Check } from 'lucide-react'

export default function Dashboard({ auth }: PageProps) {
  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>}
    >
      <Head title="Dashboard" />

      <div className="bg-muted/40 overflow-hidden shadow-sm sm:rounded-lg">
        <div className="p-6 flex items-center">
          <Check className="mr-2 w-4 h-4" /> You're logged in!
        </div>
      </div>
    </AuthenticatedLayout>
  )
}
