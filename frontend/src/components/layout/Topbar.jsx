import { Bell, LogOut, Search } from "lucide-react"
import { useNavigate } from "react-router-dom"
import { useAuth } from "../../context/AuthContext"
import NeumorphicButton from "../ui/NeumorphicButton"

function Topbar() {
  const navigate = useNavigate()
  const { logout, user } = useAuth()
  const roleName = user?.role?.name || "Tanpa role"
  const initials =
    user?.name
      ?.split(" ")
      .map((part) => part[0])
      .join("")
      .slice(0, 2)
      .toUpperCase() || "U"

  const handleLogout = async () => {
    await logout()
    navigate("/login", { replace: true })
  }

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
        <div className="topbar-user">
          <div className="user-avatar" aria-label={user?.name || "User"}>
            {initials}
          </div>
          <div>
            <p>{user?.name || "User"}</p>
            <span>{roleName}</span>
          </div>
        </div>
        <NeumorphicButton className="logout-button" onClick={handleLogout}>
          <LogOut size={17} />
          Logout
        </NeumorphicButton>
      </div>
    </header>
  )
}

export default Topbar
