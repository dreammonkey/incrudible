import { convertLaravelToZod } from '@/lib/utils'
import { FormMetaData } from '@/types/incrudible'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { Button } from '@/Incrudible/ui/button'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/Incrudible/ui/form'
import { Input } from '@/Incrudible/ui/input'
import { forwardRef, useImperativeHandle } from 'react'

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
            <div className="grid gap-4">
              {_filteredFields.map((fieldData) => (
                <FormField
                  key={fieldData.name}
                  control={form.control}
                  name={fieldData.name}
                  render={({ field }) => (
                    <FormItem>
                      <div className="grid gap-2">
                        <FormLabel htmlFor={fieldData.name}>{fieldData.label}</FormLabel>
                        <FormControl>
                          <Input
                            type={fieldData.type}
                            // id={field.name}
                            // name={field.name}
                            placeholder={fieldData.placeholder}
                            // required={field.required}
                            // value={formValues[field.name] || ''}
                            // onChange={handleChange}
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </div>
                    </FormItem>
                  )}
                />
              ))}
              <div className="mt-4 flex items-center justify-between">
                <Button disabled={!isDirty} type="submit">
                  Save
                </Button>
              </div>
            </div>
          </form>
        </Form>
      </section>
    )
  },
)

export default IncrudibleForm
