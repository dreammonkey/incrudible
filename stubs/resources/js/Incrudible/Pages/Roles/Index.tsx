import { getCrudIndex } from '@/Incrudible/Api/services/getCrudIndex'
import { TablePagination } from '@/Incrudible/Components/TablePagination'
import { createColumns } from '@/Incrudible/Helpers/table-helpers'
import { useToast } from '@/Incrudible/Hooks/use-toast'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { buttonVariants } from '@/Incrudible/ui/button'
import { DataTable } from '@/Incrudible/ui/data-table'
import { Input } from '@/Incrudible/ui/input'
import { cn } from '@/lib/utils'
import {
  Role,
  Filters,
  PagedResource,
  PageProps,
  PagingConfig,
  TableActionConfig,
} from '@/types/incrudible'
import { Head, Link, router, usePage } from '@inertiajs/react'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { SortingState } from '@tanstack/react-table'
import { Plus, Search, TriangleAlert } from 'lucide-react'
import { useEffect, useMemo, useState } from 'react'

export default function RoleIndex({
  auth,

  listable,
  paging,
  actions,
  create: allowCreate,
}: PageProps<{
  listable: string[]
  actions: TableActionConfig[]
  paging: PagingConfig
  create: boolean
}>) {
  const props = usePage<PageProps>().props
  const { toast } = useToast()
  const queryClient = useQueryClient()

  const {
    incrudible: { routePrefix },
    ziggy: { query, location },
  } = props

  const params = new URLSearchParams(query)
  const routeKey = 'roles.index'

  // Extract filters directly from URL params
  const filters: Filters = {
    page: params.get('page') ? parseInt(params.get('page') as string) : 1,
    perPage: params.get('perPage')
      ? parseInt(params.get('perPage') as string)
      : paging.default,
    orderBy: params.get('orderBy') ?? 'created_at',
    orderDir: params.get('orderDir') ?? 'desc',
    search: params.get('search') ?? undefined,
  }

  const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    updateFilters({
      search: e.target.value || undefined,
      page: 1, // Reset to first page on search change
    })
  }

  // Helper function to update URL with new filters
  const updateFilters = (newFilters: Partial<Filters>) => {
    const updatedFilters = { ...filters, ...newFilters }

    // Remove empty search param
    if (!updatedFilters.search) {
      delete updatedFilters.search
    }

    router.get(location, updatedFilters, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  }

  const baseRoute = route(`${routePrefix}.${routeKey}`, [])

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
      if (!url) return
      router.delete(url, {
        onBefore: () => confirm('Are you sure you want to delete this role?'),
        onSuccess: () => {
          toast({
            title: 'Role deleted successfully',
          })
          queryClient.invalidateQueries({
            queryKey: [routeKey, filters],
          })
        },
        onError: () => {
          toast({
            title: 'Error deleting role',
            description: 'Please try again later',
            variant: 'destructive',
          })
        },
      })
    }
  }

  // Table columns helper
  const columns = useMemo(
    () => createColumns<Role>(actions, listable, actionsCallback),
    [actions, listable],
  )

  // Sorting state
  const [sorting, setSorting] = useState<SortingState>([
    { id: filters.orderBy, desc: filters.orderDir === 'desc' },
  ])

  useEffect(() => {
    const newOrderBy = sorting[0]?.id ?? 'created_at'
    const newOrderDir = sorting[0]?.desc ? 'desc' : 'asc'

    if (newOrderBy !== filters.orderBy || newOrderDir !== filters.orderDir) {
      updateFilters({
        orderBy: newOrderBy,
        orderDir: newOrderDir,
      })
    }
  }, [sorting])

  return (
    <AuthenticatedLayout
      admin={auth.admin}
      header={
        <div className="flex w-full flex-row items-center justify-between space-x-2">
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Roles
          </h2>
          <form className="ml-4 flex-1">
            <div className="relative min-w-96 md:w-2/3 lg:w-1/2 xl:w-1/4">
              <Search className="absolute left-2.5 top-3 size-4 text-muted-foreground" />
              <Input
                defaultValue={filters.search ?? ''}
                onChange={handleSearchChange}
                type="search"
                placeholder="Search..."
                className="w-full appearance-none bg-background pl-8 shadow-none"
              />
            </div>
          </form>
          {allowCreate && (
            <Link
              href={route(`${routePrefix}.roles.create`, [])}
              className={cn(
                buttonVariants({ variant: 'outline', size: 'sm' }),
                'ml-auto',
              )}
            >
              <Plus className="size-4" />
            </Link>
          )}
        </div>
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
          <DataTable
            columns={columns}
            data={roles.data ?? []}
            sorting={sorting}
            setSorting={setSorting}
          />
          <TablePagination
            meta={roles.meta}
            onPageSelect={(page) => updateFilters({ page })}
            perPage={filters.perPage}
            onPerPageChange={(perPage) => updateFilters({ page: 1, perPage })}
          />
        </>
      )}
    </AuthenticatedLayout>
  )
}
