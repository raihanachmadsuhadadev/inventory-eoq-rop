import Sidebar from "../components/layout/Sidebar"
import Topbar from "../components/layout/Topbar"

function AppLayout({ children }) {
  return (
    <div className="app-shell">
      <Sidebar />
      <Topbar />
      <main className="app-main">
        <div className="app-content">
          <div className="app-content-inner">{children}</div>
        </div>
      </main>
    </div>
  )
}

export default AppLayout
