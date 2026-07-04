import { Navigate } from "react-router-dom"
import { useAuth } from "../../context/AuthContext"

function ProtectedRoute({ children }) {
  const { isAuthenticated, loading } = useAuth()

  if (loading) {
    return (
      <main className="auth-loading">
        <div className="neo-card compact">Memeriksa sesi...</div>
      </main>
    )
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  return children
}

export default ProtectedRoute
