import { Filters } from '@/types/incrudible'
import axios from 'axios'

export async function getAdmins(route: string, filters?: Filters) {
  const res = await axios.get(route, {
    params: {
      orderBy: filters?.orderBy ?? undefined,
      page: filters?.page ?? undefined,
      perPage: filters?.perPage ?? undefined,
      search: filters?.search ?? undefined,
      orderDir: filters?.orderDir ?? undefined,
    },
  })
  return res.data
}
