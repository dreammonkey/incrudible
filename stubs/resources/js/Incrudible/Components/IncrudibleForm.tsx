import { convertLaravelToZod } from '@/lib/utils'
import { InputField, FormRules } from '@/types/incrudible'
import { zodResolver } from '@hookform/resolvers/zod'
import { ControllerRenderProps, useForm, UseFormWatch } from 'react-hook-form'
import { z } from 'zod'
import { Button } from '@/Incrudible/ui/button'
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/Incrudible/ui/form'
import { Input } from '@/Incrudible/ui/input'
import { forwardRef, useImperativeHandle } from 'react'
import { DateTimeInput } from '@/Incrudible/ui/date-time-input'
import { Switch } from '@/Incrudible/ui/switch'
import { Textarea } from '@/Incrudible/ui/textarea'
import { Combobox } from '@/Incrudible/ui/combobox'
import { InputFieldType } from '../Enum/Incrudible'

interface FormProps<T> {
  fields: InputField[]
  rules: FormRules
  initialData?: T
  onFormSubmit?: (data: T) => void
  isProcessing?: boolean
  className?: string
  readOnly?: boolean
}

export interface FormRef<T> {
  submit: () => void
  reset: (data: T) => void
  clearErrors: () => void
  setError: (name: string, error: { type: string; message: string }) => void
  watch: UseFormWatch<{
    [x: string]: any
  }>
}

const renderInput = (
  fieldData: InputField,
  field: ControllerRenderProps<
    {
      [x: string]: any
    },
    string
  >,
  readOnly: boolean = false,
) => {
  switch (fieldData.type) {
    case InputFieldType.Text:
    case InputFieldType.Number:
    case InputFieldType.Email:
    case InputFieldType.Password:
      return (
        <Input
          {...field}
          type={fieldData.type}
          placeholder={fieldData.placeholder}
          readOnly={readOnly}
        />
      )

    case InputFieldType.Textarea:
      return (
        <Textarea
          className="min-h-32"
          {...field}
          placeholder={fieldData.placeholder}
          readOnly={readOnly}
        />
      )

    case InputFieldType.DateTimeLocal:
      // TODO convert php format to date-fns format
      return (
        <DateTimeInput
          {...field}
          valueFormat="yyyy-MM-dd HH:mm:ss"
          readOnly={readOnly}
        />
      )

    case InputFieldType.Checkbox:
      return (
        <div className="flex h-10 items-center">
          <Switch
            {...field}
            checked={field.value}
            onCheckedChange={(value) => field.onChange(value)}
            disabled={readOnly}
          />
        </div>
      )

    case InputFieldType.Select:
      return (
        <Combobox
          placeholder={fieldData.placeholder}
          options={fieldData.options}
          value={field.value}
          getKey={fieldData.getKey}
          getValue={fieldData.getValue}
          getLabel={fieldData.getLabel}
          onChange={(value) => field.onChange(value)}
        />
      )

    default:
      return <Input {...field} readOnly={readOnly} />
  }
}

const IncrudibleForm = forwardRef(
  <T extends object>(
    {
      fields,
      rules,
      initialData,
      onFormSubmit,
      isProcessing,
      className,
      readOnly = false,
    }: FormProps<T>,
    ref: React.Ref<FormRef<T>>,
  ) => {
    // console.log('IncrudibleForm -- render')
    // console.log(metadata)
    // console.log({ metadata, data })

    const formSchema = convertLaravelToZod(rules)
    // console.log({ formSchema })

    // 1. Define your form.
    const form = useForm<z.infer<typeof formSchema>>({
      resolver: zodResolver(formSchema),
      defaultValues: initialData,
    })

    // 2. Define a submit handler.
    function onSubmit(values: z.infer<typeof formSchema>) {
      onFormSubmit?.(values as T)
    }

    useImperativeHandle(ref, () => {
      return {
        submit: form.handleSubmit(onSubmit),
        reset: form.reset,
        clearErrors: form.clearErrors,
        setError: form.setError,
        watch: form.watch,
      }
    }, [form, onSubmit])

    // console.log(metadata.fields[0])
    // console.log('username' in metadata.rules)
    const _filteredFields = fields.filter((field) => field.name in rules)
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
                        <FormControl>
                          {renderInput(fieldData, field, readOnly)}
                        </FormControl>
                        <FormMessage />
                      </div>
                    </FormItem>
                  )}
                />
              ))}
            </div>
            <div className="mt-4 flex items-center justify-between">
              {!readOnly && (
                <div className="flex items-center space-x-2">
                  <Button
                    isLoading={isProcessing}
                    disabled={!isDirty || isProcessing}
                    type="submit"
                  >
                    Save
                  </Button>

                  {isDirty && (
                    <Button
                      variant="link"
                      disabled={!isDirty || isProcessing}
                      type="button"
                      onClick={() => form.reset()}
                    >
                      cancel
                    </Button>
                  )}
                </div>
              )}
            </div>
          </form>
        </Form>
      </section>
    )
  },
)

export default IncrudibleForm
