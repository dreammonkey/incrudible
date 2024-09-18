import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { createColumns } from '@/Incrudible/Helpers/table-helpers'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { buttonVariants } from '@/Incrudible/ui/button'
import { DataTable } from '@/Incrudible/ui/data-table'
import { cn } from '@/lib/utils'
import { CrudResource, Filters, HasManyCrudRelation, PagedResource, Resource } from '@/types/incrudible'
import { Link } from '@inertiajs/react'
import { useQuery } from '@tanstack/react-query'
import { Plus, TriangleAlert } from 'lucide-react'
import { useMemo, useState } from 'react'
import { TablePagination } from '../TablePagination'

interface HasManyProps<T> {
  resource: Resource<T>
  relation: HasManyCrudRelation<T>
}

export const HasMany = <T extends CrudResource>({ resource, relation }: HasManyProps<T>) => {
  const { routePrefix } = useIncrudible()

  const [filters, setFilters] = useState<Filters>({
    page: 1,
    perPage: relation.paging.default,
    orderBy: 'created_at',
    orderDir: 'desc',
    search: '',
  })

  // console.log('HasMany', resource, relation)
  // console.log(route(`${routePrefix}.${relation.route}.index`, resource.data.id))
  const { data, isLoading, isError, isSuccess, error } = useQuery<PagedResource<T>>({
    queryFn: () => getCrudIndex(route(`${routePrefix}.${relation.route}.index`, resource.data.id), filters),
    queryKey: [relation?.route, filters],
  })

  const columns = useMemo(
    () => createColumns(relation.actions, relation.listable),
    [relation.actions, relation.listable],
  )

  return (
    <div key={relation.name} className="mt-4 grid gap-y-2 rounded-lg border p-4">
      <div className="flex">
        <h3 className="text-lg font-semibold capitalize">
          {relation.name} <small className="text-xs text-muted-foreground">({relation.type})</small>
        </h3>
        <form>{/* TODO: Search */}</form>
        <Link
          href={route(`${routePrefix}.${relation.route}.create`, resource.data.id)}
          className={cn(buttonVariants({ variant: 'outline', size: 'sm' }), 'ml-auto')}
        >
          <Plus className="size-4" />
        </Link>
      </div>
      {isLoading && <div className="">Loading...</div>}
      {isError && (
        <div className="relative rounded-lg border px-4 py-3 text-justify text-sm">
          <TriangleAlert className="mr-2 inline-block h-5 w-5 text-red-800" />
          {error?.message ?? 'Error loading data. Please try again later.'}
        </div>
      )}
      {isSuccess && (
        <>
          {/* <pre className="text-xs">{JSON.stringify(data.data, null, 2)}</pre> */}
          <DataTable columns={columns} data={data.data ?? []} />
          <TablePagination
            meta={data.meta}
            onPageSelect={(page) =>
              setFilters({
                ...filters,
                page,
              })
            }
            perPage={filters.perPage}
            perPageOptions={relation.paging.options}
            onPerPageChange={(perPage) =>
              setFilters({
                ...filters,
                page: 1,
                perPage,
              })
            }
          />
        </>
      )}
    </div>
  )
}
