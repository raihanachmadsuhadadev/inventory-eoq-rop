import { Navigate, Route, Routes } from "react-router-dom"
import ProtectedRoute from "./components/auth/ProtectedRoute"
import { useAuth } from "./context/AuthContext"
import Categories from "./pages/Categories"
import Dashboard from "./pages/Dashboard"
import Hubs from "./pages/Hubs"
import Login from "./pages/Login"
import Products from "./pages/Products"
import Shifts from "./pages/Shifts"
import Suppliers from "./pages/Suppliers"

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
      <Route
        path="/categories"
        element={
          <ProtectedRoute>
            <Categories />
          </ProtectedRoute>
        }
      />
      <Route
        path="/hubs"
        element={
          <ProtectedRoute>
            <Hubs />
          </ProtectedRoute>
        }
      />
      <Route
        path="/shifts"
        element={
          <ProtectedRoute>
            <Shifts />
          </ProtectedRoute>
        }
      />
      <Route
        path="/suppliers"
        element={
          <ProtectedRoute>
            <Suppliers />
          </ProtectedRoute>
        }
      />
      <Route
        path="/products"
        element={
          <ProtectedRoute>
            <Products />
          </ProtectedRoute>
        }
      />
    </Routes>
  )
}

export default App
