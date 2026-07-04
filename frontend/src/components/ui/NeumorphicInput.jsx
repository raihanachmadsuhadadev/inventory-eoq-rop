function NeumorphicInput({ id, label, className = "", ...props }) {
  return (
    <div className="field">
      {label ? <label htmlFor={id}>{label}</label> : null}
      <input id={id} className={`neo-input ${className}`.trim()} {...props} />
    </div>
  )
}

export default NeumorphicInput
