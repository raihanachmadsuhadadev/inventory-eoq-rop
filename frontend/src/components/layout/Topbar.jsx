import { Bell, Search } from "lucide-react"

function Topbar() {
  return (
    <header className="topbar">
      <div>
        <p className="topbar-title">Inventory Control System</p>
        <p className="topbar-date">Monitoring stok, EOQ, dan ROP</p>
      </div>

      <div className="topbar-actions">
        <div className="topbar-icon" aria-label="Pencarian">
          <Search size={19} />
        </div>
        <div className="topbar-icon" aria-label="Notifikasi">
          <Bell size={19} />
        </div>
        <div className="user-avatar" aria-label="Admin">
          A
        </div>
      </div>
    </header>
  )
}

export default Topbar
