import { CrudRelationType } from '@/Incrudible/Helpers/incrudible'
import { CrudRelation, Resource } from '@/types/incrudible'
import { BelongsToMany } from './BelongsToMany'
import { HasMany } from './HasMany'

export const CrudRelations: React.FC<{
  resource: Resource<any>
  relations: CrudRelation<any>[]
}> = ({ relations, resource }) => {
  return (
    <>
      {relations?.map((relation) => {
        switch (relation.type) {
          case CrudRelationType.BelongsToMany:
            return <BelongsToMany key={relation.name} relation={relation} resource={resource} />

          case CrudRelationType.HasMany:
            return <HasMany key={relation.name} relation={relation} resource={resource} />

          default:
            return null
        }
      })}

      {/* <pre className="text-xs">{JSON.stringify(relations, null, 2)}</pre> */}
    </>
  )
}
