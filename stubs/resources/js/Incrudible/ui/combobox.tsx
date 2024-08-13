import { Button } from '@/Incrudible/ui/button'
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/Incrudible/ui/command'
import { Popover, PopoverContent, PopoverTrigger } from '@/Incrudible/ui/popover'
import { cn } from '@/lib/utils'
import { Check, ChevronsUpDown, X } from 'lucide-react'
import { SelectHTMLAttributes, forwardRef, useMemo, useState, useCallback } from 'react'
import { Badge } from '@/Incrudible/ui//badge'

export type UseSelectParams<V, Option, Multiple extends boolean> = {
  value: Multiple extends true ? V[] : V
  options: readonly Option[]
  onChange: (value: Multiple extends true ? V[] : V) => void
  placeholder?: string
  readOnly?: boolean
  multiple?: Multiple
}

export type UseSelectOptionsParams<V, Option> = {
  options: readonly Option[]
  getLabel?: (option: Option) => string
  getKey?: (option: Option) => string
  getValue?: (option: Option) => V
}

// More native props should pass through the props
type SelectProps = Pick<SelectHTMLAttributes<HTMLSelectElement>, 'name' | 'disabled' | 'required'>

function ComboboxInner<V, Option, Multiple extends boolean = false>(
  {
    value,
    options,
    onChange,
    getLabel = (option: Option) => option as unknown as string,
    getKey = (option: Option) => option as unknown as string,
    getValue = (option: Option) => option as unknown as V,
    placeholder,
    disabled,
    required,
    readOnly,
    multiple = false as Multiple,
    ...delegated
  }: UseSelectParams<V, Option, Multiple> & UseSelectOptionsParams<V, Option> & SelectProps,
  ref: React.ForwardedRef<HTMLButtonElement>,
) {
  const [open, setOpen] = useState(false)

  const optionsInValue = useMemo(() => {
    return options.filter((option) => {
      return multiple
        ? (value as V[]).some((v) => JSON.stringify(v) === JSON.stringify(getValue(option)))
        : JSON.stringify(value) === JSON.stringify(getValue(option))
    })
  }, [value, options, getValue, multiple])

  const onSelect = useCallback(
    (key: string) => {
      const option = options.find((option) => getKey(option) === key)
      const optionValue = getValue(option!)

      if (multiple) {
        const newValue = (value as V[]).some((v) => JSON.stringify(v) === JSON.stringify(optionValue))
          ? (value as V[]).filter((v) => JSON.stringify(v) !== JSON.stringify(optionValue))
          : [...(value as V[]), optionValue]
        onChange(newValue as Multiple extends true ? V[] : V)
      } else {
        onChange(optionValue as Multiple extends true ? V[] : V)
      }
    },
    [options, onChange, getKey, getValue, value, multiple],
  )

  const handleUnselect = (item: V) => {
    onChange(
      (value as V[]).filter((v) => JSON.stringify(v) !== JSON.stringify(item)) as Multiple extends true ? V[] : V,
    )
  }

  const handleUnselectAll: React.MouseEventHandler = (e) => {
    e.preventDefault()
    onChange([] as Multiple extends true ? V[] : V)
  }

  return (
    <Popover open={open} onOpenChange={setOpen} modal={true}>
      <PopoverTrigger asChild>
        <Button
          ref={ref}
          disabled={disabled || readOnly}
          variant="outline"
          role="combobox"
          aria-expanded={open}
          className="h-fit min-h-10 w-full justify-between py-1.5 focus-within:ring-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus-visible:ring-black"
        >
          {multiple ? (
            <div className="flex flex-wrap gap-x-1 gap-y-1">
              {optionsInValue.length ? (
                optionsInValue.map((item) => (
                  <Badge
                    variant="outline"
                    key={getKey(item)}
                    className="text-md border-icwest-gray-500 mr-1 max-w-80 rounded-md px-2"
                  >
                    <span className="max-w-36 overflow-hidden truncate">{getLabel(item)}</span>
                    <span
                      role="button"
                      aria-label="Close"
                      className="ml-1 rounded-full outline-none ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
                      onKeyDown={(e) => {
                        if (e.key === 'Enter') {
                          handleUnselect(getValue(item))
                        }
                      }}
                      onMouseDown={(e) => {
                        e.preventDefault()
                        e.stopPropagation()
                      }}
                      onClick={() => handleUnselect(getValue(item))}
                    >
                      <X className="h-3 w-3 text-muted-foreground hover:text-foreground" />
                    </span>
                  </Badge>
                ))
              ) : (
                <span className="text-muted-foreground">{placeholder}</span>
              )}
            </div>
          ) : optionsInValue.length ? (
            <span className="truncate">{getLabel(optionsInValue[0])}</span>
          ) : (
            <span className="text-muted-foreground">{placeholder}</span>
          )}
          <div className="flex">
            {multiple && (value as V[]).length > 0 && (
              <Button
                asChild
                size="icon"
                variant="ghost"
                onClick={handleUnselectAll}
                disabled={disabled}
                className="ml-2 flex size-4 cursor-pointer text-muted-foreground hover:text-foreground"
              >
                <X />
              </Button>
            )}
            <ChevronsUpDown className="ml-2 size-4 shrink-0 opacity-50" />
          </div>
        </Button>
      </PopoverTrigger>
      <PopoverContent className="popoverContent p-0">
        <Command>
          <CommandInput placeholder={placeholder} required={required} />
          <CommandList className="max-h-64 md:max-h-96">
            <CommandEmpty>No data found.</CommandEmpty>
            <CommandGroup>
              {options.map((option) => {
                const key = getKey(option)
                const label = getLabel(option)
                const itemValue = getValue(option)
                const isSelected = optionsInValue.some((o) => JSON.stringify(getValue(o)) === JSON.stringify(itemValue))
                return (
                  <CommandItem key={key} value={label} onSelect={() => onSelect(key)}>
                    <Check className={cn('mr-2 size-4', isSelected ? 'opacity-100' : 'opacity-0')} />
                    <span>{label}</span>
                  </CommandItem>
                )
              })}
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  )
}

export const Combobox = forwardRef(ComboboxInner)
