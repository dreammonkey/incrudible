import { Sliders } from 'lucide-react'
import { MainNavigation } from './MainNavigation'

const SideBar: React.FC = () => {
  return (
    <div className="bg-gray-100 dark:bg-gray-900 hidden md:flex">
      <div className="border border-gray-200 dark:border-none  h-screen w-64 shadow-x">
        <div className="flex flex-col flex-1">
          <div className="flex flex-grow flex-col space-y-1">
            <div className="inline-flex py-2 md:py-4 px-2 md:px-4 items-center justify-center uppercase text-gray-800  border-b">
              <Sliders className="w-5 h-15 mr-2" /> Incrudible
            </div>
            <MainNavigation />
          </div>
        </div>
      </div>
    </div>
  )
}

export default SideBar
