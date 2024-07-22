import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { PageProps } from '@/types/incrudible'
import { Head } from '@inertiajs/react'
import UpdatePasswordForm from './Partials/UpdatePasswordForm'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm'

export default function Edit({
  auth,
  mustVerifyEmail,
  status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
  console.log(auth)
  return (
    <AuthenticatedLayout
      admin={auth.admin.data}
      header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Profile</h2>}
    >
      <Head title="Profile" />

      <div className="rounded-lg border p-4">
        <UpdateProfileInformationForm mustVerifyEmail={mustVerifyEmail} status={status} className="max-w-xl" />
      </div>

      {/* <div className="p-4 sm:p-8 shadow sm:rounded-lg"> */}
      <div className="rounded-lg border p-4">
        <UpdatePasswordForm className="max-w-xl" />
      </div>
    </AuthenticatedLayout>
  )
}
