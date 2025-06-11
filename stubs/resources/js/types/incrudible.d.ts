import { InputFieldType } from '@/Incrudible/Enum/Incrudible'
import { CrudRelationType } from '@/Incrudible/Helpers/incrudible'
import * as Icons from 'lucide-react'
import { Config } from 'ziggy-js'

// https://github.com/sveltejs/kit/issues/1997#issuecomment-887614097
export type Typify<T> = { [K in keyof T]: Typify<T[K]> }

// Redeclare forwardRef
// SEE: https://fettblog.eu/typescript-react-generic-forward-refs/
declare module 'react' {
  function forwardRef<T, P = {}>(
    render: (props: P, ref: React.Ref<T>) => React.ReactNode | null,
  ): (props: P & React.RefAttributes<T>) => React.ReactNode | null
}

export interface TableActionConfig {
  label: string
  variant?:
    | 'default'
    | 'destructive'
    | 'outline'
    | 'secondary'
    | 'ghost'
    | 'link'
    | null
    | undefined
  icon: keyof typeof Icons
  action: string
  type: 'button' | 'link'
}

interface FormRules {
  [field: string]: string[]
}

export interface FormMetaData {
  fields: InputField[]
  rules: FormRules
}

interface InputFieldBase {
  name: string
  label: string
  placeholder: string
  type: InputFieldType
  required?: boolean
  rules?: string[]
}

export interface InputFieldText extends InputFieldBase {
  type: InputFieldType.Text
}

export interface InputFieldNumber extends InputFieldBase {
  type: InputFieldType.Number
}

export interface InputFieldEmail extends InputFieldBase {
  type: InputFieldType.Email
}

export interface InputFieldPassword extends InputFieldBase {
  type: InputFieldType.Password
}

export interface InputFieldTextarea extends InputFieldBase {
  type: InputFieldType.Textarea
}

export interface InputFieldSelect extends InputFieldBase {
  type: InputFieldType.Select
  multiple?: boolean
  options: any[]
  getLabel: (option: any) => string
  getValue: (option: any) => string
  getKey: (option: any) => string
}

export interface InputFieldCheckbox extends InputFieldBase {
  type: InputFieldType.Checkbox
}

export interface InputFieldRadio extends InputFieldBase {
  type: InputFieldType.Radio
}

export interface InputFieldFile extends InputFieldBase {
  type: InputFieldType.File
}

export interface InputFieldDateTimeLocal extends InputFieldBase {
  type: InputFieldType.DateTimeLocal
}

export type InputField =
  | InputFieldText
  | InputFieldNumber
  | InputFieldEmail
  | InputFieldPassword
  | InputFieldTextarea
  | InputFieldSelect
  | InputFieldCheckbox
  | InputFieldRadio
  | InputFieldFile
  | InputFieldDateTimeLocal

export interface Filters {
  orderBy: string
  page: number
  perPage: number
  search?: string
  orderDir: string
}

export interface PagedResource<T> {
  data: T[]
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number
    last_page: number
    links: {
      url: string | null
      label: string
      active: boolean
    }[]
    path: string
    per_page: number
    to: number
    total: number
  }
}

export type CrudResource = Record<string, any> & {
  actions: { action: string; url: string }[]
}

export interface Resource<T> {
  data: T & { actions: { action: string; url: string }[] }
}

export interface CrudRelationBase<T> {
  name: CrudRelationType
  type: string
  // model: string
  // enabled: boolean
}

export interface PagingConfig {
  default: number
  options: number[]
}

export interface HasManyCrudRelation<T> extends CrudRelationBase<T> {
  type: CrudRelationType.HasMany
  route: string
  listable: string[]
  sortable: string[]
  paging: PagingConfig
  actions: TableActionConfig[]
}

export interface BelongsToManyCrudRelation<T> extends CrudRelationBase<T> {
  type: CrudRelationType.BelongsToMany
  route: string
  idKey: keyof T
  labelKey: keyof T
}
export type CrudRelation<T> =
  | HasManyCrudRelation<T>
  | BelongsToManyCrudRelation<T>

export interface Admin extends CrudResource {
  id: number
  username: string
  email: string
  email_verified_at: string
  created_at: string
  updated_at: string
}

export interface Role extends CrudResource {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
  permissions: Permission[]
}

export interface Permission extends CrudResource {
  id: number
  name: string
  guard_name: string
  created_at: string
  updated_at: string
}

export interface User extends CrudResource {
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

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>,
> = T &
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
      tenantId: string
    }
    ziggy: Config & {
      location: string
      query:
        | string
        | string[][]
        | Record<string, string>
        | URLSearchParams
        | undefined
    }
  }>
