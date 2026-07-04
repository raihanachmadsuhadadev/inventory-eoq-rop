import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react"
import api, { TOKEN_KEY } from "../lib/api"

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [token, setToken] = useState(() => localStorage.getItem(TOKEN_KEY))
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(Boolean(localStorage.getItem(TOKEN_KEY)))

  const clearAuth = useCallback(() => {
    localStorage.removeItem(TOKEN_KEY)
    setToken(null)
    setUser(null)
  }, [])

  const fetchMe = useCallback(async () => {
    const storedToken = localStorage.getItem(TOKEN_KEY)

    if (!storedToken) {
      clearAuth()
      setLoading(false)
      return null
    }

    try {
      setLoading(true)
      const response = await api.get("/auth/me")
      const currentUser = response.data?.data?.user || null
      setUser(currentUser)
      return currentUser
    } catch (error) {
      clearAuth()
      throw error
    } finally {
      setLoading(false)
    }
  }, [clearAuth])

  const login = useCallback(async (email, password) => {
    const response = await api.post("/auth/login", { email, password })
    const nextToken = response.data?.data?.token
    const nextUser = response.data?.data?.user

    if (!nextToken || !nextUser) {
      throw new Error("Response login tidak valid")
    }

    localStorage.setItem(TOKEN_KEY, nextToken)
    setToken(nextToken)
    setUser(nextUser)

    return nextUser
  }, [])

  const logout = useCallback(async () => {
    try {
      if (localStorage.getItem(TOKEN_KEY)) {
        await api.post("/auth/logout")
      }
    } finally {
      clearAuth()
    }
  }, [clearAuth])

  useEffect(() => {
    if (!token) {
      setLoading(false)
      return
    }

    fetchMe().catch(() => null)
  }, [fetchMe, token])

  const value = useMemo(
    () => ({
      user,
      token,
      loading,
      login,
      logout,
      fetchMe,
      isAuthenticated: Boolean(token && user),
    }),
    [fetchMe, loading, login, logout, token, user],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)

  if (!context) {
    throw new Error("useAuth harus dipakai di dalam AuthProvider")
  }

  return context
}
