import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { InputField, FormRules, PageProps, Role } from '@/types/incrudible'
import { Head, Link, useForm } from '@inertiajs/react'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

export default function RoleCreate({
  auth,
  fields,
  rules,
}: PageProps<{ fields: InputField[]; rules: FormRules }>) {
  const { routePrefix } = useIncrudible()

  const { setData, post, data, recentlySuccessful } = useForm<Role>(
    fields.reduce((acc, field) => {
      return { ...acc, [field.name]: '' }
    }, {} as Role),
  )

  const formRef = useRef<FormRef<Role>>(null!)

  const onSubmit = (data: Role) => {
    post(route(`${routePrefix}.roles.store`), {
      onSuccess: () => {
        console.log('Role created successfully')
        formRef.current?.reset(data)
      },
      onError: (error) => {
        console.error('Error updating role', error)
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Create Role
          </h2>
          <Link
            href={route(`${routePrefix}.roles.index`)}
            className={cn(
              buttonVariants({ variant: 'outline', size: 'sm' }),
              'ml-auto',
            )}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            &nbsp;Back
          </Link>
        </>
      }
    >
      <Head title="Create Role" />

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
          Role created successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
