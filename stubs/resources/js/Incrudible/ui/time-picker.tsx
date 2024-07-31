import { Label } from './label'
import * as React from 'react'
import { TimePickerInput } from './time-picker-input'

interface TimePickerProps {
  date: Date | undefined
  setDate: (date: Date | undefined) => void
  timeFormat?: 'HH:mm' | 'HH:mm:ss'
}

export function TimePicker({ date, setDate, timeFormat = 'HH:mm:ss' }: Readonly<TimePickerProps>) {
  const minuteRef = React.useRef<HTMLInputElement>(null)
  const hourRef = React.useRef<HTMLInputElement>(null)
  const secondRef = React.useRef<HTMLInputElement>(null)

  return (
    <div className="flex items-end gap-2">
      <div className="grid gap-1 text-center">
        <Label htmlFor="hours" className="text-xs">
          hh
        </Label>
        <TimePickerInput
          picker="hours"
          date={date}
          setDate={setDate}
          ref={hourRef}
          onRightFocus={() => minuteRef.current?.focus()}
        />
      </div>
      <div className="grid gap-1 text-center">
        <Label htmlFor="minutes" className="text-xs">
          mm
        </Label>
        <TimePickerInput
          picker="minutes"
          date={date}
          setDate={setDate}
          ref={minuteRef}
          onLeftFocus={() => hourRef.current?.focus()}
          onRightFocus={() => secondRef.current?.focus()}
        />
      </div>
      {timeFormat === 'HH:mm:ss' && (
        <div className="grid gap-1 text-center">
          <Label htmlFor="seconds" className="text-xs">
            ss
          </Label>
          <TimePickerInput
            picker="seconds"
            date={date}
            setDate={setDate}
            ref={secondRef}
            onLeftFocus={() => minuteRef.current?.focus()}
          />
        </div>
      )}
      {/* <div className="flex h-10 items-center">
        <Clock className="ml-2 size-4" />
      </div> */}
    </div>
  )
}
