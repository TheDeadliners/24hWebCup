import React from "react"
import ReactDOM from "react-dom/client"

import "./styles/app.css"

const root = ReactDOM.createRoot(document.getElementById("app-root"))

root.render(
  <React.StrictMode>
    <div>TEST</div>
  </React.StrictMode>
)

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉")
