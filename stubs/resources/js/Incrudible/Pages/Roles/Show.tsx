import IncrudibleForm, { FormRef } from '@/Incrudible/Components/IncrudibleForm'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { 
  Role, 
  FormRules,
  InputField,
  PageProps,
  Resource,
} from '@/types/incrudible'
import { Head, Link } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'
import { useRef } from 'react'

export default function RoleShow({ 
  auth,
  
  role,
  fields,
  rules,
}: PageProps<{ 
  
  role: Resource<Role> 
  fields: InputField[] 
  rules: FormRules
}>) {
  const { routePrefix } = useIncrudible()

  const formRef = useRef<FormRef<Role>>(null!)

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Show Role
          </h2>
          <Link
            href={route(`${routePrefix}.roles.index`, [])}
            className={cn(
              buttonVariants({ variant: 'outline', size: 'sm' }), 
              'ml-auto'
            )}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            &nbsp;Back
          </Link>
        </>
      }
    >
      <Head title="Role Show" />

      <IncrudibleForm
        ref={formRef}
        fields={fields}
        rules={rules} 
        initialData={role.data} 
        readOnly
      />
      
      {/* <div className="rounded-lg border p-2 text-xs sm:p-4">
        <pre>{JSON.stringify(role, null, 2)}</pre>
      </div> */}
    </AuthenticatedLayout>
  )
}
