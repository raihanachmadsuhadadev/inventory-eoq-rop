function NeumorphicCard({ as: Component = "div", className = "", children, ...props }) {
  return (
    <Component className={`neo-card ${className}`.trim()} {...props}>
      {children}
    </Component>
  )
}

export default NeumorphicCard
