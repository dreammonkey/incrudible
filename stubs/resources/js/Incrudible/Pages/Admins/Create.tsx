import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { useRecentlySuccessful } from '@/Incrudible/Hooks/use-recently-successful'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import {
  Admin,
  InputField,
  FormRules,
  PageProps,
  Resource,
} from '@/types/incrudible'
import { Head, Link, router, usePage } from '@inertiajs/react'
import { useMutation } from '@tanstack/react-query'
import { ArrowLeft, ThumbsUp } from 'lucide-react'
import { useRef } from 'react'

export default function AdminCreate({
  auth,
  fields,
  rules,
}: PageProps<{
  fields: InputField[]
  rules: FormRules
}>) {
  // console.log('AdminCreate', fields, rules)
  //
  const { routePrefix } = useIncrudible()
  const { recentlySuccessful, triggerSuccess } = useRecentlySuccessful()

  const formRef = useRef<FormRef<Admin>>(null!)

  const { mutate, status } = useMutation({
    mutationFn: (data: Admin) => {
      /** Inertia js router.* does not support async requests */
      return new Promise<void>((resolve, reject) => {
        router.post(route(`${routePrefix}.admins.store`, []), data, {
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

  const onSubmit = (data: Admin) => {
    mutate(data, {
      onSuccess: () => {
        // console.log('Admin created successfully :)')
        formRef.current?.reset(data)
        triggerSuccess()
      },
      onError: (error) => {
        console.error('Error updating Admin', error)
      },
    })
  }

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Create Admin
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
      <Head title="Create Admin" />

      <div className="grid gap-y-2 rounded-lg border p-4">
        <IncrudibleForm
          ref={formRef}
          fields={fields}
          rules={rules}
          initialData={fields.reduce((acc, field) => {
            return { ...acc, [field.name]: '' }
          }, {} as Admin)}
          onFormSubmit={onSubmit}
          isProcessing={status === 'pending'}
          className=""
        />
      </div>

      {recentlySuccessful && (
        <div className="flex items-center rounded-lg border px-4 py-3 text-sm">
          <ThumbsUp className="mr-4 inline-block size-4 text-green-800" />
          Admin created successfully
        </div>
      )}
    </AuthenticatedLayout>
  )
}
