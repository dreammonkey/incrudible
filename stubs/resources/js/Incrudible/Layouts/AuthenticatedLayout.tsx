import { Admin, Resource } from '@/types/incrudible'
import { Link } from '@inertiajs/react'
import { CircleUser, Menu, Package } from 'lucide-react'
import { PropsWithChildren, ReactNode, useState } from 'react'
import { DarkModeToggle } from '../Components/DarkModeToggle'
import { MainNavigation } from '../Components/MainNavigation'
import { useIncrudible } from '../Hooks/use-incrudible'
import { Button } from '../ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '../ui/dropdown-menu'
import { Sheet, SheetContent, SheetTrigger } from '../ui/sheet'

export default function AuthenticatedLayout({
  admin,
  header,
  children,
}: PropsWithChildren<{ admin: Resource<Admin>; header?: ReactNode }>) {
  // Toggle mobile menu
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)

  const { routePrefix } = useIncrudible()

  return (
    <div className="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[280px_1fr]">
      <div className="hidden border-r bg-muted/40 md:block">
        {/* Sidebar Desktop */}
        <div className="flex h-full max-h-screen flex-col gap-2">
          <div className="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-6">
            <Link href="/" className="flex items-center gap-2 font-semibold">
              <Package className="h-6 w-6" />
              <span className="">Incrudible</span>
            </Link>

            {/* <Button variant="outline" size="icon" className="ml-auto h-8 w-8">
            <Bell className="h-4 w-4" />
            <span className="sr-only">Toggle notifications</span>
          </Button> */}
          </div>
          <div className="flex-1">
            <nav className="grid items-start px-2 text-sm font-medium lg:px-4">
              <MainNavigation />
            </nav>
          </div>
          <div className="mt-auto p-4">
            {/* <Card x-chunk="dashboard-02-chunk-0">
            <CardHeader className="p-2 pt-0 md:p-4">
              <CardTitle>Upgrade to Pro</CardTitle>
              <CardDescription>
                Unlock all features and get unlimited access to our support
                team.
              </CardDescription>
            </CardHeader>
            <CardContent className="p-2 pt-0 md:p-4 md:pt-0">
              <Button size="sm" className="w-full">
                Upgrade
              </Button>
            </CardContent>
          </Card> */}
          </div>
        </div>
      </div>
      <div className="flex flex-col">
        <header className="flex h-14 items-center gap-2 border-b px-4 md:gap-4 lg:h-[60px] lg:px-6">
          <Sheet>
            <SheetTrigger asChild>
              <Button variant="outline" size="icon" className="shrink-0 md:hidden">
                <Menu className="h-5 w-5" />
                <span className="sr-only">Toggle navigation menu</span>
              </Button>
            </SheetTrigger>
            <SheetContent side="left" className="flex flex-col px-4 py-6">
              {/* Sidebar Mobile */}
              <nav className="grid gap-1">
                <Link href="/" className="mb-2 flex items-center gap-2 bg-muted/40 p-2 font-semibold">
                  <Package className="h-6 w-6" />
                  <span className="">Incrudible</span>
                </Link>
                <MainNavigation />
              </nav>
              <div className="mt-auto">
                {/* <Card>
                <CardHeader>
                  <CardTitle>Upgrade to Pro</CardTitle>
                  <CardDescription>
                    Unlock all features and get unlimited access to our
                    support team.
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <Button size="sm" className="w-full">
                    Upgrade
                  </Button>
                </CardContent>
              </Card> */}
              </div>
            </SheetContent>
          </Sheet>
          {/* Top header */}
          <div className="flex w-full space-x-2">
            {/* <form className="flex-1">
              <div className="relative">
                <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                  type="search"
                  placeholder="Search..."
                  className="w-full appearance-none bg-background pl-8 shadow-none md:w-2/3 lg:w-1/3"
                />
              </div>
            </form> */}

            <DarkModeToggle className="ml-auto" />

            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="secondary" size="icon" className="">
                  <CircleUser className="h-5 w-5" />
                  <span className="sr-only">Toggle user menu</span>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem>
                  <Link className="w-full" href={route(`${routePrefix}.settings`)}>
                    Settings
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Link className="w-full" href={route(`${routePrefix}.profile.edit`)}>
                    Profile
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem>
                  <Link method={'post'} as="button" className="w-full text-left" href={route(`${routePrefix}.logout`)}>
                    Logout
                  </Link>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </header>
        <main className="flex flex-1 flex-col gap-4 p-4 lg:gap-x-6 lg:p-6">
          <div className="flex items-center">{header}</div>
          <div className="flex flex-col gap-4">{children}</div>
        </main>
      </div>
    </div>
  )
}
