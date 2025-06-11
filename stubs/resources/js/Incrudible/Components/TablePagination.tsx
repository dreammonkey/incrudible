import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/Incrudible/ui/select'
import { PagedResource } from '@/types/incrudible'
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from '@/Incrudible/ui/pagination'
import { ChevronsLeft, ChevronsRight } from 'lucide-react'

function pageList(currentPage: number, maxPages: number, lastPage: number) {
  const pages = []
  const start = Math.max(1, currentPage - maxPages)
  const end = Math.min(lastPage, currentPage + maxPages)

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
}

export function TablePagination<T>({
  meta,
  perPage,
  perPageOptions = [5, 10, 25, 50, 100],
  onPageSelect,
  onPerPageChange,
}: {
  meta: PagedResource<T>['meta']
  perPage: number
  perPageOptions?: number[]
  onPageSelect?: (page: number) => void
  onPerPageChange: (perPage: number) => void
}) {
  //   console.log(pageList(5, 2, 10))
  // console.log(meta)
  return (
    <Pagination className="flex justify-between">
      <PaginationContent>
        <p className="text-sm">
          Displaying {meta.from?.toLocaleString()} to{' '}
          {meta.to?.toLocaleString()} of {meta.total?.toLocaleString()} results
        </p>
      </PaginationContent>

      <PaginationContent>
        {/* First */}
        <PaginationItem>
          <PaginationLink
            href="#"
            aria-disabled={meta.current_page === 1}
            tabIndex={meta.current_page === 1 ? -1 : undefined}
            className={
              meta.current_page === 1
                ? 'pointer-events-none border-gray-500 opacity-60'
                : undefined
            }
            onClick={() => onPageSelect?.(1)}
          >
            <ChevronsLeft className="h-4 w-4" />
          </PaginationLink>
        </PaginationItem>

        {/* Previous */}
        <PaginationItem>
          <PaginationPrevious
            href="#"
            aria-disabled={meta.current_page === 1}
            tabIndex={meta.current_page === 1 ? -1 : undefined}
            className={
              meta.current_page === 1
                ? 'pointer-events-none border-gray-500 opacity-60'
                : undefined
            }
            onClick={() => onPageSelect?.(1)}
          />
        </PaginationItem>

        {/* Page list */}
        {pageList(meta.current_page, 2, meta.last_page).map((page) => (
          <PaginationItem key={page}>
            <PaginationLink
              href="#"
              isActive={meta.current_page === page}
              aria-disabled={meta.current_page === page}
              tabIndex={meta.current_page === page ? -1 : undefined}
              className={
                meta.current_page === page
                  ? 'pointer-events-none border-gray-500 opacity-60'
                  : undefined
              }
              onClick={() => onPageSelect?.(page)}
            >
              {page}
            </PaginationLink>
          </PaginationItem>
        ))}

        {/* Next */}
        <PaginationItem>
          <PaginationNext
            href="#"
            aria-disabled={meta.current_page === meta.last_page}
            tabIndex={meta.current_page === meta.last_page ? -1 : undefined}
            className={
              meta.current_page === meta.last_page
                ? 'pointer-events-none border-gray-500 opacity-60'
                : undefined
            }
            onClick={() => onPageSelect?.(meta.current_page + 1)}
          />
        </PaginationItem>

        {/* Last */}
        <PaginationItem>
          <PaginationLink
            href="#"
            aria-disabled={meta.current_page === meta.last_page}
            tabIndex={meta.current_page === meta.last_page ? -1 : undefined}
            className={
              meta.current_page === meta.last_page
                ? 'pointer-events-none border-gray-500 opacity-60'
                : undefined
            }
            onClick={() => onPageSelect?.(meta.last_page)}
          >
            <ChevronsRight className="h-4 w-4" />
          </PaginationLink>
        </PaginationItem>
      </PaginationContent>

      <PaginationContent className="flex items-center space-x-2 text-sm">
        <p>Rows per page</p>

        <Select
          onValueChange={(value) => onPerPageChange(parseInt(value))}
          defaultValue={perPage.toString()}
        >
          <SelectTrigger className="w-[80px] text-sm">
            <SelectValue placeholder={perPage} />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectLabel>Rows</SelectLabel>
              {perPageOptions.map((option) => (
                <SelectItem key={option} value={option.toString()}>
                  {option}
                </SelectItem>
              ))}
            </SelectGroup>
          </SelectContent>
        </Select>
      </PaginationContent>
    </Pagination>
  )
}
