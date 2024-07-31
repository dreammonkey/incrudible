import * as React from 'react'

import { format, isValid, parse, set } from 'date-fns'
import { cn } from '@/lib/utils'
import { Calendar as CalendarIcon, ClockIcon, X } from 'lucide-react'
import { Popover, PopoverContent, PopoverTrigger } from './popover'
import { Calendar } from './calendar'
import InputMask from 'react-input-mask'
import { TimePicker } from './time-picker'
import { Button } from './button'

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  value: string
  disabled?: boolean
  valueFormat?: string
  dateFormat?: string
  timeFormat?: 'none' | 'HH:mm' | 'HH:mm:ss'
  dateMask?: string
  timeMask?: string
  datePlaceholder?: string
  timePlaceholder?: string
}

/**
 * A custom input component for selecting date and time.
 *
 * @component
 * @example
 * ```tsx
 * <DateTimeInput
 *   value={selectedDateTime}
 *   onChange={handleDateTimeChange}
 *   dateFormat="dd/MM/yyyy"
 *   timeFormat="HH:mm"
 *   datePlaceholder="dd/mm/yyyy"
 *   timePlaceholder="hh:mm"
 * />
 * ```
 *
 * @param className - The CSS class name for the component.
 * @param value - The selected date and time value.
 * @param onChange - The callback function triggered when the date and time value changes.
 * @param valueFormat - The format of the selected date and time value. Default is "yyyy-MM-dd'T'HH:mm:ssXX".
 * @param dateFormat - The format of the date displayed in the input. Default is "dd/MM/yyyy".
 * @param timeFormat - The format of the time displayed in the input. Default is "HH:mm".
 * @param dateMask - The mask for the date input. Default is "99/99/9999".
 * @param timeMask - The mask for the time input. Default is "99:99".
 * @param datePlaceholder - The placeholder text for the date input. Default is "dd/mm/yyyy".
 * @param timePlaceholder - The placeholder text for the time input. Default is "hh:mm".
 * @param readOnly - Whether the input is read-only.
 * @param disabled - Whether the input is disabled.
 * @param props - Additional props to be spread to the component.
 * @returns The rendered DateTimeInput component.
 */
const DateTimeInput = React.forwardRef<HTMLInputElement, InputProps>(
  (
    {
      className,
      value,
      onChange,
      valueFormat = "yyyy-MM-dd'T'HH:mm:ss'Z'",
      dateFormat = 'dd/MM/yyyy',
      timeFormat = 'HH:mm',
      dateMask = '99/99/9999',
      timeMask = '99:99',
      datePlaceholder = 'dd/mm/yyyy',
      timePlaceholder = 'hh:mm',
      readOnly,
      disabled,
      ...props
    },
    ref,
  ) => {
    // Use this as a starting point for the calendar when the input is empty/invalid
    const [month, setMonth] = React.useState(new Date())

    // Hold the selected date in state
    const [selectedDate, setSelectedDate] = React.useState<Date | undefined>(undefined)

    // Hold the date input value in state
    const [dateInputValue, setDateInputValue] = React.useState('')

    // Hold the time input value in state
    const [timeInputValue, setTimeInputValue] = React.useState('')

    // Handle the selection of a date in the calendar
    const handleDayPickerSelect = (date: Date | undefined) => {
      if (!date) {
        setDateInputValue('')
        setSelectedDate(undefined)
      } else {
        if (timeFormat !== 'none') {
          if (selectedDate) {
            date = set(date, {
              hours: selectedDate.getHours(),
              minutes: selectedDate.getMinutes(),
              seconds: selectedDate.getSeconds(),
            })
          } else {
            setTimeInputValue(format(date, timeFormat))
          }
        }

        setSelectedDate(date)
        setMonth(date)
        setDateInputValue(format(date, dateFormat))
      }
    }

    // Handle the change of the date input
    const handleDateInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
      setDateInputValue(e.target.value)

      const parsedInput = parse(e.target.value, dateFormat, new Date())

      if (isValid(parsedInput)) {
        if (timeFormat !== 'none') {
          if (selectedDate) {
            const newDate = set(parsedInput, {
              hours: selectedDate.getHours(),
              minutes: selectedDate.getMinutes(),
              seconds: selectedDate.getSeconds(),
            })
            setSelectedDate(newDate)
            setMonth(newDate)
          } else {
            setTimeInputValue(format(parsedInput, timeFormat))
            setSelectedDate(parsedInput)
            setMonth(parsedInput)
          }
        } else {
          setSelectedDate(parsedInput)
          setMonth(parsedInput)
        }
      }
    }

    // Validate the date input onBlur
    const validateDateInput = (e: React.ChangeEvent<HTMLInputElement>) => {
      const parsedInput = parse(e.target.value, dateFormat, new Date())

      if (!isValid(parsedInput)) {
        setDateInputValue('')
        setTimeInputValue('')
        setSelectedDate(undefined)
      }
    }

    // Handle the change of the time input
    const handleTimeInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
      setTimeInputValue(e.target.value)

      const parsedInput = parse(e.target.value, timeFormat, new Date())

      if (isValid(parsedInput)) {
        if (selectedDate) {
          const newDate = set(selectedDate, {
            hours: parsedInput.getHours(),
            minutes: parsedInput.getMinutes(),
            seconds: parsedInput.getSeconds(),
          })
          setSelectedDate(newDate)
          setMonth(newDate)
        } else {
          setSelectedDate(parsedInput)
          setMonth(parsedInput)
        }
      }
    }

    // Validate the time input onBlur
    const validateTimeInput = (e: React.ChangeEvent<HTMLInputElement>) => {
      const parsedInput = parse(e.target.value, timeFormat, new Date())

      if (!isValid(parsedInput)) {
        // Reset the time input value to the previous value if selectedDate is defined
        if (selectedDate) {
          setTimeInputValue(format(selectedDate, timeFormat))
        } else {
          setTimeInputValue('')
        }
      }
    }

    // Update the input value when the value prop changes
    React.useEffect(() => {
      const parsedValue = parse(value, valueFormat, new Date())

      if (isValid(parsedValue)) {
        // set the selected date to the parsed value
        setSelectedDate(parsedValue)
        // set the selected month of the calendar to the parsed value
        setMonth(parsedValue)
        // set the input values to the parsed value
        setDateInputValue(format(parsedValue, dateFormat))
        if (timeFormat !== 'none') {
          setTimeInputValue(format(parsedValue, timeFormat))
        }
      } else {
        // when the input is invalid/empty, reset the selected month of the calendar to the current month/year
        setMonth(new Date())
      }
    }, [value, valueFormat])

    React.useEffect(() => {
      const formattedDate = selectedDate ? format(selectedDate, valueFormat) : ''

      if (formattedDate !== value) {
        // Dispatch the formatted date to the parent component
        onChange?.({ target: { value: formattedDate } } as React.ChangeEvent<HTMLInputElement>)
      }
    }, [selectedDate, valueFormat])

    const reset = () => {
      setDateInputValue('')
      setTimeInputValue('')
      setSelectedDate(undefined)
    }

    return (
      <div
        className={cn(
          'group flex h-10 w-full items-center justify-start rounded-md border border-input bg-background px-3 ring-offset-background placeholder:text-muted-foreground focus-within:ring-2 focus-within:ring-black focus-within:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50',
          className,
        )}
        ref={ref}
        {...props}
      >
        <Popover>
          <PopoverTrigger disabled={disabled || readOnly}>
            <CalendarIcon className="size-4 cursor-pointer" />
          </PopoverTrigger>
          <InputMask
            mask={dateMask}
            className="mr-2 w-full border-none px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-0 bg-background grow"
            placeholder={datePlaceholder}
            disabled={disabled}
            readOnly={readOnly}
            value={dateInputValue}
            onChange={handleDateInputChange}
            onBlur={validateDateInput}
            {...props}
          />
          <PopoverContent className="w-auto p-0">
            <Calendar
              // initialFocus
              month={month}
              onMonthChange={setMonth}
              mode="single"
              selected={selectedDate}
              onSelect={handleDayPickerSelect}
            />
          </PopoverContent>
        </Popover>
        {timeFormat !== 'none' && (
          <Popover>
            <PopoverTrigger disabled={disabled || readOnly}>
              <ClockIcon className="size-4 cursor-pointer" />
            </PopoverTrigger>
            <InputMask
              mask={timeMask}
              className="mr-2 w-full border-none px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-0 bg-background"
              value={timeInputValue}
              placeholder={timePlaceholder}
              disabled={disabled}
              readOnly={readOnly}
              onChange={handleTimeInputChange}
              onBlur={validateTimeInput}
            />
            <PopoverContent className="w-auto p-2">
              <TimePicker date={selectedDate} setDate={setSelectedDate} timeFormat={timeFormat} />
            </PopoverContent>
          </Popover>
        )}

        <div className="flex w-24">
          {selectedDate && (
            <Button
              asChild
              size="icon"
              variant="ghost"
              onClick={reset}
              disabled={disabled || readOnly}
              className="ml-2 flex size-4 cursor-pointer text-muted-foreground hover:text-foreground"
            >
              <X />
            </Button>
          )}
        </div>
      </div>
    )
  },
)
;(DateTimeInput as React.FC<InputProps>).displayName = 'DateTimeInput'

export { DateTimeInput }
