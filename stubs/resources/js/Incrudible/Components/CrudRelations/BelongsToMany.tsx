import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { useIncrudible } from '@/Incrudible/Hooks/use-incrudible'
import { Button } from '@/Incrudible/ui/button'
import { Combobox } from '@/Incrudible/ui/combobox'
import { DataTable } from '@/Incrudible/ui/data-table'
import { BelongsToManyCrudRelation, CrudResource, PagedResource, Resource } from '@/types/incrudible'
import { useForm } from '@inertiajs/react'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { ColumnDef } from '@tanstack/react-table'
import { Trash2 } from 'lucide-react'
import { useEffect } from 'react'

interface BelongsToManyProps<T> {
  resource: Resource<T>
  relation: BelongsToManyCrudRelation<T>
  onChange?: (value: T[]) => void
}

export const BelongsToMany = <T extends CrudResource>({ resource, relation, onChange }: BelongsToManyProps<T>) => {
  const queryClient = useQueryClient()

  const { routePrefix } = useIncrudible()

  const { data: values } = useQuery<PagedResource<T>>({
    queryFn: () => getCrudIndex(route(`${routePrefix}.${relation.route}.value`, resource.data.id)),
    queryKey: [`${routePrefix}.${relation.route}.value`, resource.data.id],
  })

  useEffect(() => {
    if (values?.data) {
      setDefaults({ items: values.data })
    }
  }, [values])

  const { data: options } = useQuery<PagedResource<T>>({
    queryFn: () => getCrudIndex(route(`${routePrefix}.${relation.route}.options`, resource.data.id)),
    queryKey: [`${routePrefix}.${relation.route}.options`, resource.data.id],
  })

  const updateRoute = route(`${routePrefix}.${relation.route}.update`, resource.data.id)

  const { data, put, setData, isDirty, setDefaults } = useForm<{ items: T[] }>({
    items: [],
  })

  const columns: ColumnDef<T>[] = [
    {
      accessorKey: relation.labelKey as string,
      header: 'Name',
    },
    {
      id: 'actions',
      enableSorting: false,
      cell: ({ row }) => {
        const item = row.original

        return (
          <div className="flex justify-end">
            <Button
              onClick={() =>
                setData({
                  items: data.items.filter((d) => d[relation.idKey] !== item[relation.idKey]),
                })
              }
              variant="destructive"
              size="sm"
              className="mr-2"
            >
              <Trash2 className="size-4" />
            </Button>
          </div>
        )
      },
    },
  ]

  return (
    <div key={relation.name} className="mt-4 grid gap-y-2 rounded-lg border p-4">
      <h3 className="text-lg font-semibold capitalize">
        {relation.name} <small className="text-xs text-muted-foreground">({relation.type})</small>
      </h3>

      <div className="grid gap-4">
        <Combobox
          value={undefined as unknown as T}
          // filter out options that have already been selected (but have not yet been saved)
          options={((options?.data ?? []) as T[]).filter(
            (option) => !data.items.find((d) => d[relation.idKey] === option[relation.idKey]),
          )}
          getKey={(option) => option[relation.idKey].toString()}
          getLabel={(option) => option[relation.labelKey]}
          onChange={(value) => {
            setData({
              items: [...data.items, value],
            })
            onChange?.([...data.items, value])
          }}
          placeholder={`Select ${relation.name}`}
        />

        <div>
          <DataTable data={data.items} columns={columns} />
        </div>

        <div>
          <Button
            onClick={() => {
              put(updateRoute, {
                onSuccess: () => {
                  // reset form
                  setDefaults()
                  // invalidate queries
                  queryClient.invalidateQueries({
                    queryKey: [`${routePrefix}.${relation.route}.value`, resource.data.id],
                  })
                  queryClient.invalidateQueries({
                    queryKey: [`${routePrefix}.${relation.route}.options`, resource.data.id],
                  })
                },
              })
            }}
            disabled={!isDirty}
          >
            Save
          </Button>
        </div>
      </div>

      {/* <pre className="text-xs">{JSON.stringify(relation, null, 2)}</pre> */}
    </div>
  )
}
