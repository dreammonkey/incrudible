import { cn } from '@/lib/utils'
import { MenuItem, PageProps } from '@/types/incrudible'
import { Link, usePage } from '@inertiajs/react'
import { Collapsible } from '@radix-ui/react-collapsible'
import * as Icons from 'lucide-react'
import { useState } from 'react'
import { CollapsibleContent, CollapsibleTrigger } from '../ui/collapsible'

const menuItemClasses =
  'w-full flex items-center gap-3 rounded-lg px-3 py-2 text-muted-foreground transition-all hover:text-primary whitespace-nowrap'

const isRouteActive = (item: MenuItem, url: string, routePrefix: string): boolean => {
  if (item.route) {
    return url === route(`${routePrefix}.${item.route}`, undefined, false)
  }
  return item.items?.some((child) => isRouteActive(child, url, routePrefix)) || false
}

const MenuItemComponent: React.FC<{ item: MenuItem }> = ({ item }) => {
  const {
    props: {
      incrudible: { routePrefix },
    },
    url,
  } = usePage<PageProps>()

  const IconComponent = Icons[item.icon] as React.ComponentType<{
    className?: string
  }>

  const isActive = isRouteActive(item, url, routePrefix)

  const [isOpen, setIsOpen] = useState(isActive)

  return item.items ? (
    <Collapsible open={isOpen} onOpenChange={setIsOpen}>
      <CollapsibleTrigger className={menuItemClasses}>
        {IconComponent && <IconComponent className="h-4 w-4" />}
        {item.label}
        {isOpen ? <Icons.ChevronUp className="ml-auto h-4 w-4" /> : <Icons.ChevronDown className="ml-auto h-4 w-4" />}
      </CollapsibleTrigger>
      <CollapsibleContent className="">
        {item.items?.map((item) => <MenuItemComponent key={item.label} item={item} />)}
      </CollapsibleContent>
    </Collapsible>
  ) : (
    <Link
      href={route(`${routePrefix}.${item.route}`)}
      className={cn(isActive ? 'bg-muted text-primary' : '', menuItemClasses)}
    >
      {IconComponent && <IconComponent className="h-4 w-4" />}
      {item.label}
    </Link>
  )
}

export const MainNavigation: React.FC = () => {
  const { menu } = usePage<PageProps>().props.incrudible
  return menu?.items.map((item) => <MenuItemComponent key={item.label} item={item} />)
}
