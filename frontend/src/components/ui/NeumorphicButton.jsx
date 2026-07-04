function NeumorphicButton({
  children,
  className = "",
  variant = "default",
  type = "button",
  ...props
}) {
  const variantClass = variant === "primary" ? "primary" : ""

  return (
    <button
      type={type}
      className={`neo-button ${variantClass} ${className}`.trim()}
      {...props}
    >
      {children}
    </button>
  )
}

export default NeumorphicButton
