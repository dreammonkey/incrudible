import { getCrudIndex } from '@/Incrudible/Api/Crud'
import { Combobox } from '@/Incrudible/ui/combobox'
import { CrudRelation, Permission, Role } from '@/types/incrudible'
import { useQuery } from '@tanstack/react-query'
import { useEffect, useState } from 'react'
// import { route } from '../../../../../vendor/tightenco/ziggy/src/js'
import { useForm } from 'react-hook-form'

interface BelongsToManyProps {
  relation: CrudRelation<Permission>
  onChange?: (value: Permission[]) => void
}

export const BelongsToMany: React.FC<BelongsToManyProps> = ({ relation, onChange, ...props }) => {
  const { data: options } = useQuery({
    queryFn: () => getCrudIndex(route(relation?.routeKey ?? '')),
    queryKey: [relation?.routeKey],
  })
  // console.log(options)

  // const [selected, setSelected] = useState<Permission[]>(relation.value)

  // const { setData, put, data, recentlySuccessful } = useForm<Role>(role.data)

  // useEffect(() => {

  //   // put

  // }, [selected])

  return (
    <div key={relation.name} className="mt-4 grid gap-y-2 rounded-lg border p-4">
      <h3 className="text-lg font-semibold capitalize">
        {relation.name} <small className="text-xs text-muted-foreground">({relation.type})</small>
      </h3>
      {/* <form action=""> */}
      <div className="grid gap-y-2">
        <Combobox
          multiple
          value={relation.value}
          options={(options?.data as Permission[]) ?? []}
          getKey={(option) => option.id.toString()}
          getLabel={(option) => option.name}
          getValue={(option) => option}
          onChange={(value) => {
            // setSelected(value)
            // onChange?.(value)
            // A post request should be made here to update the relation
            console.log({ value })
          }}
          placeholder={`Select ${relation.name}`}
        />
      </div>
      {/* </form> */}

      <pre className="text-xs">{JSON.stringify(relation, null, 2)}</pre>
    </div>
  )
}
