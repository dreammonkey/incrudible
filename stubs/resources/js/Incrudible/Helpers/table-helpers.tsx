import { cn, formatDate } from '@/lib/utils'
import { CrudResource, TableActionConfig } from '@/types/incrudible'
import { Link } from '@inertiajs/react'
import { ColumnDef } from '@tanstack/react-table'
import * as Icons from 'lucide-react'
import { MoreHorizontal } from 'lucide-react'
import { Button, buttonVariants } from '@/Incrudible/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/Incrudible/ui/dropdown-menu'

export const getIcon = (iconName: keyof typeof Icons) => {
  return Icons[iconName] as React.ComponentType<{ className?: string }>
}

export const createColumns = <T extends CrudResource>(
  actions: TableActionConfig[],
  listable: string[] = [],
  callback?: (action: string, item: T) => void,
): ColumnDef<T>[] => {
  //
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
                <MoreHorizontal className="h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              {actions.map((action, index) => {
                const IconComponent = getIcon(action.icon as keyof typeof Icons)
                return (
                  <DropdownMenuItem key={index}>
                    {action.type === 'button' && (
                      <Button
                        variant={action.variant ?? 'ghost'}
                        onClick={() => callback?.(action.action, item)}
                      >
                        <IconComponent className="mr-2 h-4 w-4" />
                        &nbsp;{action.label}
                      </Button>
                    )}
                    {action.type === 'link' && (
                      <Link
                        href={
                          item.actions.find((a) => a.action === action.action)
                            ?.url ?? '#'
                        }
                        className={cn(
                          buttonVariants({
                            variant: action.variant ?? 'ghost',
                            size: 'sm',
                          }),
                          'w-full justify-start rounded-md text-sm',
                        )}
                      >
                        <IconComponent className="mr-2 h-4 w-4" />
                        &nbsp;{action.label}
                      </Link>
                    )}
                  </DropdownMenuItem>
                )
              })}
            </DropdownMenuContent>
          </DropdownMenu>
        )
      },
    },
  ]
}
