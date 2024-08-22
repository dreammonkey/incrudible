import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { Button } from '@/Incrudible/ui/button'
import { Combobox } from '@/Incrudible/ui/combobox'
import { DataTable } from '@/Incrudible/ui/data-table'
import { CrudRelation } from '@/types/incrudible'
import { useForm } from '@inertiajs/react'
import { useQuery } from '@tanstack/react-query'
import { ColumnDef } from '@tanstack/react-table'
import { Trash2 } from 'lucide-react'

interface BelongsToManyProps<T> {
  relation: CrudRelation<T>
  idKey: keyof T
  nameKey: keyof T
  onChange?: (value: T[]) => void
}

export const BelongsToMany = <T extends Record<string, any>>({
  relation,
  idKey,
  nameKey,
  onChange,
}: BelongsToManyProps<T>) => {
  const { data: options } = useQuery({
    queryFn: () => getCrudIndex(relation?.indexRoute ?? ''),
    queryKey: [relation?.indexRoute],
  })

  const { data, put, setData, isDirty, setDefaults } = useForm<{ items: T[] }>({
    items: relation.value,
  })

  const columns: ColumnDef<T>[] = [
    {
      accessorKey: nameKey as string,
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
                  items: data.items.filter((d) => d[idKey] !== item[idKey]),
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
          options={((options?.data as T[]) ?? []).filter(
            (option) => !data.items.find((d) => d[idKey] === option[idKey]),
          )}
          getKey={(option) => option[idKey].toString()}
          getLabel={(option) => option[nameKey]}
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
              if (relation.storeRoute) {
                put(relation.storeRoute, {
                  onSuccess: () => {
                    setDefaults()
                  },
                })
              }
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
