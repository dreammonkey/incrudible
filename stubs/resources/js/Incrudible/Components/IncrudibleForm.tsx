import { convertLaravelToZod } from '@/lib/utils'
import { FormField as FormFieldType, FormMetaData } from '@/types/incrudible'
import { zodResolver } from '@hookform/resolvers/zod'
import { ControllerRenderProps, useForm } from 'react-hook-form'
import { z } from 'zod'
import { Button } from '@/Incrudible/ui/button'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/Incrudible/ui/form'
import { Input } from '@/Incrudible/ui/input'
import { forwardRef, useImperativeHandle } from 'react'
import { DateTimeInput } from '../ui/date-time-input'
import { Switch } from '../ui/switch'

interface FormProps<T> {
  metadata: FormMetaData
  data?: T
  onFormSubmit?: (data: T) => void
  onChange?: (data: T) => void
  className?: string
}

export interface FormRef<T> {
  submit: () => void
  reset: (data: T) => void
  clearErrors: () => void
  setError: (name: string, error: { type: string; message: string }) => void
}

const renderInput = (
  fieldData: FormFieldType,
  field: ControllerRenderProps<
    {
      [x: string]: any
    },
    string
  >,
) => {
  switch (fieldData.type) {
    case 'text':
    case 'number':
    case 'email':
    case 'password':
      return <Input {...field} type={fieldData.type} placeholder={fieldData.placeholder} />

    case 'textarea':
      return (
        <textarea
          className="block w-full px-3 py-2 border rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder={fieldData.placeholder}
          {...field}
        />
      )

    case 'datetime-local':
      // TODO convert php format to date-fns format
      return <DateTimeInput {...field} valueFormat="yyyy-MM-dd HH:mm:ss" />

    case 'checkbox':
      return (
        <div className="flex h-10 items-center">
          <Switch {...field} checked={field.value} onCheckedChange={(value) => field.onChange(value)} />
        </div>
      )

    default:
      return <Input {...field} />
  }
}

const IncrudibleForm = forwardRef(
  <T extends {}>({ metadata, data, onFormSubmit, onChange, className }: FormProps<T>, ref: React.Ref<FormRef<T>>) => {
    // console.log(metadata)
    // console.log({ metadata, data })

    const formSchema = convertLaravelToZod(metadata.rules)
    // console.log({ formSchema })

    // 1. Define your form.
    const form = useForm<z.infer<typeof formSchema>>({
      resolver: zodResolver(formSchema),
      defaultValues: { ...data },
    })

    form.watch((data) => {
      onChange?.(form.getValues() as T)
    })

    // 2. Define a submit handler.
    function onSubmit(values: z.infer<typeof formSchema>) {
      // Do something with the form values.
      // ✅ This will be type-safe and validated.
      // console.log(values)
      onFormSubmit?.(values as T)
    }

    useImperativeHandle(ref, () => {
      return {
        submit: form.handleSubmit(onSubmit),
        reset: form.reset,
        clearErrors: form.clearErrors,
        setError: form.setError,
      }
    }, [form, onSubmit])

    // console.log(metadata.fields[0])
    // console.log('username' in metadata.rules)
    const _filteredFields = metadata.fields.filter((field) => field.name in metadata.rules)
    // console.log({ _filteredFields })

    const isDirty = form.formState.isDirty

    return (
      <section className={className}>
        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)}>
            <div className="grid gap-4 md:grid-cols-2">
              {_filteredFields.map((fieldData) => (
                <FormField
                  key={fieldData.name}
                  control={form.control}
                  name={fieldData.name}
                  render={({ field }) => (
                    <FormItem>
                      <div className="flex flex-col gap-2">
                        <FormLabel htmlFor={fieldData.name}>
                          {fieldData.label + (fieldData.required ? ' *' : '')}
                        </FormLabel>
                        <FormControl>{renderInput(fieldData, field)}</FormControl>
                        <FormMessage />
                      </div>
                    </FormItem>
                  )}
                />
              ))}
            </div>
            <div className="mt-4 flex items-center justify-between">
              <Button disabled={!isDirty} type="submit">
                Save
              </Button>
            </div>
          </form>
        </Form>
      </section>
    )
  },
)

export default IncrudibleForm
