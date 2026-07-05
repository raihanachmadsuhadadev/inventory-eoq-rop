import { Plus, Search } from "lucide-react"
import { useEffect, useMemo, useState } from "react"
import NeumorphicButton from "../components/ui/NeumorphicButton"
import NeumorphicCard from "../components/ui/NeumorphicCard"
import NeumorphicInput from "../components/ui/NeumorphicInput"
import { useAuth } from "../context/AuthContext"
import AppLayout from "../layouts/AppLayout"
import api from "../lib/api"

const transactionTypes = [
  { value: "in", label: "Masuk" },
  { value: "out", label: "Keluar" },
  { value: "adjustment", label: "Adjustment" },
]

const initialForm = {
  product_id: "",
  hub_id: "",
  type: "in",
  quantity: "",
  notes: "",
}

function typeLabel(type) {
  return transactionTypes.find((item) => item.value === type)?.label || type
}

function StockTransactions() {
  const { user } = useAuth()
  const canCreate = ["super_admin", "admin_gudang"].includes(user?.role?.slug)
  const [transactions, setTransactions] = useState([])
  const [products, setProducts] = useState([])
  const [hubs, setHubs] = useState([])
  const [form, setForm] = useState(initialForm)
  const [formVisible, setFormVisible] = useState(false)
  const [search, setSearch] = useState("")
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState("")

  const fetchData = async () => {
    try {
      setLoading(true)
      setError("")
      const [transactionResponse, productResponse, hubResponse] =
        await Promise.all([
          api.get("/stock-transactions"),
          api.get("/products"),
          api.get("/hubs"),
        ])

      setTransactions(transactionResponse.data?.data || [])
      setProducts(productResponse.data?.data || [])
      setHubs(hubResponse.data?.data || [])
    } catch (fetchError) {
      setError(
        fetchError.response?.data?.message ||
          "Gagal memuat data transaksi stok.",
      )
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchData()
  }, [])

  const filteredTransactions = useMemo(() => {
    const keyword = search.toLowerCase().trim()

    if (!keyword) {
      return transactions
    }

    return transactions.filter((item) =>
      [
        item.product?.code,
        item.product?.name,
        item.hub?.name,
        item.type,
        item.notes,
        item.creator?.name,
      ]
        .join(" ")
        .toLowerCase()
        .includes(keyword),
    )
  }, [search, transactions])

  const handleSubmit = async (event) => {
    event.preventDefault()

    if (!canCreate) {
      return
    }

    try {
      setSaving(true)
      setError("")
      await api.post("/stock-transactions", {
        product_id: form.product_id,
        hub_id: form.hub_id,
        type: form.type,
        quantity: Number(form.quantity),
        notes: form.notes || null,
      })

      setForm(initialForm)
      setFormVisible(false)
      await fetchData()
    } catch (saveError) {
      const validationErrors = saveError.response?.data?.errors
      const firstValidationError = validationErrors
        ? Object.values(validationErrors).flat()[0]
        : null

      setError(
        firstValidationError ||
          saveError.response?.data?.message ||
          "Gagal menyimpan transaksi stok.",
      )
    } finally {
      setSaving(false)
    }
  }

  return (
    <AppLayout>
      <section className="page-header">
        <div>
          <p className="eyebrow">Stok Barang</p>
          <h1 className="page-title">Transaksi Stok</h1>
          <p className="page-description">
            Catat stok masuk, keluar, dan adjustment untuk memperbarui inventaris.
          </p>
        </div>
        {canCreate ? (
          <NeumorphicButton
            variant="primary"
            onClick={() => setFormVisible((visible) => !visible)}
          >
            <Plus size={18} />
            Tambah Transaksi
          </NeumorphicButton>
        ) : null}
      </section>

      <section className="master-grid">
        {formVisible ? (
          <NeumorphicCard className="master-form-card">
            <h2 className="neo-card-title">Tambah Transaksi</h2>
            <form className="master-form" onSubmit={handleSubmit}>
              <div className="field">
                <label htmlFor="product_id">Produk</label>
                <select
                  id="product_id"
                  className="neo-input"
                  value={form.product_id}
                  onChange={(event) =>
                    setForm((current) => ({
                      ...current,
                      product_id: event.target.value,
                    }))
                  }
                  required
                >
                  <option value="">Pilih produk</option>
                  {products.map((product) => (
                    <option key={product.id} value={product.id}>
                      {product.code} - {product.name}
                    </option>
                  ))}
                </select>
              </div>

              <div className="field">
                <label htmlFor="hub_id">Hub</label>
                <select
                  id="hub_id"
                  className="neo-input"
                  value={form.hub_id}
                  onChange={(event) =>
                    setForm((current) => ({
                      ...current,
                      hub_id: event.target.value,
                    }))
                  }
                  required
                >
                  <option value="">Pilih hub</option>
                  {hubs.map((hub) => (
                    <option key={hub.id} value={hub.id}>
                      {hub.name}
                    </option>
                  ))}
                </select>
              </div>

              <div className="field">
                <label htmlFor="type">Tipe</label>
                <select
                  id="type"
                  className="neo-input"
                  value={form.type}
                  onChange={(event) =>
                    setForm((current) => ({
                      ...current,
                      type: event.target.value,
                    }))
                  }
                >
                  {transactionTypes.map((type) => (
                    <option key={type.value} value={type.value}>
                      {type.label}
                    </option>
                  ))}
                </select>
              </div>

              <NeumorphicInput
                id="quantity"
                label="Quantity"
                min={form.type === "adjustment" ? 0 : 1}
                type="number"
                value={form.quantity}
                onChange={(event) =>
                  setForm((current) => ({
                    ...current,
                    quantity: event.target.value,
                  }))
                }
                required
              />

              <div className="field">
                <label htmlFor="notes">Catatan</label>
                <textarea
                  id="notes"
                  className="neo-input textarea-input"
                  value={form.notes}
                  onChange={(event) =>
                    setForm((current) => ({
                      ...current,
                      notes: event.target.value,
                    }))
                  }
                  placeholder="Catatan transaksi"
                />
              </div>

              <div className="form-actions">
                <NeumorphicButton
                  type="button"
                  onClick={() => setFormVisible(false)}
                >
                  Batal
                </NeumorphicButton>
                <NeumorphicButton
                  type="submit"
                  variant="primary"
                  disabled={saving}
                >
                  {saving ? "Menyimpan..." : "Simpan"}
                </NeumorphicButton>
              </div>
            </form>
          </NeumorphicCard>
        ) : null}

        <NeumorphicCard>
          <div className="master-toolbar">
            <div className="search-field">
              <Search size={18} />
              <input
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Cari transaksi stok..."
                type="search"
              />
            </div>
            {!canCreate ? (
              <span className="readonly-note">Mode lihat saja</span>
            ) : null}
          </div>

          {error ? <p className="form-error">{error}</p> : null}

          {loading ? (
            <div className="empty-state">Memuat data...</div>
          ) : filteredTransactions.length === 0 ? (
            <div className="empty-state">Belum ada transaksi stok.</div>
          ) : (
            <div className="table-wrap">
              <table className="stock-table master-table">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Hub</th>
                    <th>Tipe</th>
                    <th>Qty</th>
                    <th>Stok Sebelum</th>
                    <th>Stok Setelah</th>
                    <th>Catatan</th>
                    <th>Dibuat Oleh</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredTransactions.map((item) => (
                    <tr key={item.id}>
                      <td>{new Date(item.created_at).toLocaleString("id-ID")}</td>
                      <td>{item.product?.name || "-"}</td>
                      <td>{item.hub?.name || "-"}</td>
                      <td>{typeLabel(item.type)}</td>
                      <td>{item.quantity}</td>
                      <td>{item.stock_before}</td>
                      <td>{item.stock_after}</td>
                      <td>{item.notes || "-"}</td>
                      <td>{item.creator?.name || "-"}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </NeumorphicCard>
      </section>
    </AppLayout>
  )
}

export default StockTransactions
