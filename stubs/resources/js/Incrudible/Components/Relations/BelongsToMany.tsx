import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { Button } from '@/Incrudible/ui/button'
import { Combobox } from '@/Incrudible/ui/combobox'
import { DataTable } from '@/Incrudible/ui/data-table'
import { CrudRelation, Permission } from '@/types/incrudible'
import { useForm } from '@inertiajs/react'
import { useQuery } from '@tanstack/react-query'
import { ColumnDef } from '@tanstack/react-table'
import { Trash2 } from 'lucide-react'

interface BelongsToManyProps {
  relation: CrudRelation<Permission>
  onChange?: (value: Permission[]) => void
}

export const BelongsToMany: React.FC<BelongsToManyProps> = ({ relation, onChange }) => {
  const { data: options } = useQuery({
    queryFn: () => getCrudIndex(relation?.indexRoute ?? ''),
    queryKey: [relation?.indexRoute],
  })

  const { data, put, setData, isDirty, transform } = useForm<{ permissions: Permission[] }>({
    permissions: relation.value,
  })
  console.log({ data, isDirty })

  // Transform the data before sending it to the server
  transform((data) => ({
    // @ts-expect-error the api only accepts an array of ids
    permissions: data.permissions.map((permission) => permission.id),
  }))

  const columns: ColumnDef<Permission>[] = [
    {
      accessorKey: 'name',
      header: 'Name',
    },
    {
      accessorKey: 'guard_name',
      header: 'Guard Name',
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
                  permissions: data.permissions.filter((d) => d.id !== item.id),
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
          multiple
          value={relation.value}
          options={((options?.data as Permission[]) ?? []).filter(
            (option) => !data.permissions.find((d) => d.id === option.id),
          )}
          getKey={(option) => option.id.toString()}
          getLabel={(option) => option.name}
          onChange={(value) => {
            setData((previousData) => ({
              permissions: [...previousData.permissions, ...value],
            }))
            if (onChange) {
              onChange([...data.permissions, ...value])
            }
          }}
          placeholder={`Select ${relation.name}`}
        />

        <div>
          <DataTable data={data.permissions} columns={columns} />
        </div>

        <div>
          <Button
            onClick={() => {
              if (relation.storeRoute) {
                put(relation.storeRoute, { [relation.name]: data.permissions })
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
