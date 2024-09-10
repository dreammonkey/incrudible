import './bootstrap'
import '../css/incrudible.css'

import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import React from 'react'
import { ThemeProvider } from '@/Incrudible/Context/theme-provider'

const appName = import.meta.env.VITE_APP_NAME || 'Incrudible'

const queryClient = new QueryClient()

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Incrudible/Pages/${name}.tsx`, import.meta.glob('./Incrudible/Pages/**/*.tsx')),
  setup({ el, App, props }) {
    const root = createRoot(el)

    root.render(
      <React.StrictMode>
        <QueryClientProvider client={queryClient}>
          <ThemeProvider defaultTheme="dark" storageKey="vite-ui-theme">
            <App {...props} />
          </ThemeProvider>
          {/* <ReactQueryDevtools initialIsOpen={false} /> */}
        </QueryClientProvider>
      </React.StrictMode>,
    )
  },
  progress: {
    color: '#4B5563',
  },
})
