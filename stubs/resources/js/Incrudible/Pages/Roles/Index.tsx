import { getCrudIndex } from '@/Incrudible/Api/services/getCrudIndex'
import { TablePagination } from '@/Incrudible/Components/TablePagination'
import { createColumns } from '@/Incrudible/Helpers/table-helpers'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { DataTable } from '@/Incrudible/ui/data-table'
import { Input } from '@/Incrudible/ui/input'
import { cn } from '@/lib/utils'
import { Role, Filters, PagedResource, PageProps, PagingConfig, TableActionConfig } from '@/types/incrudible'
import { Head, Link, router, usePage } from '@inertiajs/react'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { SortingState } from '@tanstack/react-table'
import { Plus, Search, TriangleAlert } from 'lucide-react'
import { useEffect, useLayoutEffect, useMemo, useState } from 'react'

export default function RoleIndex({
  auth,
  listable,
  paging,
  actions,
}: PageProps<{ listable: string[]; actions: TableActionConfig[]; paging: PagingConfig }>) {
  const props = usePage<PageProps>().props

  const queryClient = useQueryClient()

  const {
    incrudible: { routePrefix },
    ziggy: { query, location },
  } = props

  const params = new URLSearchParams(query)

  const routeKey = 'roles.index'

  const [filters, setFilters] = useState<Filters>({
    page: params.get('page') ? parseInt(params.get('page') as string) : 1,
    perPage: params.get('perPage') ? parseInt(params.get('perPage') as string) : paging.default,
    orderBy: params.get('orderBy') ?? 'created_at',
    orderDir: params.get('orderDir') ?? 'desc',
    search: '',
  })

  // Sync filters state with URL search params
  useLayoutEffect(() => {
    const page = params.get('page')
    if (page && parseInt(page) !== filters.page) {
      setFilters({ ...filters, page: parseInt(page) })
    }
    const perPage = params.get('perPage')
    if (perPage && parseInt(perPage) !== filters.perPage) {
      setFilters({ ...filters, perPage: parseInt(perPage) })
    }
    const orderBy = params.get('orderBy')
    if (orderBy && orderBy !== filters.orderBy) {
      setFilters({ ...filters, orderBy })
    }
    const orderDir = params.get('orderDir')
    if (orderDir && orderDir !== filters.orderDir) {
      setFilters({ ...filters, orderDir })
    }
  }, [])

  const baseRoute = route(`${routePrefix}.${routeKey}`)

  const {
    isLoading,
    isError,
    isSuccess,
    data: roles,
    error,
  } = useQuery<PagedResource<Role>, Error>({
    queryKey: [routeKey, filters],
    queryFn: () => getCrudIndex(baseRoute, filters),
  })

  // Actions callback
  const actionsCallback = (action: string, item: Role) => {
    if (action === 'destroy') {
      const url = item.actions.find((a) => a.action === 'destroy')?.url
      router.delete(url!, {
        onBefore: () => confirm('Are you sure you want to delete this item?'),
        onSuccess: () => {
          queryClient.invalidateQueries({
            queryKey: [routeKey, filters],
          })
        },
      })
    }
  }

  // Table columns helper
  const columns = useMemo(() => createColumns<Role>(actions, listable, actionsCallback), [actions, listable])

  // Sorting state
  const [sorting, setSorting] = useState<SortingState>([{ id: filters.orderBy, desc: filters.orderDir === 'desc' }]) // can set initial sorting state here

  useEffect(() => {
    setFilters({
      ...filters,
      orderBy: sorting[0]?.id ?? 'created_at',
      orderDir: sorting[0]?.desc ? 'desc' : 'asc',
    })
  }, [sorting])

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Roles</h2>
          <form
            className="ml-4 flex-1"
            onChange={(e) => {
              const target = e.target as HTMLInputElement
              setFilters({ ...filters, search: target.value })
            }}
          >
            <div className="relative">
              <Search className="absolute left-2.5 top-3 size-4 text-muted-foreground" />
              <Input
                defaultValue={filters.search}
                type="search"
                placeholder="Search..."
                className="w-full appearance-none bg-background pl-8 shadow-none md:w-2/3 lg:w-1/3"
              />
            </div>
          </form>
          <Link
            href={route(`${routePrefix}.roles.create`)}
            className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'ml-auto')}
          >
            <Plus className="size-4" />
          </Link>
        </>
      }
    >
      <Head title="Roles" />

      {isLoading && <div className="">Loading...</div>}
      {isError && (
        <div className="relative rounded-lg border px-4 py-3 text-justify text-sm">
          <TriangleAlert className="mr-2 inline-block h-5 w-5 text-red-800" />
          {error?.message ?? 'Error loading data. Please try again later.'}
        </div>
      )}
      {isSuccess && (
        <>
          <DataTable columns={columns} data={roles.data ?? []} sorting={sorting} setSorting={setSorting} />
          <TablePagination
            meta={roles.meta}
            onPageSelect={(page) =>
              router.get(location, {
                ...filters,
                page,
              })
            }
            perPage={filters.perPage}
            onPerPageChange={(perPage) =>
              router.get(location, {
                ...filters,
                page: 1,
                perPage,
              })
            }
          />
        </>
      )}
    </AuthenticatedLayout>
  )
}
