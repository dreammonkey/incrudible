import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { useToast } from '@/Incrudible/Hooks/use-toast'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import {
  Permission,
  InputField,
  FormRules,
  PageProps,
} from '@/types/incrudible'
import { Head, Link, router } from '@inertiajs/react'
import { useMutation } from '@tanstack/react-query'
import { ArrowLeft } from 'lucide-react'
import { useRef } from 'react'

export default function PermissionCreate({
  auth,

  fields,
  rules,
}: PageProps<{
  fields: InputField[]
  rules: FormRules
}>) {
  const { routePrefix } = useIncrudible()
  const { toast } = useToast()
  const formRef = useRef<FormRef<Permission>>(null!)

  const { mutate, status } = useMutation({
    mutationFn: (data: Permission) => {
      /** Inertia js router.* does not support async requests */
      return new Promise<void>((resolve, reject) => {
        router.post(route(`${routePrefix}.permissions.store`, []), data, {
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
    mutate(data, {
      onSuccess: () => {
        formRef.current?.reset(data)
        toast({
          title: 'Permission created successfully',
        })
      },
      onError: (error) => {
        toast({
          title: 'Error creating permission',
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
            Create Permission
          </h2>
          <Link
            href={route(`${routePrefix}.permissions.index`, [])}
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
      <Head title="Create Permission" />

      <div className="grid gap-y-2 rounded-lg border p-4">
        <IncrudibleForm
          ref={formRef}
          fields={fields}
          rules={rules}
          initialData={fields.reduce((acc, field) => {
            return { ...acc, [field.name]: '' }
          }, {} as Permission)}
          onFormSubmit={onSubmit}
          isProcessing={status === 'pending'}
          className=""
        />
      </div>
    </AuthenticatedLayout>
  )
}
