import { ArrowRight, BarChart3, ShieldCheck, Warehouse } from "lucide-react"
import NeumorphicButton from "../components/ui/NeumorphicButton"
import NeumorphicCard from "../components/ui/NeumorphicCard"
import NeumorphicInput from "../components/ui/NeumorphicInput"

function Login() {
  return (
    <main className="login-page">
      <div className="login-grid">
        <NeumorphicCard className="login-panel">
          <div>
            <p className="eyebrow">Inventory Platform</p>
            <h1 className="page-title">Inventory Control System</h1>
            <p className="page-description">
              EOQ & ROP Based Inventory Management
            </p>

            <form className="login-form">
              <NeumorphicInput
                id="email"
                label="Email"
                placeholder="admin@example.com"
                type="email"
              />
              <NeumorphicInput
                id="password"
                label="Password"
                placeholder="Masukkan password"
                type="password"
              />
              <div className="login-actions">
                <NeumorphicButton type="button" variant="primary">
                  Masuk
                  <ArrowRight size={18} />
                </NeumorphicButton>
              </div>
            </form>
          </div>
        </NeumorphicCard>

        <NeumorphicCard>
          <p className="eyebrow">Ringkasan Sistem</p>
          <h2 className="neo-card-title">Kontrol inventaris lebih terukur</h2>
          <p className="neo-card-muted">
            Fondasi UI ini disiapkan untuk pemantauan stok, rekomendasi
            pemesanan, dan analisis persediaan berbasis EOQ serta ROP.
          </p>

          <div className="info-list">
            <div className="info-item">
              <div className="info-icon">
                <Warehouse size={20} />
              </div>
              <div>
                <h3 className="neo-card-title">Monitoring Stok</h3>
                <p className="neo-card-muted">
                  Pantau produk aman, perlu pesan, dan kritis dari satu
                  dashboard.
                </p>
              </div>
            </div>
            <div className="info-item">
              <div className="info-icon">
                <BarChart3 size={20} />
              </div>
              <div>
                <h3 className="neo-card-title">EOQ & ROP</h3>
                <p className="neo-card-muted">
                  Siap dikembangkan untuk perhitungan kuantitas optimal dan
                  titik pemesanan ulang.
                </p>
              </div>
            </div>
            <div className="info-item">
              <div className="info-icon">
                <ShieldCheck size={20} />
              </div>
              <div>
                <h3 className="neo-card-title">Operasional Rapi</h3>
                <p className="neo-card-muted">
                  Struktur komponen konsisten untuk halaman inventaris
                  berikutnya.
                </p>
              </div>
            </div>
          </div>
        </NeumorphicCard>
      </div>
    </main>
  )
}

export default Login
