import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { BelongsToMany } from '@/Incrudible/Components/Relations/BelongsToMany'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Role, FormMetaData, PageProps, CrudRelation } from '@/types/incrudible'
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

// import { laravelFormRulesToZodSchema } from '@/lib/utils'

export default function RoleCreate({
  auth,
  role,
  metadata,
  relations,
}: PageProps<{ role: any; metadata: FormMetaData; relations: CrudRelation<any>[] }>) {
  console.log({ auth, role, metadata, relations })
  const { routePrefix } = usePage<PageProps>().props.incrudible

  const { setData, post, data, recentlySuccessful } = useForm<Role>(
    metadata.fields.reduce((acc, field) => {
      return { ...acc, [field.name]: '' }
    }, {} as Role),
  )
  // console.log({ data })

  const formRef = useRef<FormRef<Role>>(null!)

  const onSubmit = (data: Role) => {
    // console.log({ data })

    // TODO: mutation instead of post ??

    // POST `${routePrefix}/roles`}`
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
      admin={auth.admin.data}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Create Role
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
      <Head title="Create Role" />

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

      {relations?.map((relation) => {
        switch (relation.type) {
          case 'BelongsToMany':
            return <BelongsToMany key={relation.name} relation={relation} />
          default:
            return null
        }
      })}

      {recentlySuccessful && (
        <div className="relative justify-center rounded-lg border px-4 py-3 text-sm">
          <ThumbsUp className="mr-2 inline-block h-5 w-5 text-green-800" />
          Role created successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
