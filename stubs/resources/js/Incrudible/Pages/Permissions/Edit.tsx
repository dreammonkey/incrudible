import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Permission, FormField, FormRules, PageProps, Resource } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

export default function PermissionEdit({
  auth,
  permission,
  fields,
  rules,
}: PageProps<{ permission: Resource<Permission>; fields: FormField[]; rules: FormRules }>) {
  // console.log({ permission })

  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { setData, put, data, recentlySuccessful } = useForm<Permission>(permission.data)

  const formRef = useRef<FormRef<Permission>>(null!)

  const onSubmit = (data: Permission) => {
    // console.log({ data })

    put(route(`${routePrefix}.permissions.update`, permission.data.id), {
      onSuccess: () => {
        console.log('Permission updated successfully')
        formRef.current?.reset(data)
      },
      onError: (error) => {
        console.error('Error updating permission', error)
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin.data}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Edit Permission</h2>
          <Link
            href={route(`${routePrefix}.permissions.index`)}
            className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'ml-auto')}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            &nbsp;Back
          </Link>
        </>
      }
    >
      <Head title="Permission Edit" />

      <div className="grid gap-y-2 rounded-lg border p-4">
        <IncrudibleForm
          ref={formRef}
          fields={fields}
          rules={rules}
          data={data}
          onFormSubmit={onSubmit}
          onChange={setData}
          className=""
        />
      </div>

      {recentlySuccessful && (
        <div className="flex items-center rounded-xl border px-4 py-3 text-sm">
          <ThumbsUp className="mr-2 inline-block size-4 text-green-800" />
          Permission updated successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
