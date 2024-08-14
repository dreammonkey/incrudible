import { getCrudIndex } from '@/Incrudible/Api/services/getCrudIndex'
import { TablePagination } from '@/Incrudible/Components/TablePagination'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { Button, buttonVariants } from '@/Incrudible/ui/button'
import { DataTable } from '@/Incrudible/ui/data-table'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/Incrudible/ui/dropdown-menu'
import { Input } from '@/Incrudible/ui/input'
import { cn, formatDate } from '@/lib/utils'
import { Admin, Filters, PageProps, PagedResource, TableAction } from '@/types/incrudible'
import { Head, Link, router, useForm, usePage } from '@inertiajs/react'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { ColumnDef, SortingState } from '@tanstack/react-table'
import { Eye, MoreHorizontal, Pencil, Plus, Search, Trash, TriangleAlert } from 'lucide-react'
import { useEffect, useLayoutEffect, useMemo, useState } from 'react'

export const createColumns = (actions: TableAction[], listable: string[] = []): ColumnDef<Admin>[] => {
  const fields = listable.map((field) => {
    return {
      accessorKey: field,
      header: field,
    }
  })

  return [
    {
      accessorKey: 'id',
      header: 'Id',
    },
    ...fields,
    {
      accessorKey: 'created_at',
      header: 'Created',
      cell: ({ row }) => formatDate(row.original.created_at),
    },
    {
      accessorKey: 'updated_at',
      header: 'Updated',
      cell: ({ row }) => formatDate(row.original.updated_at),
    },
    {
      id: 'actions',
      header: 'Actions',
      enableSorting: false,
      cell: ({ row }) => {
        const item = row.original

        return (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="h-8 w-8 p-0">
                <span className="sr-only">Open menu</span>
                <MoreHorizontal className="size-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              {actions.map((action, index) => (
                <DropdownMenuItem key={index}>
                  {action.onClick ? (
                    <Button variant={action.variant ?? 'ghost'} onClick={() => action.onClick?.(item.id)}>
                      <action.icon className="mr-2 size-4" />
                      &nbsp;{action.label}
                    </Button>
                  ) : (
                    <Link
                      href={action.route ? route(action.route, { admin: item.id }) : '#'}
                      className={cn(
                        buttonVariants({ variant: action.variant ?? 'ghost', size: 'sm' }),
                        'w-full justify-start rounded-md text-sm',
                      )}
                    >
                      <action.icon className="mr-2 size-4" />
                      &nbsp;{action.label}
                    </Link>
                  )}
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
        )
      },
    },
  ]
}

export default function AdminIndex({ auth, listable }: PageProps<{ listable: string[] }>) {
  const props = usePage<PageProps>().props

  const queryClient = useQueryClient()

  const {
    incrudible: { routePrefix },
    ziggy: { query, location },
  } = props

  const params = new URLSearchParams(query)

  const routeKey = 'admins.index'

  const [filters, setFilters] = useState<Filters>({
    page: params.get('page') ? parseInt(params.get('page') as string) : 1,
    perPage: params.get('perPage') ? parseInt(params.get('perPage') as string) : 10,
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
    data: admins,
    error,
  } = useQuery<PagedResource<Admin>, Error>({
    queryKey: [routeKey, filters],
    queryFn: () => getCrudIndex(baseRoute, filters),
  })

  const { delete: destroy } = useForm()

  const onDelete = async (id: any) => {
    if (!confirm('Are you sure you want to delete this item?')) {
      return
    }

    destroy(route(`${routePrefix}.admins.destroy`, { admin: id }), {
      onSuccess: () => {
        queryClient.invalidateQueries({
          queryKey: [routeKey, filters],
        })
      },
    })
  }

  const actions: TableAction[] = [
    {
      label: 'Show',
      variant: 'ghost',
      icon: Eye,
      route: `${routePrefix}.admins.show`,
    },
    {
      label: 'Edit',
      variant: 'ghost',
      icon: Pencil,
      route: `${routePrefix}.admins.edit`,
    },
    {
      label: 'Delete',
      variant: 'destructive',
      icon: Trash,
      onClick: onDelete,
    },
  ]

  const columns = useMemo(() => createColumns(actions, listable), [actions, listable])

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
      admin={auth.admin.data}
      header={
        <>
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Admins</h2>
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
            href={route(`${routePrefix}.admins.create`)}
            className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'ml-auto')}
          >
            <Plus className="size-4" />
          </Link>
        </>
      }
    >
      <Head title="Admins" />

      {isLoading && <div className="">Loading...</div>}
      {isError && (
        <div className="relative rounded-lg border px-4 py-3 text-justify text-sm">
          <TriangleAlert className="mr-2 inline-block h-5 w-5 text-red-800" />
          {error?.message ?? 'Error loading data. Please try again later.'}
        </div>
      )}
      {isSuccess && (
        <>
          <DataTable columns={columns} data={admins.data ?? []} sorting={sorting} setSorting={setSorting} />
          <TablePagination
            meta={admins.meta}
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
