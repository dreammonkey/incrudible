import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { BelongsToMany } from '@/Incrudible/Components/Relations/BelongsToMany'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Role, FormField, FormRules, PageProps, Resource, CrudRelation } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

export default function RoleEdit({
  auth,
  role,
  fields,
  rules,
  relations,
}: PageProps<{ role: Resource<Role>; fields: FormField[]; rules: FormRules; relations: CrudRelation<any>[] }>) {
  // console.log({ role })

  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { setData, put, data, recentlySuccessful } = useForm<Role>(role.data)

  const formRef = useRef<FormRef<Role>>(null!)

  const onSubmit = (data: Role) => {
    // console.log({ data })

    put(route(`${routePrefix}.roles.update`, role.data.id), {
      onSuccess: () => {
        console.log('Role updated successfully')
        formRef.current?.reset(data)
      },
      onError: (error) => {
        console.error('Error updating role', error)
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin.data}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Role
          </h2>
          <Link
            href={route(`${routePrefix}.roles.index`)}
            className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'ml-auto')}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            &nbsp;Back
          </Link>
        </>
      }
    >
      <Head title="Role Edit" />

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

        {/* <pre className="text-xs">{JSON.stringify(role, null, 2)}</pre> */}
      </div>

      {relations?.map((relation) => {
        switch (relation.type) {
          case 'BelongsToMany':
            return (
              <BelongsToMany
                key={relation.name}
                relation={relation}
                onChange={(value) =>
                  setData({
                    ...data,
                    [relation.name]: value,
                  })
                }
              />
            )
          default:
            return null
        }
      })}

      {recentlySuccessful && (
        <div className="flex items-center rounded-xl border px-4 py-3 text-sm">
          <ThumbsUp className="mr-2 inline-block size-4 text-green-800" />
          Role updated successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
