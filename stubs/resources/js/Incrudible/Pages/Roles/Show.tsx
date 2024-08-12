import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { cn } from '@/lib/utils'
import { FormMetaData, PageProps } from '@/types/incrudible'
import { Head, Link, usePage } from '@inertiajs/react'
import { ArrowLeft } from 'lucide-react'

export default function RoleShow({ auth, role, metadata }: PageProps<{ role: any; metadata: FormMetaData }>) {
  // console.log(role)

  const { routePrefix } = usePage<PageProps>().props.incrudible
  return (
    <AuthenticatedLayout
      admin={auth.admin.data}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Show Role</h2>
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
      <Head title="Role Show" />

      <div className="rounded-lg border p-4 text-sm sm:p-8">
        <pre>{JSON.stringify(role, null, 2)}</pre>
      </div>

      {/* <IncrudibleForm metadata={metadata} data={role} /> */}
    </AuthenticatedLayout>
  )
}
