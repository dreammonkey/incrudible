import './bootstrap'
import '../css/incrudible.css'

import { ThemeProvider } from '@/Incrudible/Context/theme-provider'
import { createInertiaApp } from '@inertiajs/react'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'

const appName = import.meta.env.VITE_APP_NAME || 'Incrudible'

const queryClient = new QueryClient()

createInertiaApp({
  pages: './Incrudible/Pages',
  title: (title) => (title ? `${title} - ${appName}` : appName),
  strictMode: true,
  withApp(app) {
    return (
      <QueryClientProvider client={queryClient}>
        <ThemeProvider defaultTheme="dark" storageKey="vite-ui-theme">
          {app}
        </ThemeProvider>
      </QueryClientProvider>
    )
  },
  progress: {
    color: '#4B5563',
  },
})
