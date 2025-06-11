// useRecentlySuccessful.ts
import { useState, useCallback } from 'react'

export function useRecentlySuccessful(timeout: number = 3000) {
  const [recentlySuccessful, setRecentlySuccessful] = useState(false)

  const triggerSuccess = useCallback(() => {
    setRecentlySuccessful(true)

    // Reset the recentlySuccessful state after the specified timeout
    const tid = setTimeout(() => {
      setRecentlySuccessful(false)
    }, timeout)

    return () => clearTimeout(tid)
  }, [timeout])

  return {
    recentlySuccessful,
    triggerSuccess,
  }
}
