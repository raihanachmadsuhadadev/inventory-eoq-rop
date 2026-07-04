import {
  AlertTriangle,
  ArrowUpRight,
  Building2,
  Package,
  ShoppingCart,
} from "lucide-react"
import AppLayout from "../layouts/AppLayout"
import NeumorphicCard from "../components/ui/NeumorphicCard"
import NeumorphicButton from "../components/ui/NeumorphicButton"
import StatusBadge from "../components/ui/StatusBadge"

const summaries = [
  { label: "Total Produk", value: "128", icon: Package },
  { label: "Total Supplier", value: "24", icon: Building2 },
  { label: "Stok Kritis", value: "9", icon: AlertTriangle },
  { label: "Perlu Pemesanan", value: "16", icon: ShoppingCart },
]

const criticalStocks = [
  {
    product: "Kopi Arabica 1kg",
    category: "Bahan Baku",
    stock: 12,
    rop: 20,
    supplier: "Nusa Agro",
    status: "Kritis",
  },
  {
    product: "Cup Paper 12oz",
    category: "Kemasan",
    stock: 85,
    rop: 120,
    supplier: "Prima Pack",
    status: "Perlu Pesan",
  },
  {
    product: "Gula Cair 5L",
    category: "Bahan Baku",
    stock: 34,
    rop: 30,
    supplier: "Sari Manis",
    status: "Aman",
  },
  {
    product: "Susu UHT 1L",
    category: "Bahan Baku",
    stock: 18,
    rop: 25,
    supplier: "Dairy Fresh",
    status: "Kritis",
  },
]

const recommendations = [
  {
    product: "Kopi Arabica 1kg",
    quantity: "48 unit",
    note: "Prioritas tinggi karena stok berada di bawah ROP.",
    meta: ["EOQ 48", "Lead time 5 hari"],
  },
  {
    product: "Cup Paper 12oz",
    quantity: "220 unit",
    note: "Pemesanan disarankan minggu ini untuk menjaga buffer stok.",
    meta: ["EOQ 220", "ROP 120"],
  },
]

function Dashboard() {
  return (
    <AppLayout>
      <section className="page-header">
        <div>
          <p className="eyebrow">Dashboard</p>
          <h1 className="page-title">Ringkasan Inventaris</h1>
          <p className="page-description">
            Pantau kondisi stok, supplier, dan rekomendasi pemesanan awal dalam
            tampilan yang ringan dibaca.
          </p>
        </div>
        <NeumorphicButton>
          Lihat Laporan
          <ArrowUpRight size={18} />
        </NeumorphicButton>
      </section>

      <section className="summary-grid" aria-label="Ringkasan inventaris">
        {summaries.map((item) => {
          const Icon = item.icon

          return (
            <NeumorphicCard key={item.label} className="summary-card compact">
              <div className="summary-top">
                <p className="summary-label">{item.label}</p>
                <div className="summary-icon">
                  <Icon size={21} />
                </div>
              </div>
              <p className="summary-value">{item.value}</p>
            </NeumorphicCard>
          )
        })}
      </section>

      <section className="content-grid">
        <NeumorphicCard>
          <div className="page-header">
            <div>
              <h2 className="neo-card-title">Stok Kritis</h2>
              <p className="neo-card-muted">
                Data dummy untuk fondasi tampilan tabel persediaan.
              </p>
            </div>
          </div>

          <div className="table-wrap">
            <table className="stock-table">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Kategori</th>
                  <th>Stok</th>
                  <th>ROP</th>
                  <th>Supplier</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {criticalStocks.map((item) => (
                  <tr key={item.product}>
                    <td>{item.product}</td>
                    <td>{item.category}</td>
                    <td>{item.stock}</td>
                    <td>{item.rop}</td>
                    <td>{item.supplier}</td>
                    <td>
                      <StatusBadge status={item.status} />
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </NeumorphicCard>

        <NeumorphicCard>
          <h2 className="neo-card-title">Rekomendasi Pemesanan</h2>
          <p className="neo-card-muted">
            Prioritas pemesanan dummy berdasarkan stok saat ini dan nilai ROP.
          </p>

          <div className="recommendation-list">
            {recommendations.map((item) => (
              <article key={item.product} className="recommendation-item">
                <h3>{item.product}</h3>
                <p className="neo-card-muted">{item.note}</p>
                <div className="recommendation-meta">
                  <span className="meta-pill">Pesan {item.quantity}</span>
                  {item.meta.map((meta) => (
                    <span key={meta} className="meta-pill">
                      {meta}
                    </span>
                  ))}
                </div>
              </article>
            ))}
          </div>
        </NeumorphicCard>
      </section>
    </AppLayout>
  )
}

export default Dashboard
