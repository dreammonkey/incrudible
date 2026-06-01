import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { Button } from '@/Incrudible/ui/button'
import { Combobox } from '@/Incrudible/ui/combobox'
import { DataTable } from '@/Incrudible/ui/data-table'
import {
  BelongsToManyCrudRelation,
  CrudResource,
  PagedResource,
  Resource,
} from '@/types/incrudible'
import { useForm } from '@inertiajs/react'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { ColumnDef } from '@tanstack/react-table'
import { Trash2 } from 'lucide-react'
import { useEffect, useState } from 'react'

interface BelongsToManyProps<T> {
  resource: Resource<T>
  relation: BelongsToManyCrudRelation<T>
  onChange?: (value: T[]) => void
}

type RelationFormItem = {
  id: string | number
}

export const BelongsToMany = <T extends CrudResource>({
  resource,
  relation,
  onChange,
}: BelongsToManyProps<T>) => {
  const queryClient = useQueryClient()
  const [items, setItems] = useState<T[]>([])

  const formItems = (selectedItems: T[]): RelationFormItem[] =>
    selectedItems.map((item) => ({
      id: item[relation.idKey] as string | number,
    }))

  const { data: values } = useQuery<PagedResource<T>>({
    queryFn: () => getCrudIndex(relation.urls.value),
    queryKey: [relation.urls.value],
    enabled: Boolean(relation.urls.value),
  })

  useEffect(() => {
    if (values?.data) {
      setItems(values.data)
      setData({ items: formItems(values.data) })
      setDefaults({ items: formItems(values.data) })
    }
  }, [values?.data])

  const { data: options } = useQuery<PagedResource<T>>({
    queryFn: () => getCrudIndex(relation.urls.options),
    queryKey: [relation.urls.options],
    enabled: Boolean(relation.urls.options),
  })

  const updateRoute = relation.urls.update

  const { put, setData, isDirty, setDefaults } = useForm<{
    items: RelationFormItem[]
  }>({
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
              onClick={() => {
                const nextItems = items.filter(
                  (d) => d[relation.idKey] !== item[relation.idKey],
                )

                setItems(nextItems)
                setData({ items: formItems(nextItems) })
                onChange?.(nextItems)
              }}
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
    <div
      key={relation.name}
      className="mt-4 grid gap-y-2 rounded-lg border p-4"
    >
      <h3 className="text-lg font-semibold capitalize">
        {relation.name}{' '}
        <small className="text-xs text-muted-foreground">
          ({relation.type})
        </small>
      </h3>

      <div className="grid gap-4">
        <Combobox
          value={undefined as unknown as T}
          // filter out options that have already been selected (but have not yet been saved)
          options={((options?.data ?? []) as T[]).filter(
            (option) =>
              !items.find(
                (d) => d[relation.idKey] === option[relation.idKey],
              ),
          )}
          getKey={(option) => option[relation.idKey].toString()}
          getLabel={(option) => option[relation.labelKey]}
          onChange={(value) => {
            const nextItems = [...items, value]

            setItems(nextItems)
            setData({ items: formItems(nextItems) })
            onChange?.(nextItems)
          }}
          placeholder={`Select ${relation.name}`}
        />

        <div>
          <DataTable data={items} columns={columns} />
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
                    queryKey: [relation.urls.value],
                  })
                  queryClient.invalidateQueries({
                    queryKey: [relation.urls.options],
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
