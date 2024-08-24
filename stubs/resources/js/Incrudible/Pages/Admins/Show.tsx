import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { Admin, FormField, FormRules, PageProps, Resource } from '@/types/incrudible'
import { Head, Link, useForm } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import { useRef } from 'react'

export default function AdminShow({
  auth,
  admin,
  fields,
  rules,
}: PageProps<{ admin: Resource<Admin>; fields: FormField[]; rules: FormRules }>) {
  const { routePrefix } = useIncrudible()

  const { data } = useForm<Admin>(admin.data)

  const formRef = useRef<FormRef<Admin>>(null!)

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Show Admin
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
      <Head title="Admin Show" />

      <IncrudibleForm ref={formRef} fields={fields} rules={rules} data={data} readOnly />

      <div className="rounded-lg border p-2 text-xs sm:p-4">
        <pre>{JSON.stringify(admin, null, 2)}</pre>
      </div>
    </AuthenticatedLayout>
  )
}
