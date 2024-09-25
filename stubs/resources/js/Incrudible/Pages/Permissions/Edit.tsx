import { CrudRelations } from '@/Incrudible/Components/CrudRelations/CrudRelations'
import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { useRecentlySuccessful } from '@/Incrudible/Hooks/use-recently-successful'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Permission, CrudRelation, CrudResource, InputField, FormRules, PageProps, Resource } from '@/types/incrudible'
import { Head, Link, router } from '@inertiajs/react'
import { useMutation } from '@tanstack/react-query'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

export default function PermissionEdit({
  auth,
  
  permission,
  fields,
  rules,
  relations,
}: PageProps<{
  
  permission: Resource<Permission>
  fields: InputField[]
  rules: FormRules
  relations: CrudRelation<CrudResource>[]
}>) {
  const { routePrefix } = useIncrudible()
  const { recentlySuccessful, triggerSuccess } = useRecentlySuccessful()

  // const { setData, put, data, recentlySuccessful } = useForm<Permission>(permission.data)

  const formRef = useRef<FormRef<Permission>>(null!)

  const { mutate, status } = useMutation({
    mutationFn: (data: Permission) => {
      return new Promise<void>((resolve, reject) => {
        router.put(route(`${routePrefix}.permissions.update`, [permission.data.id]), data, {
          onSuccess: () => {
            resolve()
          },
          onError: (error) => {
            for (let key in error) {
              // console.error(key, error[key])
              formRef.current?.setError(key, {
                type: 'server',
                message: error[key],
              })
            }
            reject(error)
          },
        })
      })
    },
  })

  const onSubmit = (data: Permission) => {
    // console.log({ data })

    /*put(route(`${routePrefix}.permissions.update`, [permission.data.id]), {
      onSuccess: () => {
        formRef.current?.reset(data)
      },
      onError: (error) => {
        console.error('Error updating permission', error)
      },
    })*/

   mutate(data, {
      onSuccess: () => {
        // console.log('Permission updated successfully :)')
        formRef.current?.reset(data)
        triggerSuccess()
      },
      onError: (error) => {
        console.error('Error updating permission', error)
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Edit Permission</h2>
          <Link
            href={route(`${routePrefix}.permissions.index`, [])}
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
          initialData={permission.data}
          onFormSubmit={onSubmit}
          isProcessing={status === 'pending'}
          className=""
        />
      </div>

      {recentlySuccessful && (
        <div className="flex items-center rounded-xl border px-4 py-3 text-sm">
          <ThumbsUp className="mr-4 inline-block size-4 text-green-800" />
          Permission updated successfully
        </div>
      )}

      <CrudRelations resource={permission} relations={relations} />
    </AuthenticatedLayout>
  )
}
