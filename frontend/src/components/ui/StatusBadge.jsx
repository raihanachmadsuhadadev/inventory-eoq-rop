const statusClass = {
  Aman: "status-safe",
  "Perlu Pesan": "status-order",
  Kritis: "status-critical",
}

function StatusBadge({ status }) {
  return (
    <span className={`status-badge ${statusClass[status] || "status-safe"}`}>
      {status}
    </span>
  )
}

export default StatusBadge
