import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Admin, FormMetaData, PageProps, Resource } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

export default function AdminEdit({
  auth,
  admin,
  metadata,
}: PageProps<{ admin: Resource<Admin>; metadata: FormMetaData }>) {
  // console.log({ admin })

  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { setData, put, data, recentlySuccessful } = useForm<Admin>(admin.data)

  const formRef = useRef<FormRef<Admin>>(null!)

  const onSubmit = (data: Admin) => {
    // console.log({ data })

    // TODO: mutation instead of put ??

    // PUT `${routePrefix}/admins/${admin.data.id}`
    put(route(`${routePrefix}.admins.update`, admin.data.id), {
      onSuccess: () => {
        console.log('Admin updated successfully')
        formRef.current?.reset(data)
      },
      onError: (error) => {
        console.error('Error updating admin', error)
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin.data}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Admin
          </h2>
          <Link
            href={route(`${routePrefix}.admins.index`)}
            className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'ml-auto')}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            &nbsp;Back
          </Link>
        </>
      }
    >
      <Head title="Admin Edit" />

      <div className="grid gap-y-2 rounded-lg border p-4">
        <IncrudibleForm
          ref={formRef}
          metadata={metadata}
          data={data}
          onFormSubmit={onSubmit}
          onChange={setData}
          className="max-w-xl"
        />
      </div>

      {recentlySuccessful && (
        <div className="relative justify-center rounded-xl border px-4 py-3 text-sm">
          <ThumbsUp className="mr-2 inline-block h-5 w-5 text-green-800" />
          Admin updated successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
