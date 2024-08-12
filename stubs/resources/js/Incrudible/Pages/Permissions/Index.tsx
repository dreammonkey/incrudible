import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { TablePagination } from '@/Incrudible/Components/TablePagination'
import AuthenticatedLayout from '@/Incrudible/Layouts/AuthenticatedLayout'
import { Button, buttonVariants } from '@/Incrudible/ui/button'
import { DataTable } from '@/Incrudible/ui/data-table'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/Incrudible/ui/dropdown-menu'
import { Input } from '@/Incrudible/ui/input'
import { cn, formatDate } from '@/lib/utils'
import {
  Permission,
  Filters,
  PageProps,
  PagedResource,
  TableAction,
} from '@/types/incrudible'
import { Head, Link, router, usePage } from '@inertiajs/react'
import { useQuery } from '@tanstack/react-query'
import { ColumnDef, SortingState } from '@tanstack/react-table'
import {
  Eye,
  MoreHorizontal,
  Pencil,
  Plus,
  Search,
  Trash,
  TriangleAlert,
} from 'lucide-react'
import { useEffect, useLayoutEffect, useMemo, useState } from 'react'

export const createColumns = (actions: TableAction[]): ColumnDef<Permission>[] => [
  {
    accessorKey: 'id',
    header: 'Id',
  },
  {
accessorKey: 'name',
header: 'Name',
},
{
accessorKey: 'guard_name',
header: 'Guard Name',
},
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
              <MoreHorizontal className="h-4 w-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            {actions.map((action, index) => (
              <DropdownMenuItem key={index}>
                <Link
                  className={cn(
                    buttonVariants({ variant: 'ghost', size: 'sm' }),
                    'w-full justify-start rounded-md text-sm',
                  )}
                  onClick={action.onClick?.(item.id) ?? (() => {})}
                  href={
                    action.route ? route(action.route, { permission: item.id }) : '#'
                  }
                >
                  <action.icon className="mr-2 h-4 w-4" />
                  &nbsp;{action.label}
                </Link>
              </DropdownMenuItem>
            ))}
          </DropdownMenuContent>
        </DropdownMenu>
      )
    },
  },
]

export default function PermissionIndex({ auth }: PageProps) {
  const props = usePage<PageProps>().props

  const {
    incrudible: { routePrefix },
    ziggy: { query, location },
  } = props

  const params = new URLSearchParams(query)
  // console.log(query)

  const routeKey = 'permissions.index'

  const [filters, setFilters] = useState<Filters>({
    page: params.get('page') ? parseInt(params.get('page') as string) : 1,
    perPage: params.get('perPage')
      ? parseInt(params.get('perPage') as string)
      : 10,
    orderBy: params.get('orderBy') ?? 'created_at',
    orderDir: params.get('orderDir') ?? 'desc',
    search: '',
  })
  // console.log({ filters })

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
    data: permissions,
    error,
  } = useQuery<PagedResource<Permission>, Error>({
    queryKey: [routeKey, filters],
    queryFn: () => getCrudIndex(baseRoute, filters),
  })

  // console.log({ permissions })
  // console.log({ error })

  // TODO: actions
  const actions = [
    {
      label: 'Show',
      icon: Eye,
      route: `${routePrefix}.permissions.show`,
    },
    {
      label: 'Edit',
      icon: Pencil,
      route: `${routePrefix}.permissions.edit`,
    },
    {
      // TODO
      label: 'Delete',
      icon: Trash,
      route: `${routePrefix}.permissions.edit`,
      // onClick: (id: any) => console.log('Delete', id),
    },
  ]

  const columns = useMemo(() => createColumns(actions), [actions])

  const [sorting, setSorting] = useState<SortingState>([
    { id: filters.orderBy, desc: filters.orderDir === 'desc' },
  ]) // can set initial sorting state here
  // console.log({ sorting })

  useEffect(() => {
    // console.log({ sorting })
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
          <h2 className="xs:ml-2 px-1 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Permissions
          </h2>
          <form
            className="ml-4 flex-1"
            onChange={(e) => {
              const target = e.target as HTMLInputElement
              setFilters({ ...filters, search: target.value })
            }}
          >
            <div className="relative">
              <Search className="absolute left-2.5 top-3 h-4 w-4 text-muted-foreground" />
              <Input
                defaultValue={filters.search}
                type="search"
                placeholder="Search..."
                className="w-full appearance-none bg-background pl-8 shadow-none md:w-2/3 lg:w-1/3"
              />
            </div>
          </form>
          <Link
            href={route(`${routePrefix}.permissions.create`)}
            className={cn(
              buttonVariants({ variant: 'outline', size: 'sm' }),
              'ml-auto',
            )}
          >
            <Plus className="h-4 w-4" />
          </Link>
        </>
      }
    >
      <Head title="Permissions" />

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
            data={permissions.data ?? []}
            sorting={sorting}
            setSorting={setSorting}
          />
          <TablePagination
            meta={permissions.meta}
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
