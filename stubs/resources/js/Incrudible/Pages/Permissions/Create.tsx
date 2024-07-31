import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Permission, FormMetaData, PageProps } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, ArrowLeftSquare, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

// import { laravelFormRulesToZodSchema } from '@/lib/utils'

export default function PermissionCreate({ auth, permission, metadata }: PageProps<{ permission: any; metadata: FormMetaData }>) {
  // console.log({ auth, permission, metadata })
  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { setData, post, data, recentlySuccessful } = useForm<Permission>(
    metadata.fields.reduce((acc, field) => {
      return { ...acc, [field.name]: '' }
    }, {} as Permission),
  )
  // console.log({ data })

  const formRef = useRef<FormRef<Permission>>(null!)

  const onSubmit = (data: Permission) => {
    // console.log({ data })

    // TODO: mutation instead of post ??

    // POST `${routePrefix}/permissions`}`
    post(route(`${routePrefix}.permissions.store`), {
      onSuccess: () => {
        console.log('Permission created successfully')
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
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Create Permission</h2>
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
      <Head title="Create Permission" />

      <div className="grid gap-y-2 rounded-lg border p-4">
        <IncrudibleForm
          ref={formRef}
          metadata={metadata}
          data={data}
          onFormSubmit={onSubmit}
          onChange={setData}
          className=""
        />
      </div>

      {recentlySuccessful && (
        <div className="relative justify-center rounded-lg border px-4 py-3 text-sm">
          <ThumbsUp className="mr-2 inline-block h-5 w-5 text-green-800" />
          Permission created successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
