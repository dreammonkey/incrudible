import { CrudRelations } from '@/Incrudible/Components/CrudRelations/CrudRelations'
import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { useToast } from '@/Incrudible/Hooks/use-toast'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import {
  Admin,
  CrudRelation,
  CrudResource,
  InputField,
  FormRules,
  PageProps,
  Resource,
} from '@/types/incrudible'
import { Head, Link, router } from '@inertiajs/react'
import { useMutation } from '@tanstack/react-query'
import { ArrowLeft } from 'lucide-react'
import { useRef } from 'react'

export default function AdminEdit({
  auth,
  admin,
  fields,
  rules,
  relations,
}: PageProps<{
  admin: Resource<Admin>
  fields: InputField[]
  rules: FormRules
  relations: CrudRelation<CrudResource>[]
}>) {
  const { routePrefix } = useIncrudible()

  const { toast } = useToast()

  const formRef = useRef<FormRef<Admin>>(null!)

  const { mutate, status } = useMutation({
    mutationFn: (data: Admin) => {
      return new Promise<void>((resolve, reject) => {
        router.put(
          route(`${routePrefix}.admins.update`, [admin.data.id]),
          data,
          {
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
          },
        )
      })
    },
  })

  const onSubmit = (data: Admin) => {
    // console.log({ data })

    mutate(data, {
      onSuccess: () => {
        formRef.current?.reset(data)
        toast({
          title: 'Admin updated successfully',
        })
      },
      onError: (error) => {
        toast({
          title: 'Error updating admin',
          description: 'Please check the form for errors',
          variant: 'destructive',
        })
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Admin
          </h2>
          <Link
            href={route(`${routePrefix}.admins.index`, [])}
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
      <Head title="Admin Edit" />

      <div className="grid gap-y-2 rounded-lg border p-4">
        <IncrudibleForm
          ref={formRef}
          fields={fields}
          rules={rules}
          initialData={admin.data}
          onFormSubmit={onSubmit}
          isProcessing={status === 'pending'}
          className=""
        />
      </div>

      <CrudRelations resource={admin} relations={relations} />
    </AuthenticatedLayout>
  )
}
