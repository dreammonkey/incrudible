import { FormRules } from '@/types/incrudible'
import { clsx, type ClassValue } from 'clsx'
import { format, parseISO } from 'date-fns'
import { twMerge } from 'tailwind-merge'
import { z } from 'zod'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export const formatDate = (dateString: string): string => {
  const date = parseISO(dateString)
  return format(date, 'yyyy-MM-dd HH:mm:ss')
}

export const convertLaravelToZod = (rules: FormRules): z.ZodObject<any> => {
  const zodSchema: { [key: string]: z.ZodTypeAny } = {}

  for (const [field, validations] of Object.entries(rules)) {
    // start with a generic schema
    let schema: z.ZodSchema<any> = z.any()

    validations.forEach((validation) => {
      if (validation === 'required') {
        schema = schema.refine((value) => value !== undefined && value !== null, {
          message: `${field} is required`,
        })
      } else if (validation === 'nullable') {
        schema = schema.nullable()
      } else if (validation === 'string') {
        schema = z.string()
      } else if (validation === 'date') {
        schema = z.string().refine((value) => !isNaN(Date.parse(value)), {
          message: `${field} must be a valid date`,
        })
      } else if (validation.startsWith('date_format:')) {
        const dateFormat = validation.split(':')[1]
        schema = z.string().refine((value) => !isNaN(Date.parse(value)), {
          message: `${field} must be a valid date`,
        })
      } else if (validation.startsWith('min:')) {
        const minLength = parseInt(validation.split(':')[1])
        if (schema instanceof z.ZodString) {
          schema = schema.min(minLength, `${field} must have at least ${minLength} characters`)
        } else if (schema instanceof z.ZodNumber) {
          schema = schema.min(minLength, `${field} must be at least ${minLength}`)
        }
      } else if (validation.startsWith('max:')) {
        const maxLength = parseInt(validation.split(':')[1])
        if (schema instanceof z.ZodString) {
          schema = schema.max(maxLength, `${field} can have at most ${maxLength} characters`)
        } else if (schema instanceof z.ZodNumber) {
          schema = schema.max(maxLength, `${field} can be at most ${maxLength}`)
        }
      } else if (validation.startsWith('after_or_equal:')) {
        const minDate = validation.split(':')[1]
        schema = schema.refine((value) => new Date(value) >= new Date(minDate), {
          message: `${field} must be after or equal to ${minDate}`,
        })
      } else if (validation.startsWith('before_or_equal:')) {
        const maxDate = validation.split(':')[1]
        schema = schema.refine((value) => new Date(value) <= new Date(maxDate), {
          message: `${field} must be before or equal to ${maxDate}`,
        })
      }
    })

    zodSchema[field] = schema
  }

  return z.object(zodSchema)
}
