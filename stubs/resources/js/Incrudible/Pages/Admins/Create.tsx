import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Admin, FormField, FormRules, PageProps } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

// import { laravelFormRulesToZodSchema } from '@/lib/utils'

export default function AdminCreate({ auth, fields, rules }: PageProps<{ fields: FormField[]; rules: FormRules }>) {
  // console.log({ auth, fields, rules })
  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { setData, post, data, recentlySuccessful } = useForm<Admin>(
    fields.reduce((acc, field) => {
      return { ...acc, [field.name]: '' }
    }, {} as Admin),
  )
  // console.log({ data })

  const formRef = useRef<FormRef<Admin>>(null!)

  const onSubmit = (data: Admin) => {
    // console.log({ data })

    post(route(`${routePrefix}.admins.store`), {
      onSuccess: () => {
        console.log('Admin created successfully')
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
            Create Admin
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
      <Head title="Create Admin" />

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
        <div className="flex items-center rounded-lg border px-4 py-3 text-sm">
          <ThumbsUp className="mr-2 inline-block size-4 text-green-800" />
          Admin created successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
