import { Config } from 'ziggy-js'
import * as Icons from 'lucide-react'

// https://github.com/sveltejs/kit/issues/1997#issuecomment-887614097
export type Typify<T> = { [K in keyof T]: Typify<T[K]> }

// Redecalare forwardRef
// SEE: https://fettblog.eu/typescript-react-generic-forward-refs/
declare module 'react' {
  function forwardRef<T, P = {}>(
    render: (props: P, ref: React.Ref<T>) => React.ReactNode | null,
  ): (props: P & React.RefAttributes<T>) => React.ReactNode | null
}

export interface TableAction {
  label: string
  icon: React.ElementType
  route?: string
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  onClick?: (id: any) => void
}

interface FormRules {
  [field: string]: string[]
}

export interface FormMetaData {
  fields: FormField[]
  rules: FormRules
}

export interface FormField {
  name: string
  label: string
  placeholder: string
  type:
    | 'text'
    | 'number'
    | 'email'
    | 'password'
    | 'textarea'
    | 'select'
    | 'checkbox'
    | 'radio'
    | 'file'
    | 'datetime-local'
  required?: boolean
  options?: string[]
  rules?: string[]
}

export interface Filters {
  orderBy: string
  page: number
  perPage: number
  search: string
  orderDir: string
}
export interface Resource<T> {
  data: T
}

export interface PagedResource<T> {
  data: T[]
  links: string[]
  meta: {
    current_page: number
    from: number
    last_page: number
    first_page_url: string
    last_page_url: string
    next_page_url: string | null
    path: string
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
  }
}

export interface Admin {
  id: number
  username: string
  email: string
  email_verified_at: string
  created_at: string
  updated_at: string
}

export interface Role {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
}

export interface Permission {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
}

export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string
  created_at: string
  updated_at: string
}

export interface MenuItem {
  label: string
  icon: keyof typeof Icons
  route?: string
  items: MenuItem[]
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T &
  Readonly<{
    auth: {
      admin: {
        data: Admin
      }
    }
    incrudible: {
      routePrefix: string
      currentRouteName: string
      menu: {
        items: MenuItem[]
        top_right_items: {
          label: string
          route: string
        }[]
      }
    }
    ziggy: Config & {
      location: string
      query: string | string[][] | Record<string, string> | URLSearchParams | undefined
    }
  }>

interface Flight {
  id: number
  name: string
  number: string
  departure: string
  arrival: string
  departure_time: string
  arrival_time: string
  duration: string
  price: number
  created_at: string
  updated_at: string
}
