import { Package } from 'lucide-react'
import { SVGAttributes } from 'react'

export default function ApplicationLogo(props: SVGAttributes<SVGElement>) {
  return (
    <>
      <h1 className="flex items-center text-2xl font-semibold">
        <Package className="w-20 h-20 mr-4" />
        Incrudible
      </h1>
    </>
  )
}
