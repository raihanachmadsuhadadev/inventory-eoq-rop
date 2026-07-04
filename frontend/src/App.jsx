import { Navigate, Route, Routes } from "react-router-dom"
import ProtectedRoute from "./components/auth/ProtectedRoute"
import { useAuth } from "./context/AuthContext"
import Dashboard from "./pages/Dashboard"
import Login from "./pages/Login"

function App() {
  const { isAuthenticated, loading } = useAuth()

  const homeRoute = loading ? (
    <main className="auth-loading">
      <div className="neo-card compact">Memeriksa sesi...</div>
    </main>
  ) : (
    <Navigate to={isAuthenticated ? "/dashboard" : "/login"} replace />
  )

  return (
    <Routes>
      <Route path="/" element={homeRoute} />
      <Route
        path="/login"
        element={
          isAuthenticated ? <Navigate to="/dashboard" replace /> : <Login />
        }
      />
      <Route
        path="/dashboard"
        element={
          <ProtectedRoute>
            <Dashboard />
          </ProtectedRoute>
        }
      />
    </Routes>
  )
}

export default App
